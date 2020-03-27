<?php

namespace Hunter\cors;

use Hunter\Http\Response;
use Hunter\Middleware\DelegateInterface;
use Hunter\Middleware\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Medz\Cors\Cors;

/**
 * Provides cors module permission auth.
 */
class CorsPermission extends Middleware {

  /**
   * Returns bool value of cors permission.
   *
   * @return bool
   */
   public function handle(ServerRequestInterface $request, DelegateInterface $next) {
     $allowed_origins = variable_get('allowed_origins', array());
     $response = $next->process($request);
     $config = [
        'allow-credentials' => false, // set "Access-Control-Allow-Credentials" 👉 string "false" or "true".
        'allow-headers'      => ['Origin', 'Content-Type', 'Cookie', 'Accept'], // ex: Content-Type, Accept, X-Requested-With
        'expose-headers'     => [],
        'origins'            => array_keys($allowed_origins), // ex: http://localhost
        'methods'            => ['GET','POST','PUT','DELETE'], // ex: GET, POST, PUT, PATCH, DELETE
        'max-age'            => 0,
     ];
     $cors = new Cors($config);
     $cors->setRequest('psr-7', $request);
     $cors->setResponse('psr-7', $response);
     $cors->handle();

     $response = $cors->getResponse();
     return $response;
  }

}
