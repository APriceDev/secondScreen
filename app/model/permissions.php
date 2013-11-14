<?php
/**
 * App_Model_Permissions
 */
class App_Model_Permissions extends App_Model
{
    protected $_table_name = 'permissions';
    protected $_model_name = 'Permissions';
    protected $_primary_col = 'id';
    
    public $default_permissions = array(
        'admin_users_view'   => 'Allow viewing of superadmin users',
        'admin_users_create' => 'Allow creation of superadmin users',
        'admin_users_edit'   => 'Allow editing of superadmin users',
        'admin_users_delete' => 'Allow deletion of superadmin users',
        'admin_groups_view'   => 'Allow viewing of superadmin groups',
        'admin_groups_create' => 'Allow creation of superadmin groups',
        'admin_groups_edit'   => 'Allow editing of superadmin groups',
        'admin_groups_delete' => 'Allow deletion of superadmin groups',
        'admin_permissions_view'   => 'Allow viewing of superadmin permissions',
        'admin_permissions_create' => 'Allow creation of superadmin permissions',
        'admin_permissions_edit'   => 'Allow editing of superadmin permissions',
        'admin_permissions_delete' => 'Allow deletion of superadmin permissions',
    );
    
    /**
     * fetchPermissionByName
     * Retrieve permission by name
     *
     * @param $name string
     *
     * @return array
     */
    public function fetchPermissionByName($name)
    {
        $sql = "SELECT * FROM {$this->_table_name} WHERE name = :pn";
        $data = array('pn' => $name);
        
        return $this->fetchOne($sql, $data);
    }
    
    /**
     * fetchPermissions
     * Fetch all permissions
     *
     * @return array
     */
    public function fetchPermissions()
    {
        return $this->fetchAll("SELECT * FROM {$this->_table_name}");
    }
    
    /**
     * createPermission
     * Create new ACL permission entry
     *
     * @param $name string
     * @param $description string
     *
     * @return integer last insert id
     */
    public function createPermission($name, $description)
    {
        // already exists?
        if ($this->fetchPermissionByName($name)) {
            return false;
        }
        
        // attempted to create a default permission? disallow.
        if (in_array(trim($name), array_keys($this->default_permissions))) {
            return false;
        }
        
        return $this->insert(
            array(
                'name' => trim($name),
                'description' => trim($description),
            )
        );
    }
    
    /**
     * updatePermission
     * Update ACL permission entry
     *
     * @param $id integer
     * @param $name string
     * @param $description string
     *
     * @return
     */
    public function updatePermission($id, $name, $description)
    {
        // already exists?
        if (!$this->isInt($id)) {
            return false;
        }
        
        // attempted to edit a default permission? disallow.
        if (in_array(trim($name), array_keys($this->default_permissions))) {
            return false;
        }
        
        $data = array(
            'name' => trim($name),
            'description' => trim($description),
        );
        
        $where = array('id = ?' => (int) $id);
        
        return $this->update($data, $where);
    }
}
