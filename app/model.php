<?php
/**
 * App level Model Arch class to override
 * SolarLite Arch Model Class
 * 
 */
class App_Model extends SolarLite_Model
{
    public function isCached($key)
    {
        return $this->_cache->fetch($key);
    }
    
    /**
     * 
     * Use cached data if available, otherwise hit the DB and save in the cache.
     * 
     * @param string $stmt The SQL command to execute.  Doubles as the cache
     * entry key.
     * 
     * @param bool $reset When true, forces a DB hit and replaces any data
     * cached for the SQL command. Default false.
     * 
     * @param bool $long Use a long-term expiration in the cache. Default 
     * false.
     * 
     * @return mixed
     */
    public function useCache($stmt, $data = array(), $reset = false, $long = false)
    {
        if (!isset($stmt) || empty($stmt)) {
            return;
        }

        $cache_key = md5(serialize(array($stmt, $data)));
        $cached = $this->isCached($cache_key);
        if ($cached !== false && !$reset) {
            return $cached;
        } else {
            $prep = $this->query($stmt, $data);
            $rows = $prep->fetchAll(PDO::FETCH_ASSOC);
            if($cached === false || $reset === false) {
                if ($long){
                    $this->_cache->addLong($cache_key, $rows);
                } else {
                    $this->_cache->add($cache_key, $rows);
                }
            } else {
                if (!$long) {
                    $this->_cache->replace($cache_key, $rows);
                } else{
                    $this->_cache->replace($cache_key, $rows, 86400);
                }
            }
            return $rows;
        }
    }
    
    /**
     * This function is used as pseudo query caching cron job
     * we are creating a memcache key with a timestamp that 
     * will be compared against a check_interval duration
     * default is 12 hours...so every 12 hrs the cached query
     * will be recached despite if it is already cached or not
     * useful to use with queries that take longer to run and will be 
     * requested very a lot if uncached.
     */
    protected function _keepCached($sql, $check_interval = 43200, $long = true)
    {
        $reset = false;
        $memcache_key = 'keepcached_' . $sql;
        $state_key = 'keepcached_state_' . $sql;
        $timestamp = $this->isCached($memcache_key);
        $state = $this->isCached($state_key);
        
        if ($state === false) {
            $this->_cache->addLong($state_key, 'unlocked');
        }
        
        if ($timestamp === false) {
            $reset = true;
            $this->_cache->addLong($memcache_key, time());
        } elseif ((time() - (int) $timestamp) >= $check_interval) {
            $reset = true;      
        }
        
        if ($reset) {
            if ($state == 'locked') {
                sleep(2); // wait two seconds and rerun
                return $this->_keepCached($sql, $check_interval, $long);
            } else {
                $this->_cache->replace($state_key, 'locked');
                $this->_cache->replace($memcache_key, time(), 60*60*24*4);
                $results = $this->useCache($sql, true, $long);
                $this->_cache->replace($state_key, 'unlocked');
                return $results;
            }
        } else {
            return $this->useCache($sql, false, $long);
        }
    }
    
    public function getCacheObj()
    {
        return $this->_cache;
    }
    
    public function isInt($value) 
    {
        if (is_int($value)) {
            return true;
        }
        
        // otherwise, must be numeric, and must be same as when cast to int
        return is_numeric($value) && $value == (int) $value;
    }
    
    /**
     * Clear model cache
     */
    public function clearCache()
    {
        $this->_deleteCache();
    }
    
    public function fetchById($id)
    {
        if (!$this->isInt($id)) {
            return false;
        }
        
        $sql = "SELECT * FROM {$this->_table_name} WHERE id = :tid";
        $data = array('tid' => (int) $id);
        return $this->fetchOne($sql, $data);
    }
    
    public function fetchBySlug($slug)
    {
        $sql = "SELECT * FROM {$this->_table_name} WHERE slug = :s";
        $data = array('s' => $slug);
        return $this->fetchOne($sql, $data);
    }
    
    
    public function deleteById($id)
    {
        if (!$this->isInt($id)) {
            return false;
        }
        
        $this->delete(array('id = ?' => (int) $id));
    }
    
    /**
     * Changes a string to url friendly slug
     *
     * @param  string $str        the input string
     * @param  string $delim      [optional] the word delimiter; defaults to '-'
     * @param  int    $max_length [optional] the max slug length; defaults to 75
     * @return string the slug
     */
    public function makeSlug($str, $delim = '-', $max_length = 75, $incr = 0)
    {
        $slug = App_Util::makeSlug($str, $delim, $max_length);
        if ($incr) {
            $slugcheck = $slug . $incr;
        } else {
            $slugcheck = $slug;
        }
        // does this slug already exist?
        $res = $this->fetchBySlug($slugcheck);
        if (!empty($res)) {
            return $this->makeSlug($slug, $delim, $max_length, $incr + 1);
        }
        return $slugcheck;
    }
}
