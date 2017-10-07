<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\NotEmpty;

class NavigationModel extends MasterModel implements InputFilterAwareInterface
{
    protected $inputFilter;

    public function __construct($services)
    {
        $this->tableName = 'navigation';
        $this->primary  = 'navigation_id';

        $this->tableView = 'view_navigation';

        parent::__construct($services);
    }

    public function getNavigations($group_navigation_id = null, $parent = 0, $level = -1, $data = array())
    {
        $paramsCache = ['name' => __FUNCTION__, 'params' => $this->tableName . '.' . $this->primary . '.' . $group_navigation_id . '.' . $parent . '.' . $level . '.' . serialize($data)];
        $keyCache = $this->getKeyCache($paramsCache);

        if (!$this->cache->setNameSpace($this->tableName)->checkItem($keyCache)) {

            $result = $this->getNavigationLoop($group_navigation_id, $parent, $level, $data);

            $this->cache->setNameSpace($this->tableName)->set($keyCache, $result);

            return $result;

        } else {
            return $this->cache->setNameSpace($this->tableName)->get($keyCache);
        }
    }

    public function getNavigationLoop($group_navigation_id = null, $parent = 0, $level = -1, $data = array())
    {
        $sql = 'SELECT * FROM ' . $this->tableView . ' WHERE navigation_parent = ' . $parent;

        if ($group_navigation_id != null) {
            $sql .= ' AND group_navigation_id = ' . $group_navigation_id;
        }

        $statement = $this->tableGateway->getAdapter()->query($sql);
        $result = $statement->execute();

        $level++;

        foreach($result as $row) {
            $menu_id = $row[$this->primary];

            $data[$menu_id] = (array) $row;
            $data[$menu_id]['navigation_level'] = $level;

            $sql = 'SELECT * FROM ' . $this->tableView . ' WHERE navigation_parent = ' . $menu_id;

            if ($group_navigation_id != null) {
                $sql .= ' AND group_navigation_id = ' . $group_navigation_id;
            }

            $statement = $this->tableGateway->getAdapter()->query($sql);
            $result = $statement->execute();

            if ($result->count() > 0) {
                $data = $this->getNavigationLoop($group_navigation_id, $menu_id, $level, $data);
            }
        }
        return $data;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }


}