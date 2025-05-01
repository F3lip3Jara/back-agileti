-- Active: 1742763600964@@localhost@3306@agiletic_app
CREATE OR REPLACE VIEW usuarios( token, name, email, imgName, rolId, rolDes, emploNom, emploApe, emploFecNac, gerId, gerDes , activado, reinicio ,created_at , id) AS( SELECT a.token, a.name, a.email, c.emploAvatar, a.rolId, b.RolDes, c.emploNom, c.emploApe, c.emploFecNac, c.gerId, d.gerDes , a.activado , a.reinicio ,a.created_at , a.id FROM users a JOIN segu_roles b ON a.rolId = b.rolId LEFT JOIN parm_empleados c ON a.id = c.id LEFT JOIN parm_gerencia d ON c.gerId = d.gerId);
CREATE OR REPLACE VIEW tblusuarios( empId, empresa,rolId,id, name, email, rolDes, emploNom, emploApe, emploFecNac, gerId,gerDes, activado, reinicio, created_at ) AS( SELECT  a.empId , e.`empDes`, b.rolID, a.id, a.name, a.email, b.RolDes, c.emploNom, c.emploApe, c.emploFecNac, d.gerId,d.gerDes, CASE WHEN a.activado = 'A' THEN "ACTIVADO" WHEN a.activado = 'D' THEN "DESACTIVADO" END AS 'activado', CASE WHEN a.reinicio = 'S' THEN "SI" WHEN a.reinicio = 'N' THEN "NO" END 'reinicio', a.created_at  FROM users a JOIN segu_roles b ON a.rolId = b.rolId LEFT JOIN parm_empleados c ON a.id = c.id LEFT JOIN parm_gerencia d ON c.gerId = d.gerId  JOIN parm_empresa  e on e.`empId` = a.`empId`  ORDER BY a.created_at DESC);
CREATE OR REPLACE VIEW regiones (regId, regDes, regCod, empId, paiId, created_at, updated_at , paiDes , paiCod) as ( SELECT a.regId, a.regDes, a.regCod, a.empId, a.paiId, a.created_at, a.updated_at , paiDes , paiCod from parm_region a join parm_pais b on a.paiId = b.paiId );       
CREATE OR REPLACE VIEW comunas (comId, comDes, comCod, empId, paiId, regId,ciuId , ciuDes , created_at, updated_at , paiCod , paiDes , regCod , regDes) AS (SELECT comId, comDes, comCod, a.empId, a.paiId, a.regId,  a.ciuId , ciuDes , a.created_at, a.updated_at , paicod , paiDes , regCod , regDes  from parm_comuna a JOIN parm_region b on a.regId = b.regId join parm_pais c on a.paiId = c.paiId join parm_ciudad d on a.ciuId = d.ciuId);
CREATE OR REPLACE VIEW ciudades ( empId, paiId, regId, created_at, updated_at , paiCod , paiDes , regCod , regDes , ciuId , ciuDes , ciuCod) AS (SELECT a.empId, a.paiId, a.regId, a.created_at, a.updated_at , paicod , paiDes , regCod , regDes , a.ciuId , ciuDes , ciuCod from parm_ciudad a JOIN parm_region b on a.regId = b.regId JOIN parm_pais c on a.paiId = c.paiId ) ;
CREATE OR REPLACE VIEW proveedores( id,rut,nombre,nombre_fantasia,giro,pais , pais_id ,region, region_id,comuna, comuna_id,ciudad,ciudad_id, direccion,numero,telefono ,es_cliente, es_proveedor,mail , activado , place , lat , lng)AS ( SELECT prvId ,prvRut, prvNom, prvNom2, prvGiro,b.paiDes , b.`paiId` , c.regDes , c.`regId`,d.comDes, d.`comId` ,e.ciuDes , e.`ciuId`, prvDir, prvNum , prvTel, prvCli, prvPrv, prvMail , prvAct, prvPlace, prvLat, prvLong from parm_proveedor a join parm_pais b on a.paiId = b.paiId join parm_region c on a.regId = c.regId join parm_comuna d on a.comId = d.comId join parm_ciudad e on a.ciuId = e.ciuId );
CREATE OR REPLACE VIEW proveedores_dir(id,rut ,pais,region,comuna,ciudad,direccion,numero) AS ( SELECT a.prvdId , f.prvRut,b.paiDes , c.regDes,d.comDes,e.ciuDes, prvdDir, prvdNum From parm_prv_suc a join parm_pais b on a.paiId = b.paiId join parm_region c on a.regId = c.regId join parm_comuna d on a.comId = d.comId join parm_ciudad e on a.ciuId = e.ciuId join parm_proveedor f on a.prvId = f.prvID);
CREATE OR REPLACE VIEW productos( id, cod_pareo, descripcion, observaciones, cod_rapido, cod_barra, tipo,grupo , sub_grupo , color , moneda ,costo, neto, bruto, medida , peso, minimo, inventariable, created_at, updated_at) As ( SELECT a.prdId, prdCod, prdDes, prdObs, prdRap, prdEan, prdTip,c.grpDes , d.grpsDes , e.colDes , b.monCod , prdCost, prdNet, prdBrut, f.unDes , prdPes, prdMin,prdInv , a.created_at, a.updated_at from parm_producto a join parm_moneda b on a.monId = b.monId join parm_grupo c on a.grpId = c.grpId join parm_sub_grupo d on a.grpsId = d.grpsId join parm_color e on a.colId = e.colId join parm_un_medida f on a.unId = f.unId);
CREATE OR REPLACE VIEW menu_roles AS ( SELECT a.empId, a.molId, e.rolId, b.molDes,b.molIcon,c.optId,c.optDes,c.optLink FROM segu_emp_mol_opt a  JOIN segu_modulo  b ON a.molId = b.molId JOIN segu_opciones c ON a.optId = c.optId  JOIN segu_emp_mol_rol e ON a.molId =e.molId and a.empId = e.empId );
CREATE OR REPLACE VIEW menu_roles_sub AS( SELECT a.empId, a.molId, b.rolId, d.molsDes, a.optId, c.optDes, c.optLink , a.`molsId` FROM segu_emp_mol_submol_opt a JOIN segu_emp_mol_rol b ON a.empId = b.empId AND a.molId = b.molId JOIN segu_opciones c ON a.optId = c.optId JOIN segu_sub_modulo d ON a.molsId = d.molsId );
CREATE OR REPLACE VIEW productos( id, cod_pareo, descripcion, observaciones, cod_rapido, cod_barra, tipo, codgrupo, grupo , codsubgrupo, sub_grupo , codcolor, color , moneda ,costo, neto, bruto, codmedida, medida , peso, alto , ancho , largo , volumen, minimo, inventariable, id_ext , url , codtalla , talla ,  created_at, updated_at) As ( SELECT a.prdId, prdCod, prdDes, prdObs, prdRap, prdEan, prdTip, c.grpCod ,c.grpDes , d.grpsCod, d.grpsDes , e.`colCod` , e.colDes , b.monCod , prdCost, prdNet, prdBrut, f.unCod,  f.unDes ,a.prdPes, a.prdAlto , a.`prdAncho` , a.`prdLargo` , a.`prdVolumen`,  prdMin,prdInv , `prdIdExt` , `prdUrl` , g.`tallaCod` , g.`tallaDes`  , a.created_at, a.updated_at from parm_producto a join parm_moneda b on a.monId = b.monId join parm_grupo c on a.grpId = c.grpId join parm_sub_grupo d on a.grpsId = d.grpsId join parm_color e on a.colId = e.colId join parm_un_medida f on a.unId = f.unId  join parm_talla g on a.tallaId  = g.tallaId );

