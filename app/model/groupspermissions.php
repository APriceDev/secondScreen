<?php
/**
 * App_Model_GroupsPermissions
 */
class App_Model_GroupsPermissions extends App_Model
{
    protected $_table_name = 'groups_permissions';
    protected $_model_name = 'GroupsPermissions';
    protected $_primary_col = null;
    
    /**
     * fetchPermissionsByGroup
     * Get the ACL permission given a particular group ID
     *
     * @param $group_id integer
     *
     * @return array
     */
    public function fetchPermissionsByGroup($group_id)
    {
        $sql = "SELECT p.* 
                FROM permissions p 
                INNER JOIN {$this->_table_name} gp ON p.id = gp.permission_id 
                WHERE gp.group_id = :gid";
                
        $data = array('gid' => (int) $group_id);
        
        return $this->fetchAll($sql, $data);
    }
    
    /**
     * fetchPermissionsIdListByGroup
     * Retrieve a list of permission IDs by Group ID
     *
     * @param $group_id integer
     *
     * @return array
     */
    public function fetchPermissionsIdListByGroup($group_id)
    {
        $arr = array();
        $results = $this->fetchPermissionsByGroup($group_id);
        foreach ($results as $r) {
            $arr[] = $r['id'];
        }
        return $arr;
    }
    
    /**
     * fetchPermissionsNameListByGroup
     * Retrieve a list of permission names by Group ID
     *
     * @param $group_id integer
     *
     * @return array
     */
    public function fetchPermissionsNameListByGroup($group_id)
    {
        $arr = array();
        $results = $this->fetchPermissionsByGroup($group_id);
        foreach ($results as $r) {
            $arr[] = $r['name'];
        }
        return $arr;
    }
    
    /**
     * addGroupPermission
     * Add associate for a permission to a group, by permission ID and group ID
     *
     * @param $group_id integer
     * @param $permission_id integer
     *
     * @return null
     */
    public function addGroupPermission($group_id, $permission_id)
    {
        $sql = "SELECT * FROM {$this->_table_name} WHERE group_id = :gid AND permission_id = :pid";
        $data = array('gid' => (int) $group_id, 'pid' => (int) $permission_id);
        $res = $this->fetchOne($sql, $data);
        
        if (!$res) {
            $data = array(
                'permission_id' => (int) $permission_id,
                'group_id' => (int) $group_id,
            );
            $this->insert($data);
        }
    }
    
    /**
     * deleteGroupPermission
     * Delete a group/permission association
     *
     * @param $group_id integer
     * @param $permission_id integer
     *
     * @return null
     */
    public function deleteGroupPermission($group_id, $permission_id)
    {
        $where = array('group_id = ? AND permission_id = ?' => array((int) $group_id, (int) $permission_id));
        $this->delete($where);
    }
    
    /**
     * deleteByGroup
     * Delete all permission associated for a given group.
     *
     * @param $group_id integer
     *
     * @return null
     */
    public function deleteByGroup($group_id)
    {
        $where = array('group_id = ?' => array((int) $group_id));
        $this->delete($where);
    }
}
