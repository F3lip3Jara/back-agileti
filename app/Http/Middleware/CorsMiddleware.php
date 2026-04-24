<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // El Origen debe ser el dominio específico si Allow-Credentials es true, no se permite '*'
        $origin = $request->header('Origin') ?: '*';
        
        // Manejar preflight requests TEMPRANO para evitar que llegue a las rutas o dispare errores 405
        if ($request->isMethod('OPTIONS')) {
            return response('', 200, [
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept, Origin, X-API-Key, X-App-Version, X-Platform, Cache-Control, X-File-Name, Pragma, Expires, If-Modified-Since, If-None-Match, ETag, Last-Modified, access-token',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age' => '86400',
            ]);
        }

        $response = $next($request);

        // Agregamos los headers a la respuesta real
        if (method_exists($response, 'header')) {
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept, Origin, X-API-Key, X-App-Version, X-Platform, Cache-Control, X-File-Name, Pragma, Expires, If-Modified-Since, If-None-Match, ETag, Last-Modified, access-token');
            $response->header('Access-Control-Allow-Credentials', 'true');
            $response->header('Access-Control-Max-Age', '86400');
        } elseif (property_exists($response, 'headers')) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept, Origin, X-API-Key, X-App-Version, X-Platform, Cache-Control, X-File-Name, Pragma, Expires, If-Modified-Since, If-None-Match, ETag, Last-Modified, access-token');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '86400');
        }

        return $response;
    }
} 