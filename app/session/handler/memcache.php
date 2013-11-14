<?php
/**
 * Memcache Save Handler
 */
class App_Session_Handler_Memcache extends SolarLite_Session_Handler
{
    public $_cache;
   /**
     * 
     * Sets the session save handler.
     * 
     * This doesn't actually do anything, because we're using the native PHP
     * handler, not the methods in this class.
     * 
     * @return void
     * 
     */
    protected function _setSaveHandler()
    {
        parent::_setSaveHandler();
        if (!$this->_cache) {
            $this->_cache = new SolarLite_Cache_Memcache(3600);
        }
    }
    
    /**
     * 
     * Opens the session handler.
     * 
     * Provided only to override abstract method, never actually called.
     * 
     * @return bool
     * 
     */
    public function open()
    {
        return true;
    }
    
    /**
     * 
     * Closes session handler.
     * 
     * Provided only to override abstract method, never actually called.
     * 
     * @return bool
     * 
     */
    public function close()
    {
        return true;
    }
    
    /**
     * 
     * Reads session data.
     * 
     * Provided only to override abstract method, never actually called.
     * 
     * @param string $id The session ID.
     * 
     * @return string The serialized session data.
     * 
     */
    public function read($id)
    {
        $key = 'PHPSESSIONS_' . $id;
        return $this->_cache->fetch($key);
    }
    
    /**
     * 
     * Writes session data.
     * 
     * Provided only to override abstract method, never actually called.
     * 
     * @param string $id The session ID.
     * 
     * @param string $data The serialized session data.
     * 
     * @return bool
     * 
     */
    public function write($id, $data)
    {
        $key = 'PHPSESSIONS_' . $id;
        if ($this->_cache->fetch($key) === false) {
            $this->_cache->add($key, $data);
        } else {
            $this->_cache->replace($key, $data);
        }
        return true;
    }
    
    /**
     * 
     * Destroys session data.
     * 
     * Provided only to override abstract method, never actually called.
     * 
     * @param string $id The session ID.
     * 
     * @return bool
     * 
     */
    public function destroy($id)
    {
        $key = 'PHPSESSIONS_' . $id;
        $this->_cache->delete($key);
        return true;
    }
    
    /**
     * 
     * Removes old session data (garbage collection).
     * 
     * Provided only to override abstract method, never actually called.
     * 
     * @param int $lifetime Removes session data not updated since this many
     * seconds ago.  E.g., a lifetime of 86400 removes all session data not
     * updated in the past 24 hours.
     * 
     * @return bool
     * 
     */
    public function gc($lifetime)
    {
        return true;
    }
}
