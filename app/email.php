<?php
/**
 * Hovara Email class
 */
class App_Email
{
    public $SES;
    public $email_settings = array();
    public $template_path;
    public $response;
    public $error;
    
    public function __construct()
    {
        $this->template_path = SolarLite_Config::get('email_templates_path');
        $this->email_settings = SolarLite_Config::get('email_settings');
    }
    
    // Load e-mail template from file
    protected function _loadEmailText($email_type, $title, $vals) 
    {
        ob_start();
        include "{$this->template_path}_header.php";
        include "{$this->template_path}{$email_type}.php";
        include "{$this->template_path}_footer.php";
        $s = ob_get_contents();
        ob_end_clean();

        $vals['email_subject'] = $title;
        foreach ($vals as $name => $value) {
            $s = str_replace("[{$name}]", $value, $s);
        }
        return $s;
    }
    
    protected function _mail($email, $subject, $body)
    {
        $mail = new SolarLite_Mail_Message();
        $mail->addTo($email)
             ->setFrom($this->email_settings['from_addr'], $this->email_settings['from_name'])
             ->setReturnPath($this->email_settings['from_addr']);
        $mail->setSubject($subject);
        $mail->setHtml($body);
        return $mail->send();
    }
    
    public function sendContactEmail($values, $email)
    {
        $subject = SolarLite_Config::get('project_name') . " - Contact E-mail";
        $body = $this->_loadEmailText('contact', $subject, array(
            'name' => htmlspecialchars($values['name'], ENT_COMPAT, 'UTF-8'),
            'sender' => htmlspecialchars($values['email'], ENT_COMPAT, 'UTF-8'),
            'message' => htmlspecialchars($values['message'], ENT_COMPAT, 'UTF-8'),
            'ip' => App_Util::getIP(),
            'email' => $email,
        ));
        $this->_mail($email, $subject, $body);
    }
 
    
    public function sendForgotPasswordEmail($email, $code, $username)
    {
        $url = BASE_URL . "/reset/$code/" . base64_encode($email);
        $subject = SolarLite_Config::get('project_name') . " - Forgotten Username or Password";
        $body = $this->_loadEmailText('forgotpw', $subject, array('un' => $username, 'url' => $url, 'email' => $email));
        return $this->_mail($email, $subject, $body);
    }
    
    public function sendForgotPasswordEmail2($email, $pass, $username)
    {
        $subject = SolarLite_Config::get('project_name') . " - Password Reset";
        $body = $this->_loadEmailText('forgotpw2', $subject, array('un' => $username, 'pass' => $pass, 'email' => $email));
        return $this->_mail($email, $subject, $body);
    }
    
    public function sendActivationEmail($email, $un, $code)
    {
        $link = BASE_URL . '/activate/' . $code . '/' . base64_encode($email);
        $subject = SolarLite_Config::get('project_name') . " - Welcome, Please Activate Your Account";
        $body = $this->_loadEmailText('welcome', $subject, array('url' => $link, 'un' => $un, 'email' => $email));

        return $this->_mail($email, $subject, $body);
    }
}
