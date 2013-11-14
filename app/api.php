<?php
/**
 * Sobella API Arch Class
 * Intended to be a gateway for incoming API requests to 
 * the appropraite API version methods.
 * 
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Api {
    
    public static $http_response_codes = array(
        'OK'           => 200,
        'FOUND'        => 302,
        'MOVED'        => 301,
        'BAD_REQUEST'  => 400,
        'UNAUTHORIZED' => 401,
        'FORBIDDEN'    => 403,
        'NOT_FOUND'    => 404,
        'SERVER_ERROR' => 500,
    );
    
    public static function newRequest($version, $request_method, $request_params, $args = array())
    {
        $class = 'App_Api_' . strtoupper($version);
        $api = new $class();
        return $api->call($request_method, $request_params, $args);
    }
    
    public static function formatError($e)
    {
        return array(
            'status_code' => $e->status_code,
            'message' => $e->getMessage(),
            'errors' => $e->errors,
        );
    }
}
