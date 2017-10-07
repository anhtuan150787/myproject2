<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class MasterModel {

    protected $tableGateway;
    protected $dbAdapter;
    protected $services;
    protected $cache;
    protected $tableView;

    public $tableName;
    public $primary;

    public function __construct($services)
    {
        $this->services = $services;
        $this->dbAdapter = $this->services->get('Zend/Db/Adapter/Adapter');
        $this->tableGateway = new TableGateway($this->tableName, $this->dbAdapter);
        $this->cache = $this->services->get('Cache');
    }

    public function getKeyCache($params)
    {
        return md5(serialize($params));
    }

    public function savePrimary($data, $id = null)
    {
        if ($id == null) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        } else {
            $this->tableGateway->update($data, array($this->primary => $id));
        }

        $this->cache->setNameSpace($this->tableName)->clearByNameSpace();

        return $id;
    }

    public function saveWhere($data, $where)
    {
        $this->tableGateway->update($data, $where);

        $this->cache->setNameSpace($this->tableName)->clearByNameSpace();
    }

    public function fetchPage($page, $pageItemPerCount)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary . ' DESC');

        $paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter());
        $result = new Paginator($paginatorAdapter);

        $result->setCurrentPageNumber($page);
        $result->setItemCountPerPage($pageItemPerCount);

        return $result;
    }

    public function fetchAll()
    {
        $paramsCache = ['name' => __FUNCTION__, 'params' => $this->tableName . '.' . $this->primary];
        $keyCache = $this->getKeyCache($paramsCache);

        if (!$this->cache->setNameSpace($this->tableName)->checkItem($keyCache)) {

            $sql = 'SELECT * FROM ' . $this->tableName;
            $statement = $this->tableGateway->getAdapter()->query($sql);
            $result = $statement->execute();

            $data = [];
            foreach($result as $k => $v) {
                $data[$k] = $v;
            }

            $this->cache->setNameSpace($this->tableName)->set($keyCache, $data);

            return $data;

        } else {
            return $this->cache->setNameSpace($this->tableName)->get($keyCache);
        }
    }

    public function fetchWhere($where, $limit = null)
    {
        $paramsCache = ['name' => __FUNCTION__, 'params' => $this->tableName . '.' . $this->primary . '.' .  $where];
        $keyCache = $this->getKeyCache($paramsCache);

        if (!$this->cache->setNameSpace($this->tableName)->checkItem($keyCache)) {

            $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE ' . $where;
            if ($limit != null) {
                $sql .= ' LIMIT ' . $limit;
            }
            $statement = $this->tableGateway->getAdapter()->query($sql);
            $result = $statement->execute();

            $data = [];
            foreach($result as $k => $v) {
                $data[$k] = $v;
            }

            $this->cache->setNameSpace($this->tableName)->set($keyCache, $data);

            return $data;

        } else {
            return $this->cache->setNameSpace($this->tableName)->get($keyCache);
        }
    }

    public function fetchPrimary($id)
    {
        $paramsCache = ['name' => __FUNCTION__, 'params' => $this->tableName . '.' . $this->primary . '.' . $id];
        $keyCache = $this->getKeyCache($paramsCache);

        if (!$this->cache->setNameSpace($this->tableName)->checkItem($keyCache)) {

            $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE ' . $this->primary . '=' . $id;
            $statement = $this->tableGateway->getAdapter()->query($sql);
            $result = $statement->execute();
            $data = $result->current();

            $this->cache->setNameSpace($this->tableName)->set($keyCache, $data);

            return $data;

        } else {
            return $this->cache->setNameSpace($this->tableName)->get($keyCache);
        }
    }

    public function deletePrimary($id)
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE ' . $this->primary . '=' . $id;
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $result = $statement->execute();

        $this->cache->setNameSpace($this->tableName)->clearByNameSpace();
    }

    public function deleteWhere($where)
    {
        $this->tableGateway->delete($where);

        $this->cache->setNameSpace($this->tableName)->clearByNameSpace();
    }

    public function countAll()
    {
        $paramsCache = ['name' => __FUNCTION__, 'params' => $this->tableName . '.' . $this->primary];
        $keyCache = $this->getKeyCache($paramsCache);

        if (!$this->cache->setNameSpace($this->tableName)->checkItem($keyCache)) {

            $sql = 'SELECT count(' . $this->primary . ') as total FROM ' . $this->tableName;
            $statement = $this->tableGateway->getAdapter()->query($sql);
            $result = $statement->execute();
            $result = $result->current();

            $data = $result['total'];
            $this->cache->setNameSpace($this->tableName)->set($keyCache, $data);

            return $data;

        } else {
            return $this->cache->setNameSpace($this->tableName)->get($keyCache);
        }

    }

}