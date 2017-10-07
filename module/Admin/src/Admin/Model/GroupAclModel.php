<?php
namespace Admin\Model;

use Admin\Model\Master;

class GroupAclModel extends MasterModel{

    public function __construct($services)
    {
        $this->tableName = 'group_acl';
        $this->tableView = 'view_group_acl';

        parent::__construct($services);
    }

    public function getGroupAcl($group_admin_id, $parent = 0, $level = -1, $data = array())
    {
        $paramsCache = ['name' => __FUNCTION__, 'params' => $this->tableName . '.' . $this->primary . '.' . $group_admin_id . '.' . $parent . '.' . $level . '.' . serialize($data)];
        $keyCache = $this->getKeyCache($paramsCache);

        if (!$this->cache->setNameSpace($this->tableName)->checkItem($keyCache)) {

            $sql = 'SELECT *
                    FROM ' . $this->tableView . '
                    WHERE group_users_id = ' . $group_admin_id . ' AND group_acl_status = 1';
            $statement = $this->tableGateway->getAdapter()->query($sql);
            $result = $statement->execute();

            $data = [];
            foreach($result as  $v) {
                $data[$v['group_acl_id']] = (array) $v;
            }

            $this->cache->setNameSpace($this->tableName)->set($keyCache, $data);

            return $data;

        } else {
            return $this->cache->setNameSpace($this->tableName)->get($keyCache);
        }
    }

    public function checkExistAcl($groupAdminId, $acl_id)
    {
        $sql = 'SELECT * FROM ' . $this->tableView . ' WHERE group_users_id = ' . $groupAdminId . ' AND acl_id = ' . $acl_id;
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $result = $statement->execute();

        return $result->count();
    }

    public function getGroupAclForPermission($groupUsersId, $groupBy = true)
    {
        $paramsCache = ['name' => __FUNCTION__, 'params' => $this->tableName . '.' . $this->primary . '.' . $groupUsersId . '.' . $groupBy];
        $keyCache = $this->getKeyCache($paramsCache);

        if (!$this->cache->setNameSpace($this->tableName)->checkItem($keyCache)) {

            $sql = 'SELECT *
                    FROM ' . $this->tableView . '
                    WHERE group_users_id = ' . $groupUsersId . ' AND acl_module = "admin" AND group_acl_status = 1';

            if ($groupBy ==  true) {
                $sql .= ' GROUP BY acl_controller';
            }

            $statement = $this->tableGateway->getAdapter()->query($sql);
            $result = $statement->execute();

            $data = [];
            foreach($result as  $v) {
                $data[$v['group_acl_id']] = (array) $v;
            }

            $this->cache->setNameSpace($this->tableName)->set($keyCache, $data);

            return $data;

        } else {
            return $this->cache->setNameSpace($this->tableName)->get($keyCache);
        }
    }
}