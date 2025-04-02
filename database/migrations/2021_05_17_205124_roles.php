<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Roles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('segu_roles', function (Blueprint $table) {
            $table->bigIncrements('rolId');
            $table->string('rolDes');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('rolId')->references('rolId')->on('segu_roles'); 
        });

        Schema::create('segu_log_sys', function (Blueprint $table) {
            $table->bigIncrements('logId');
            $table->bigInteger('empId')->unsigned();           
            $table->bigInteger('etaId')->unsigned();         
            $table->bigInteger('etaDesId')->unsigned();
            $table->string('lgDes');
            $table->string('lgId');
            $table->string('lgName');
            $table->integer('lgTip');
            $table->string('lgDes1')->nullable();
            $table->string('lgDes2')->nullable();
            $table->string('lgDes3')->nullable();
            $table->string('lgDes4')->nullable();
            $table->timestamps();
        });

        
         //Opciones
         Schema::create('segu_opciones', function (Blueprint $table) {
            $table->bigIncrements('optId');           
            $table->string('optDes');
            $table->string('optLink');           
            $table->timestamps();
        });

        
        Schema::create('segu_emp_opt', function (Blueprint $table) {
            $table->bigIncrements('seoId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('optId')->unsigned();
            $table->foreign('optId')->references('optId')->on('segu_opciones');             
            $table->timestamps();
        });

       /*    //Sub opciones
           Schema::create('segu_subopciones', function (Blueprint $table) {
            $table->bigIncrements('optSubId');        
            $table->bigInteger('optId')->unsigned();
            $table->foreign('optId')->references('optId')->on('segu_opciones');
            $table->string('optSDes');
            $table->string('optSLink');
            $table->timestamps();
           });*/


        Schema::create('segu_acciones', function (Blueprint $table) {
            $table->bigIncrements('accId');           
            $table->string('accDes');
            $table->string('accUrl');
            $table->char('accetaDes')->nullable();
            $table->char('acceVig', 1)->nullable();  
            $table->bigInteger('optId')->unsigned();
            $table->string('accType')->nullable();
            $table->string('accMessage')->nullable();
            $table->foreign('optId')->references('optId')->on('segu_opciones');
            $table->timestamps();
        });

        Schema::create('segu_view', function (Blueprint $table) {
            $table->bigIncrements('vieId');           
            $table->string('vieDes');
            $table->string('vieClass');
            $table->string('vieInde');            
            $table->bigInteger('accId')->unsigned();
            $table->foreign('accId')->references('accId')->on('segu_acciones');
            $table->timestamps();
        });

        Schema::create('segu_seccion', function (Blueprint $table) {
            $table->bigIncrements('secId');           
            $table->string('secDes');
            $table->string('secInde');
            $table->string('seClass');  
            $table->bigInteger('accId')->unsigned();
            $table->foreign('accId')->references('accId')->on('segu_acciones');          
            $table->bigInteger('vieId')->unsigned();
            $table->foreign('vieId')->references('vieId')->on('segu_view');
            $table->timestamps();
        });

        Schema::create('segu_campos', function (Blueprint $table) {
            $table->bigIncrements('camId');           
            $table->string('camNom');
            $table->string('camClass');
            $table->string('seClass');  
            $table->bigInteger('accId')->unsigned();
            $table->foreign('accId')->references('accId')->on('segu_acciones');          
            $table->bigInteger('vieId')->unsigned();
            $table->foreign('vieId')->references('vieId')->on('segu_view');
            $table->timestamps();
        });


      /*  Schema::create('segu_ctl_acceso_rol', function (Blueprint $table) {        
            $table->bigInteger('secId')->unsigned();
            $table->foreign('secId')->references('secId')->on('segu_seccion');     
            $table->bigInteger('rolId')->unsigned();
            $table->foreign('rolId')->references('rolId')->on('segu_rol');           
            $table->bigInteger('accId')->unsigned();
            $table->foreign('accId')->references('accId')->on('segu_acciones');          
            $table->bigInteger('vieId')->unsigned();
            $table->foreign('vieId')->references('vieId')->on('segu_view');
            $table->bigInteger('camId')->unsigned();            
            $table->foreign('camId')->references('camId')->on('segu_campos');   
          
         
            $table->string('acceAut');  
            $table->timestamps();
        });*/

        //Modulo
        

        Schema::create('segu_modulo', function (Blueprint $table) {
            $table->bigIncrements('molId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->string('molDes');
            $table->string('molIcon');
            $table->char('molsId')->nullable();
            $table->timestamps();
        });

        Schema::create('segu_sub_modulo', function (Blueprint $table) {
            $table->bigIncrements('molsId');        
            $table->bigInteger('molId')->unsigned();
            $table->foreign('molId')->references('molId')->on('segu_modulo');
            $table->string('molsDes');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->timestamps();
        });

          //Menu
          Schema::create('segu_emp_mol_submol_opt', function (Blueprint $table) {           
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('molId')->unsigned();
            $table->foreign('molId')->references('molId')->on('segu_modulo');
            $table->bigInteger('molsId')->unsigned();
            $table->foreign('molsId')->references('molsId')->on('segu_sub_modulo');            
            $table->bigInteger('optId')->unsigned();
            $table->foreign('optId')->references('optId')->on('segu_opciones'); 
            $table->timestamps();
        });
        
        //Menu
        Schema::create('segu_emp_mol_opt', function (Blueprint $table) {           
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('molId')->unsigned();
            $table->foreign('molId')->references('molId')->on('segu_modulo');
            $table->bigInteger('optId')->unsigned();
            $table->foreign('optId')->references('optId')->on('segu_opciones'); 
            $table->timestamps();
        });

        Schema::create('segu_emp_mol_rol', function (Blueprint $table) {           
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('molId')->unsigned();
            $table->foreign('molId')->references('molId')->on('segu_modulo');
            $table->bigInteger('rolId')->unsigned();
            $table->foreign('rolId')->references('rolId')->on('segu_roles'); 
            $table->timestamps();
        });


      

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
