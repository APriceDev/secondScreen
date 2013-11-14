<?php
/**
 * Memcache Save Handler
 */
class App_Session extends SolarLite_Session
{
    public function __construct()
    {
        // only set up the handler if it doesn't exist yet.
        if (! self::$_handler) {
            $this->setHandler(new App_Session_Handler_Memcache());
        }
        
        // only set up the request if it doesn't exist yet.
        if (! self::$_request) {
            self::$_request = new SolarLite_Request();
        }
        
        // lazy-start any existing session
        $this->lazyStart();
    }
    
    public function setHandler($handler_object)
    {
        self::$_handler = $handler_object;
    }
}
