<?php
/**
 * App_Model_Users
 *
 */
class App_Model_Users extends App_Model
{
    protected $_table_name = 'users';
    protected $_model_name = 'Users';
    protected $_primary_col = 'id';
    
    /**
     * fetchUserByUsername
     * Retrieve user by username
     *
     * @param $username string
     *
     * @return array
     */
    public function fetchUserByUsername($username)
    {
        $sql = "SELECT * FROM {$this->_table_name} WHERE username = :un";
        $data = array('un' => trim($username));

        return $this->fetchOne($sql, $data);
    }

    /**
     * fetchUserByEmail
     * Retrieve user by email
     *
     * @param $email
     *
     * @return
     */
    public function fetchUserByEmail($email)
    {
        $sql = "SELECT * FROM {$this->_table_name} WHERE email = :e";
        $data = array('e' => trim($email));

        return $this->fetchOne($sql, $data);
    }

    /**
     * fetchUsers
     * Retrieve all users
     * 
     * @return array
     */
    public function fetchUsers()
    {
        $sql = "SELECT u.*, g.name AS group_name
                FROM {$this->_table_name} AS u
                LEFT JOIN groups g ON g.id = u.group_id
                ORDER BY username ASC";
                
        return $this->fetchAll($sql);
    }

    /**
     * createUser
     * Create new SuperAdmin user
     *
     * @param $username string
     * @param $email string
     * @param $password string
     * @param $group_id integer
     *
     * @return integer last insert ID
     */
    public function createUser($username, $email, $password, $group_id = null)
    {
        // already exists?
        if ($this->fetchUserByUsername($username)) {
            return false;
        }

        $hasher = new App_Phpass(8, FALSE);
        $hash = $hasher->HashPassword($password);

        return $this->insert(
            array(
                'username' => trim($username),
                'email' => trim($email),
                'password' => trim($hash),
                'group_id' => $this->isInt($group_id) ? (int) $group_id : null,
                'api_key' => md5(uniqid(mt_rand(), TRUE)),
            )
        );
    }

    /**
     * updateUser
     * Update SuperAdmin user information (username, email, group_id)
     *
     * @param $id integer
     * @param $values array
     *
     * @return
     */
    public function updateUser($id, $values)
    {
        // already exists?
        if (!$this->isInt($id)) {
            return false;
        }

        $data = array(
            'username' => trim($values['username']),
            'email' => trim($values['email']),
            'group_id' => (isset($values['group_id']) && $values['group_id'] > 0) ? (int) $values['group_id'] : null,
        );

        $where = array('id = ?' => (int) $id);

        return $this->update($data, $where);
    }

    /**
     * isValidCredentials
     * Authentication check function for username and password
     * TODO: Uses phpass library for bcrypt ... PHP completely supports
     * this now, lets do away with phpass library
     *
     * @param $username string
     * @param $pass string
     *
     * @return boolean
     */
    public function isValidCredentials($username, $pass)
    {
        $sql = "SELECT username, password FROM {$this->_table_name} WHERE username = :un";
        $data = array('un' => $username);
        $stmt = $this->query($sql, $data);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($user)) {
            $hasher = new App_Phpass(8, FALSE);
            return (bool) $hasher->CheckPassword($pass, $user['password']);
        }

        return false;
    }

    /**
     * resetPassword
     * Make an entry for password reset code and email the user. Only when 
     * they confirm from e-mail will their password actually change.
     *
     * @param $email string
     *
     * @return null
     */
    public function resetPassword($email)
    {
        $reset_code = md5(uniqid(mt_rand(), TRUE));
        $data = array('password_reset_code' => $reset_code);
        $where = array('email = ?' => $email);
        $this->update($data, $where);

        $user = $this->fetchUserByEmail($email);

        // send email
        $e = new App_Email();
        $e->sendForgotPasswordEmail($email, $reset_code, $user['username']);
    }
    
    /**
     * resetPasswordFromCode
     * Once a user has confirm from email to change their password this 
     * function will actually change their password to the random one previously set
     *
     * @param $code string
     * @param $email string
     *
     * @return boolean
     */
    public function resetPasswordFromCode($code, $email)
    {
        $sql = "SELECT id, reset_password FROM {$this->_table_name} WHERE password_reset_code = :code AND email = :email";
        $data = array('code' => $code, 'email' => $email);
        $result = $this->fetchOne($sql, $data);
        if (empty($result)) {
            return false;
        }

        $sql = "UPDATE {$this->_table_name} SET password_reset_code = NULL, reset_password = NULL WHERE email = :email";
        $data = array('email' => $email);
        $this->query($sql, $data);

        $password = App_Util::makePassphrase();
        $hasher = new App_Phpass(8, FALSE);
        $hash = $hasher->HashPassword($password);
        
        $data = array('password' => $hash);
        $where = array('email = ?' => $email);
        $this->update($data, $where);
        
        // send email
        $e = new App_Email();
        $e->sendForgotPasswordEmail2($email, $password, $u['username']);
        return true;
    }

    /**
     * setAutoLogin
     * Update auto login code for remember me logins.
     *
     * @param $id integer
     * @param $code string
     *
     * @return
     */
    public function setAutoLogin($id, $code)
    {
        $data = array(
            'auto_login_code' => $code
        );

        $where = array('id = ?' => (int) $id);

        return $this->update($data, $where);
    }

    /**
     * checkAutoLogin
     * Correct auto login code?
     *
     * @param $id integer
     * @param $code string
     *
     * @return boolean
     */
    public function checkAutoLogin($id, $code)
    {
        $sql = "SELECT * FROM {$this->_table_name} WHERE id = :iid AND auto_login_code = :alc";
        $data = array('iid' => $id, 'alc' => $code);

        $result = $this->fetchOne($sql, $data);
        if ($result) {
            return true;
        }

        return false;
    }
    
    /**
     * updateUserPassword
     * Insert description here
     *
     * @param $id
     * @param $password
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function updateUserPassword($id, $password)
    {
        // already exists?
        if (!$this->isInt($id)) {
            return false;
        }
        
        $hasher = new App_Phpass(8, FALSE);
        $hash = $hasher->HashPassword($password);
        
        $data = array('password' => $hash);
        $where = array('id = ?' => (int) $id);
        
        $this->update($data, $where);
    }
}