CREATE OR REPLACE VIEW orden_produccion AS
    (SELECT 
        a.`empId`,
        `a`.`orpId` AS `id`,
        `a`.`orpUsrG` AS `usuario`,
        `a`.`orpNumOc` AS `orden_compra`,
        `a`.`orpNumRea` AS `orden_produccion`,
        b.`prvNom` AS proveedor,
        b.`prvTel` AS prv_telefono,
        b.prvId AS proveedor_id,
        `b`.`prvRut` AS `rut`,
        `a`.`orpFech` AS `fecha`,
        CASE
            WHEN `a`.`orpEst` = 1 THEN 'PENDIENTE'
            WHEN `a`.`orpEst` = 2 THEN 'PROCESANDO'
            WHEN `a`.`orpEst` = 3 THEN 'APROBADA'
            WHEN `a`.`orpEst` = 4 THEN 'RECHAZADA'
        END AS `estado_ord`,
        CASE
            WHEN `a`.`orpEstPrc` = 1 THEN 'PENDIENTE'
            WHEN `a`.`orpEstPrc` = 2 THEN 'PARCIAL'
            WHEN `a`.`orpEstPrc` = 3 THEN 'COMPLETA'
            WHEN `a`.`orpEstPrc` = 4 THEN 'UBICADA'
        END AS `estado_pro`,
        `a`.`orpObs` AS `observaciones`,
        (SELECT 
                COUNT(0)
            FROM
                `prod_orden_det` `c`
            WHERE
                `c`.`orpId` = `a`.`orpId`) AS `prd_total`,
          (SELECT 
                sum(`c`.`orpdCant`)
            FROM
                `prod_orden_det` `c`
            WHERE
                `c`.`orpId` = `a`.`orpId`) AS `prd_total_lineas`,
        f.clasTip AS tipo,
        f.clasTipCod AS tipo_cod,
        f.clasTipDes AS tipo_des,
        f.clasTipId AS tipo_id,
        d.almId AS almacen_id,
        d.almDes AS almacen_destino,
        e.centroId AS centro_id,       
        e.cenDes AS centro_destino,
        a.orpHdrCustShortText5 as 'latitud',
        a.orpHdrCustShortText6 as 'longitud',
        a.orpHdrCustShortText9 as 'fech_promesa',        
        `a`.`created_at` AS `created_at`,
        `a`.`updated_at` AS `updated_at`
    FROM
        `prod_orden` `a`
            JOIN
        `parm_proveedor` `b` ON `a`.`prvId` = `b`.`prvId`
            JOIN
        sd_tip_clase f ON a.orpHdrCustShortText3 = f.clasTipId
            JOIN
        sd_centro_alm `d` ON a.orpHdrCustShortText1 = d.centroId
            AND a.orpHdrCustShortText2 = d.almId
            JOIN 
		sd_centro e on a.orpHdrCustShortText1 = e.centroId
        );