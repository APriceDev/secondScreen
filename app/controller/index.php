<?php
class App_Controller_Index extends App_Controller_Base
{
    protected $_action_default = 'index';

    public function actionIndex()
    {
        $this->page_title = "Dashboard - " . $this->page_title;
    }
    
    public function actionLogin()
    {
        $this->page_title = "Login - " . $this->page_title;
        
        if ($this->isLoggedIn()) {
            $this->_redirect('/');
        }

        $this->form_values = $this->_request->post;
        
        // process login attempt
        if ($this->_request->post('submit')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired, please reload the page to continue with this form.');
                return;
            }

            $username_or_email = $this->_request->post('username_email');
            $pass = $this->_request->post('password');
            $this->_clientFloodCheck();
            $this->_accountFloodCheck($username_or_email);
            if ($this->_model->users->isValidCredentials($username_or_email, $pass)) {
                if (strpos($username_or_email, '@') !== false) {
                    $user = $this->_model->users->fetchUserByEmail($username_or_email);
                } else {
                    $user = $this->_model->users->fetchUserByUsername($username_or_email);
                }
                $this->_login($user['username']);
                if ($this->_request->post('remember', 0)) {
                    $this->_setupAutoLogin($this->session->get('id'));
                }
                $this->_redirectLastVisited('/');
            } else {
                $this->_incrementClientLoginAttempts();
                $this->_incrementAccountLoginAttempts($username_or_email);
                $this->_setError('Login Failed', 'We did not recognize your username and/or password.  Please try again.');
            }
        }
    }
    
    public function actionLogout()
    {
        // clear session
        $this->session->resetAll();
        
        // clear auto-login cookies
        foreach ($_COOKIE as $key => $value) {
            setcookie($key, '', time()-60*60*24*14, '/', $this->_cookie_domain, false, true);
        }

        $this->_redirect('/login');
    }
    
    public function actionForgotPassword()
    {
        $this->page_title = "Forgot Password - " . $this->page_title;
        
        if ($this->isLoggedIn()) {
            $this->_redirect('/');
        }

        if ($this->_request->post('submit')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired, please reload the page to continue with this form.');
                return;
            }

            $email = $this->_request->post('email');

            // check our email address
            $user_from_email = $this->_model->users->fetchUserByEmail($email);
            if (empty($user_from_email)) {
                $this->_setError("Invalid Account", "The account email address you provided does not exist in our system.");
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->_setError("Invalid E-mail Address", "The account email address you provided is invalid.");
                return;
            }

            $this->_model->users->resetPassword($email);
            $this->_setMessage('Form Submitted', 'Please check your e-mail for your new password.');
            $this->_redirect('/login');
        }
    }
    
    public function actionReset($code, $email)
    {
        if (!$code || !$email) {
            $this->_setError('Invalid Reset', 'This is not a valid password reset link');
            $this->_redirect('/login');
        }

        if ($this->_model->users->resetPasswordFromCode($code, base64_decode($email))) {
            $this->_setMessage('Password Reset', 'You may now login with the password sent in the forgotten password e-mail.');
        } else {
            $this->_setError('Invalid Reset', 'This is not a valid password reset link');
        }

        $this->_redirect('/login');
    }
    
    public function actionSettings()
    {
        $this->page_title .= ' - Settings';
        $this->form_values = $this->_request->post;
        
        if ($this->_request->post('submit')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired. Please reload the page to continue with this form.');
                return;
            }
            
            if (!$this->_request->post('password') || trim($this->_request->post('password')) == '') {
                $this->_setError('Password Left Blank', 'Password cannot be blank.');
                return;
            }
            
            if (strlen($this->_request->post('password')) < 8) {
                $this->_setError('Password Length', 'Your password must be at least 8 characters in length.');
                return;
            }
            
            if ($this->_request->post('password') !== $this->_request->post('password2')) {
                $this->_setError('Password Mismatch', 'Your passwords do not match!');
                return;
            }

            // no errors, woot
            $this->_model->users->updateUserPassword($this->logged_in_id, trim($this->_request->post('password')));
            $this->_setMessage('Password Updated', 'Password successfully updated.');
            $this->_redirect('/');
        }
    }

    public function actionContact()
    {
        $this->page_title = "Contact Us - " . $this->page_title;
        $this->form_values = $values = $this->_request->post;

        $errors = array();

        if ($this->_request->post('submit')) {
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('Form Error', 'Invalid CSRF Token. Your session may have expired, please resubmit the form.');
                return;
            }
            
            if (trim($values['name']) == '') {
                $this->_setError('Form Error', 'Name is blank.');
                return;
            }

            $values['email'] = trim($values['email']);
            if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
                 $this->_setError('Form Error', "Invalid e-mail address.");
                 return;
            }

            if (trim($values['message']) == '') {
                $this->_setError('Form Error', 'Message is blank.');
                return;
            }

            $email = new App_Email();
            $email->sendContactEmail($values, SolarLite_Config::get('contact_email'));
            $this->_setMessage('Message Sent', 'Thank you!');
            $this->form_values = array();
        }
    }
}
