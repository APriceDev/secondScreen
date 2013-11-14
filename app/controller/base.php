<?php
class App_Controller_Base extends SolarLite_Controller
{
    protected $_action_default = 'index';
    protected $_layout_default = 'default';

    protected $_model;

    public $session;
    public $csrf_token;
    
    public $redirect;
    public $errors = null;
    
    public $logged_in = false;
    public $logged_in_username;
    public $logged_in_id = 0;
    public $logged_in_group_id = 0;

    public $js_args = "''";
    public $page_title = 'Second Screen';
    
    public $swfobject = false;

    public $form_values = array();

    // Number of attempts allowed before flood control kicks in
    const FLOODCONTROL_CLIENT_MAX = 4; // attemps
    const FLOODCONTROL_ACCOUNT_MAX = 4; // attemps

    // cookies
    protected $_cookie_domain = '';
    protected $_cookie_prefix = 'ss_';

    public $timezones = array(
        'America/New_York'    => '(GMT-05:00) - Eastern Time',
        'America/Chicago'     => '(GMT-06:00) - Central Time',
        'America/Denver'      => '(GMT-07:00) - Mountain Time',
        'America/Los_Angeles' => '(GMT-08:00) - Pacific Time',
        'America/Juneau'      => '(GMT-09:00) - Alaska',
        'Pacific/Honolulu'    => '(GMT-10:00) - Hawaii',
    );
    
    protected function _preAction()
    {
        parent::_preAction();
        if (!$this->logged_in && !($this->_controller == 'Index' && ($this->_action == 'reset' || $this->_action == 'login' || $this->_action == 'forgot-password'))) {
            $this->_redirect('/login');
        }
    }

    /**
     * _setup
     * Insert description here
     *
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    protected function _setup()
    {
        parent::_setup();
        $this->_model = new SolarLite_Catalog();

        if (!isset($this->session)) {
            $this->session = new App_Session();
        }
        
        $this->_cookie_domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        
        if (!$this->isLoggedIn()) {
            $prefix = $this->_getCookiePrefix();
            if (isset($_COOKIE[$prefix . 'id']) && isset($_COOKIE[$prefix . 'code'])) {
                $this->_autoLogin();
            }
        }
        
        if ($this->isLoggedIn()) {
            $this->setLoggedInInfo();
        }

        if ($this->session->get('timezone', false) == false || $this->session->get('location', false) == false) {
            $location = @geoip_record_by_name(App_Util::getIP());
            if (isset($location['country_code']) && isset($location['region'])) {
                $tz = geoip_time_zone_by_country_and_region($location['country_code'], $location['region']);
                $this->session->set('timezone', $tz);
            } else {
                $this->session->set('timezone', 'America/New_York');
            }
            $this->session->set('location', $location);
        }

        $tz = $this->session->get('timezone', false);
        if (!$tz || trim($tz) == '') {
            $this->session->set('timezone', 'America/New_York');
        }

        // Set CSRF Token
        $this->_setToken();
        $this->csrf_token = $this->_getToken();
    }

    /**
     * _notFound
     */
    protected function _notFound($action, $params = null)
    {
        $this->_response->setStatusCode(404);
        $this->_view = '404';
    }

    protected function _getCookiePrefix()
    {
        return $this->_cookie_prefix . sha1(BASE_URL) . '_';
    }

    /**
     * _setToken
     * This will set a session token that will be used to match against
     * forms posted.
     *
     * @return void
     */
    protected function _setToken()
    {
        if ($this->session->get('ss_token', false) === false
            || !$this->session->get('ss_token')) {
            mt_srand(); // make sure we are seeding ourself
            $token = md5(uniqid(mt_rand(), TRUE));
            $this->session->set('ss_token', $token);
        }
        // else already set.
    }

    /**
     * _getToken
     * Fetch session csrf_token if it exists
     *
     * @return string
     */
    protected function _getToken()
    {
        if ($this->session->get('ss_token', false) !== false
            && $this->session->get('ss_token') !== '') {
                return $this->session->get('ss_token');
        }
        return null;
    }

    /**
     * _checkToken
     * Check a token against the one stored in the session
     *
     * @param $token
     * @return bool
     */
    protected function _checkToken($token)
    {
        if ($this->session->get('ss_token', false) !== false
            && $this->session->get('ss_token') === $token) {
                return true;
        }
        return false;
    }

