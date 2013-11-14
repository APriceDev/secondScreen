<?php
/**
 * App_Model_Groups
 */
class App_Model_Groups extends App_Model
{
    protected $_table_name = 'groups';
    protected $_model_name = 'Groups';
    protected $_primary_col = 'id';
    
    /**
     * fetchGroupByName
     * Fetch ACL Group by name
     *
     * @param $name string
     *
     * @return array
     */
    public function fetchGroupByName($name)
    {
        $sql = "SELECT * FROM {$this->_table_name} WHERE name = :gn";
        $data = array('gn' => $name);
        
        return $this->fetchOne($sql, $data);
    }
    
    /**
     * createGroup
     * Create new admin ACL Group
     *
     * @param $name string
     * @param $description string
     *
     * @return integer last insert id
     */
    public function createGroup($name, $description)
    {
        // already exists?
        if ($this->fetchGroupByName($name)) {
            return false;
        }
        
        return $this->insert(array(
            'name' => trim($name),
            'description' => trim($description),
        ));
    }
    
    /**
     * fetchGroups
     * Fetch all ACL groups with fetch to corresponding permissions
     *
     *
     * @return array
     */
    public function fetchGroups()
    {
        $results = $this->fetchAll("SELECT * FROM {$this->_table_name}");
        foreach ($results as $k => $v) {
            $results[$k]['permissions'] = $this->_catalog->groups_permissions->fetchPermissionsByGroup($v['id']);
        }
        return $results;
    }
    
    /**
     * updateGroup
     * Update group name and/or description by ID
     *
     * @param $id integer
     * @param $name string
     * @param $description string
     *
     * @return
     */
    public function updateGroup($id, $name, $description)
    {
        // already exists?
        if (!$this->isInt($id)) {
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
