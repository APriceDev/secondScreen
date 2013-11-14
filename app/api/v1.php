<?php
 /**
 * Sobella API V1
 *
 * @category API
 * @package  Sobella
 * @license  Proprietary/Closed Source
 */ 
class App_Api_V1 {
    
    protected $_model;
    
    /**
     * __construct function.
     * 
     * @return void
     * @access public
     */
    public function __construct()
    {

    }
    
    /**
     * setModel function.
     * 
     * @return void
     * @access public
     */
    public function setModel()
    {
        if (!$this->_model) {
            $this->_model = new SolarLite_Catalog();
        }
    }
    
    public static $uid;
    
    public function getUserID()
    {
        $this->setModel();
        if (!self::$uid) {
            self::$uid = (int) $this->_model->users->fetchUserIdBySecretApiKey($_SERVER['PHP_AUTH_USER']);
        }
        
        return self::$uid;
    }
    
    /**
     * call function.
     * 
     * @param mixed $request_method Request Method
     * @param mixed $resource       Resource
     * @param mixed $id             ID
     * @param array $args           Args
     *
     * @return void
     * @access public
     */
    public function call($request_method, $request_params = array(), $args = array())
    {
        $ids = array();
        $r = array();
        foreach ($request_params as $v) {
            if (App_Util::isInt($v)) {
                $ids[] = $v;
            } else {
                $r[] = $v;
            }
        }
        
        $resource = implode('_', $r);
        
        if (empty($ids)) {
            $id = null;
        } elseif (count($ids) == 1) {
            $id = $ids[0];
        } else {
            $id = $ids;
        }

        $method = strtolower(str_replace('-', '_', $resource) . '_' . $request_method);
        if (method_exists($this, $method)) {
            try {
                // If we've come this far lets go ahead and provide easy
                // access to the models.
                $this->setModel();
                
                // Call method, all methods should return a 
                // response or throw an exception object.
                return $this->$method($id, $args);
            } catch (App_Api_V1_Exception $e) {
                throw new App_Api_V1_Exception($e->getMessage(), $e->status_code);
            }
        } else {
            throw new App_Api_V1_Exception('Invalid Resource/Request Method combination.', 400);
        }
    }
    
    public function detect_get()
    {
        // Check for missing required fields
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';
        if (!$query) {
            throw new App_Api_V1_Exception('Required parameter is missing: query', 400);
            return;
        }
        
        $result = @json_decode(file_get_contents(BASE_URL . ':8080/query?fp_code=' . $query),true);
        if ($result && isset($result['video_id']) && App_Util::isInt($result['video_id'])) {
            $video = $this->_model->videos->fetchVideoById($result['video_id']);
            $events = $this->_model->events->fetchByVideoIdExpanded($result['video_id']);
            return new App_Api_V1_Response(200, array('video' => $video, 'events' => $events, 'echonest_response' => $result));
        } else {
            throw new App_Api_V1_Exception('Video does not exist.', 400);
            return;
        }
    }
    
    public static function hasMissingFields($required, $args)
    {
        // Keys provided by user
        $keys = array_keys($args);
        // Which keys do we have from the required in the user provided keys
        $map = array_intersect($required, $keys);
        // Take the diff of the ones we have versus what we should.
        return array_diff($required, $map);
    }
}

 /**
 * API Expception
 *
 * @category API
 * @package  So Bella
 * @license  Proprietary/Closed Source
 */ 
class App_Api_V1_Exception extends Exception {
    /**
     * __construct function.
     * 
     * @param mixed $message     (default: null)
     * @param mixed $status_code (default: null)
     *
     * @return void
     * @access public
     */
    public function __construct($message = null, $status_code = null)
    {
        $this->errors = [];
        if (is_array($message)) {
            $this->errors = $message;
            $message = implode(', ', $message);
        } else {
            $this->errors[] = $message;
        }
        
        parent::__construct($message);
        $this->status_code = $status_code;
    }
}

 /**
 * API Response
 *
 * @category API
 * @package  So Bella
 * @license  Proprietary/Closed Source
 */ 
class App_Api_V1_Response {
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
    
    public $status_code = 200;
    public $results = array();
    
    /**
     * __construct function.
     * 
     * @param int   $status_code (default: 200)
     * @param array $results     (default: array())
     *
     * @return void
     * @access public
     */
    public function __construct($status_code = 200, $results = array())
    {
        $this->status_code = $status_code;
        $this->results = $results;
    }
}