    /**
     * _setError
     * Insert description here
     *
     * @param $title
     * @param $msg
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    protected function _setError($title, $msg)
    {
        $errors = $this->session->get('ss_errors', array());
        if (isset($errors[$title])) {
            $errors[$title] = $errors[$title] . ', ' . $msg;
        } else {
            $errors[$title] = $msg;
        }
        $this->session->set('ss_errors', $errors);
    }

    /**
     * _setMessage
     * Insert description here
     *
     * @param $title
     * @param $msg
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    protected function _setMessage($title, $msg)
    {
        $messages = $this->session->get('ss_messages', array());
        if (isset($messages[$title])) {
            $messages[$title] = $messages[$title] . ', ' . $msg;
        } else {
            $messages[$title] = $msg;
        }
        $this->session->set('ss_messages', $messages);
    }

    /**
     * _incrementClientLoginAttempts
     * Increment failed login attempts in cache for an ip
     *
     */
    protected function _incrementClientLoginAttempts()
    {
        $ip = App_Util::getIP();
        if ($ip == '0.0.0.0') {
            return;
        }
        $key = 'ss_flood_control_' . $ip;
        $cache = new SolarLite_Cache_Memcache(600);
        if ($cache->fetch($key) === false) {
            $cache->add($key, 1);
        } else {
            $cache->increment($key, 1);
        }
    }

    /**
     * _clientFloodCheck
     * Check cache to see if IP requires rate limiting
     * If exceeds max attempts, inject delay
     *
     */
    protected function _clientFloodCheck()
    {
        $count = $this->_getClientFloodCount();

        if ($count >= self::FLOODCONTROL_CLIENT_MAX) {
            $delay = ($count/6) > 8 ? 8 : ($count/6);
            $this->_injectDelay($delay);
        }
    }

    /**
     * _getClientFloodCount
     * Check cache to see if IP requires rate limiting
     * If exceeds max attempts, inject delay
     *
     */
    protected function _getClientFloodCount()
    {
        $ip = App_Util::getIP();
        $count = 0;

        if ($ip != '0.0.0.0') {
            $key = 'ss_flood_control_' . $ip;

            $cache = new SolarLite_Cache_Memcache(600);
            $count = $cache->fetch($key);

            if ($count !== false && is_numeric($count)) {
                $count = (int) $count;
            } else {
                $count = 0;
            }
        }

        return $count;
    }

    /**
     * _incrementAccountLoginAttempts
     * Increment failed login attempts in cache for a username
     *
     */
    protected function _incrementAccountLoginAttempts($username)
    {
        $key = 'ss_flood_control_' . $username;

        $cache = new SolarLite_Cache_Memcache(600);
        if ($cache->fetch($key) === false) {
            $cache->add($key, 1);
        } else {
            $cache->increment($key, 1);
        }
    }


    /**
     * _accountFloodCheck
     * Check cache to see if user account requires rate limiting
     * If exceeds max account login attempts, require captcha.
     *
     * @param string $username username
     */
    protected function _accountFloodCheck($username)
    {
        $count = $this->_getAccountFloodCount($username);

        if ($count >= self::FLOODCONTROL_ACCOUNT_MAX) {
            //$this->session->set('captcha_required', true);
            $delay = ($count/6) > 8 ? 8 : ($count/6);
            $this->_injectDelay($delay);
        }
    }

    /**
     * _getAccountFloodCount
     * Check cache to see if user account requires rate limiting
     * If exceeds max account login attempts, require captcha.
     *
     * @param string $username username
     */
    protected function _getAccountFloodCount($username)
    {
        $key = 'ss_flood_control_' . $username;

        $cache = new SolarLite_Cache_Memcache(600);
        $count = $cache->fetch($key);

        if ($count !== false && is_numeric($count)) {
            $count = (int) $count;
        } else {
            $count = 0;
        }

        return $count;
    }

    /**
     * _injectDelay
     * simple util function to inject a delay into the response time.
     *
     * @param int $delay seconds
     */
    protected function _injectDelay($delay = 0) {
        if (is_numeric($delay) && $delay > 0) {
            sleep($delay);
        }
    }

    protected function _redirectLastVisited($fallback = '/')
    {
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '') {
            $last = str_replace(BASE_URL, '', $_SERVER['HTTP_REFERER']);
        } else {
            $last = $fallback;
        }

