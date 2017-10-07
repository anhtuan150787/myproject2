<?php
namespace Admin\Model;

use Admin\Model\Master;

class GroupMenuModel extends MasterModel {

    public function __construct($services)
    {
        $this->tableName = 'group_menu';
        $this->tableView = 'view_group_menu';

        parent::__construct($services);
    }

    public function getGroupMenu($group_admin_id, $parent = 0, $level = -1, $data = array()) {

        $paramsCache = ['name' => __FUNCTION__, 'params' => $this->tableName . '.' . $this->primary . '.' . $group_admin_id . '.' . $parent . '.' . $level . '.' . serialize($data)];
        $keyCache = $this->getKeyCache($paramsCache);

        if (!$this->cache->setNameSpace($this->tableName)->checkItem($keyCache)) {

            $result = $this->getGroupMenuList($group_admin_id, $parent, $level, $data);

            $this->cache->setNameSpace($this->tableName)->set($keyCache, $result);

            return $result;

        } else {
            return $this->cache->setNameSpace($this->tableName)->get($keyCache);
        }
    }

    public function getGroupMenuList($group_admin_id, $parent = 0, $level = -1, $data = array())
    {
        $sql = 'SELECT * FROM ' . $this->tableView . '
                WHERE group_users_id = ' . $group_admin_id . '
                    AND group_menu_status = 1
                    AND menu_parent = ' . $parent . ' ORDER BY menu_position ASC, menu_id ASC';
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $result = $statement->execute();

        $level++;

        foreach($result as $row) {
            $menu_id = $row['menu_id'];

            $data[$menu_id] = (array) $row;
            $data[$menu_id]['menu_level'] = $level;

            $sql = 'SELECT * FROM ' . $this->tableView . '
                    WHERE group_users_id = ' . $group_admin_id . '
                    AND group_menu_status = 1
                    AND menu_parent = ' . $menu_id . ' ORDER BY menu_position ASC, menu_id ASC';

            $statement = $this->tableGateway->getAdapter()->query($sql);
            $result = $statement->execute();

            if ($result->count() > 0) {
                $data = $this->getGroupMenuList($group_admin_id, $menu_id, $level, $data);
            }
        }
        return $data;
    }

    public function checkExistMenu($groupAdminId, $menu_id)
    {
        $sql = 'SELECT * FROM ' . $this->tableView . ' WHERE group_users_id = ' . $groupAdminId . ' AND menu_id = ' . $menu_id;
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $result = $statement->execute();

        return $result->count();
    }
}