<?php
/**
 * Sobella API Controller (Dispatch)
 * 
 * FEATURES
 * - Permit accept and content-type headers for format
 * - Permit user-agent header to identify what made the request
 * - Authorization headers
 * - Uses proper http verbs , POST(create), GET(read), PUT(update), DELETE (delete)
 * - Versioning
 * - Consistency
 * - Error handling.
 * - Rate limiting
 * 
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Controller_Api extends SolarLite_Controller
{
    protected $_action_default = 'index';
    
    public static $formats = array(
        'application/json' => 'json',
        'application/xml'  => 'xml',
    );
    
    // resources that do not need api key authentication
    public $unauthed_resources = array(
        'detect' => array('GET'),
    );
    
    // Show API docs
    public function actionIndex()
    {
        if (IT_ENV == 'prod') {
            $this->_layout = null;
            $this->_view = null;
            $this->_response->content = 'So Bella Second Screen API';
        }
    }
    
    protected function unauthedResource($resource, $request_method)
    {
        if (isset($this->unauthed_resources[$resource]) && in_array($request_method, $this->unauthed_resources[$resource])) {
            return true;
        }
        return false;
    }
    
    public function actionV1()
    {
        $resource = isset($this->_info[0]) ? $this->_info[0] : null;
        // Ensure we are on https
        /*
        if (!IS_SSL) {
            $results = array(
                'status_code' => 403,
                'message' => "The So Bella Second Screen API is only accessible over HTTPS.",
            );
            $this->renderOutput($results);
            return;
        }
        */
        // Not Authorized?
        if (!$this->unauthedResource($resource, $this->_request->server('REQUEST_METHOD', 'GET'))) {
            if (!$this->_request->server('PHP_AUTH_USER') 
                || !($this->isValidUser($this->_request->server('PHP_AUTH_USER')) || $this->isInternalUser($this->_request->server('PHP_AUTH_USER')))) {
                $results = array(
                    'status_code' => 401,
                    'message' => "Invalid API Key provided: {$this->_request->server('PHP_AUTH_USER')}",
                );
                $this->renderOutput($results);
                return;    
            }
        }
        
        // Check Rate Limit For non-internal API users, leaky bucket style
        if (!in_array($resource, $this->unauthed_resources) && !$this->isInternalUser($this->_request->server('PHP_AUTH_USER'))) {
            $minute = 60;
            $minute_limit = 120; // users are limited to 100 requests/minute
            $key = 'ss_api_flood_control_' . $this->_request->server('PHP_AUTH_USER');
            $cache = new SolarLite_Cache_Memcache(3600);
            $ts = $cache->fetch($key);
            if ($ts === false) {
                $ts = 0;
            }
            $last_api_diff = time() - $ts;

            $minute_throttle_key = 'ss_api_flood_control_minute_' . $this->_request->server('PHP_AUTH_USER');
            $minute_throttle = $cache->fetch($minute_throttle_key);
            if ($minute_throttle === false) {
                $new_minute_throttle = 0;
            } else {
                $new_minute_throttle = $minute_throttle - $last_api_diff;
                $new_minute_throttle = $new_minute_throttle < 0 ? 0 : $new_minute_throttle;
                $new_minute_throttle +=	$minute / $minute_limit;
                $minute_hits_remaining = floor( ( $minute - $new_minute_throttle ) * $minute_limit / $minute  );
                // can output this value with the request if desired:
                $minute_hits_remaining = $minute_hits_remaining >= 0 ? $minute_hits_remaining : 0;
            }
            
            if ($new_minute_throttle > $minute) {
                $wait = ceil($new_minute_throttle - $minute);
                usleep( 250000 ); // sleep for 1/4 a sec
                $results = array(
                    'status_code' => 400,
                    'message' => "Rate limit exceeded. Please wait a while before making another request.",
                );
                $this->renderOutput($results);
                return;  
            }
            
            // Save new cache values;
            $cache->save($key, time());
            $cache->save($minute_throttle_key, $new_minute_throttle);
        }
        
        // Try to fulfill request.
        $request_method = $this->_request->server('REQUEST_METHOD', 'GET');
        try {
            // Get arguments from query string
            switch ($request_method) {
                case 'GET':
                    $args = $this->_request->get;
                break;
                case 'POST':
                    $args = $this->_request->post;
                break;
                case 'PUT':
                case 'DELETE':
                    parse_str($_SERVER['QUERY_STRING'], $args);
                break;
                default:
                    $args = array();
                break;
            }
            
            $results = App_Api::newRequest('v1', $request_method, $this->_info, $args);
            $results = array_merge($results->results, array('status_code' => $results->status_code));
        } catch (Exception $e) {
            $results = App_Api::formatError($e);
        }

        $this->renderOutput($results);
    }
    
    protected function renderOutput($results)
    {
        $this->_view = null;
        $this->_layout = null;
        
        if (isset($results['status_code'])) {
            $this->_response->setStatusCode((int) $results['status_code']);
        } 
        
        if (in_array($this->_request->http('Content-Type'), array_keys(self::$formats)) || $this->_request->get('format')) {
            if (strtolower($this->_request->get('format')) == 'json') {
                $this->_format = 'json';
            } elseif (strtolower($this->_request->get('format')) == 'xml') {
                $this->_format = 'xml';
            } else {
                if (isset(self::$formats[strtolower($this->_request->http('Content-Type'))])) {
                    $this->_format = self::$formats[strtolower($this->_request->http('Content-Type'))];
                }
                if (!$this->_format) {
                    $this->_format = 'json';
                }
            }
            $method = 'output' . ucfirst($this->_format);
            $this->$method($results);
        } else {
            $this->_format = 'json';
            $this->outputJson($results);
        }
    }
    
    protected function outputJson($results)
    {
        $this->_response->content = json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    protected function outputXml($results)
    {
        require_once SolarLite::$system . '/lib/TypeConverter.php';
        $this->_response->content = mjohnson\utility\TypeConverter::toXml($results);
    }
    
    protected function isValidUser($key)
    {
        // Do some database check for the key.
        return false;
    }
    
    protected function isInternalUser($key)
    {
        return true;
        return (bool) (in_array($key, SolarLite_Config::get('mobile_api_internal_sk')));
    }
}