        $this->_redirect($last);
        exit;
    }

    protected function _throttleCheck($key, $seconds)
    {
        $ts = $this->session->get($key, 0);
        if (time() - $ts >= $seconds) {
            return true;
        }

        return false;
    }
    
    protected function _login($username)
    {
        $user = $this->_model->users->fetchUserByUsername($username);
        return $this->_loginUser($user);
    }
    
    protected function _loginById($id)
    {
        $user = $this->_model->users->fetchById($id);
        return $this->_loginUser($user);
    }
    
    protected function _loginUser($user)
    {
        if (empty($user)) {
            return;
        }

        unset($user['password']); // we don't need/want to retain this.
        foreach ($user as $k => $v) {
            $this->session->set($k, $v);
        }
        
        if ($user['group_id']) {
            $this->session->set('permissions', $this->_model->groups_permissions->fetchPermissionsNameListByGroup($user['group_id']));
        } else {
            $this->session->set('permissions', array());
        }

        // return the user
        return $user;
    }
    
    protected function _getUserInfo($name, $ret = null)
    {
        $user_info = $this->session->get('user_info', array());
        foreach ($user_info as $i) {
            if ($i['name'] == $name) {
                return $i['value'];
            } 
        }
        
        return $ret;
    }
    
    protected function _autoLogin()
    {
        $prefix = $this->_getCookiePrefix();
        $id = stripslashes($_COOKIE[$prefix . 'id']);
        $code = stripslashes($_COOKIE[$prefix . 'code']);

        if ($this->_model->users->checkAutoLogin($id, $code)) {
            $this->_loginById($id);
        }
    }
    
    protected function _setupAutoLogin($id)
    {
        $code = md5(uniqid(mt_rand(), TRUE));
        $prefix = $this->_getCookiePrefix();
        setcookie($prefix . "id", $id, strtotime('+2 weeks', time()), '/', $this->_cookie_domain, false, true);
        setcookie($prefix . "code", $code, strtotime('+2 weeks', time()), '/', $this->_cookie_domain, false, true);
        $this->_model->users->setAutoLogin($id, $code);
    }
    
    /**
     * isLoggedIn
     * Is the user logged in?
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        $uid = $this->session->get('id', false);
        if ($uid !== false) {
            return true;
        }

        return false;
    }

    /**
     * setLoggedInInfo
     *
     * @return void
     */
    public function setLoggedInInfo()
    {
        $this->logged_in = true;
        $this->logged_in_username = $this->session->get('username');
        $this->logged_in_id = (int) $this->session->get('id');
        $this->logged_in_group_id = (int) $this->session->get('group_id');
    }
    
    /**
     * Does user have a permission?
     * 
     * @param string $permission permission
     * 
     * @return bool
     */
    protected function _hasPermission($permission)
    {
        if (in_array($permission, $this->session->get('permissions', array()))) {
            return true;
        }
        
        return false;
    }

    /**
     * Check a user's permission
     * 
     * @param string $permission permission
     * 
     * @return void
     */
    protected function _checkPermission($permission)
    {
        if (!$this->_hasPermission($permission)) {
            $this->_setError('Invalid Permissions', 'You need the following permission to complete this action: ' . $permission);
            $this->_redirect('/');
            return;
        }
    }
    
    /**
     * _isValidVideo
     */
    protected function _isValidVideo($upload, $max_mb = 1024)
    {
        $filename = '/tmp/' . $upload['name'];
        if (!file_exists($filename)) {
            $this->_setError('Video Upload', 'Please select a valid video file to upload.');
            return false;
        }

        $max_bytes = $max_mb * 1048576;
        if (filesize($filename) > $max_bytes) {
            $this->_setError('Video Upload', "This video file is over the $max_mb MB limit.");
            return false;
        }

        // find the file extension
        $ext  = explode('.', $upload['name']);
        $ext  = strtolower(end($ext));

        // is the file extension allowed?
        $list = array('mp4', 'wmv', 'avi', 'm4v');
        // we can check mime types properly for audio files because we are using uploadify which with flash
        // uploads all content as application/octet-stream >_<
        // using APC to show a progress indicator is not viable because
        // the file upload tracking is not threadsafe at this point,
        // so new uploads that happen while a previous one is still going
        // will disable the tracking for the previous.
        //$mimes = array('audio/mpeg', 'audio/x-ms-wma', 'audio/ogg', 'audio/mp4', 'audio/aac', 'audio/flac');
        if (!in_array($ext, $list)) {
            $this->_setError('Vide Upload', 'Please upload video files that are mp4, avi, or wmv extension.');
            return false;
        }

        return true;
    }
}
