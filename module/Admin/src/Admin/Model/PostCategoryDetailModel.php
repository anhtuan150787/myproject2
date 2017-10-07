<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\NotEmpty;

class PostCategoryDetailModel extends MasterModel implements InputFilterAwareInterface
{
    protected $inputFilter;

    public function __construct($services)
    {
        $this->tableName = 'post_category_detail';
        $this->primary  = 'post_category_detail_id';

        $this->tableView = 'view_post_category_detail';

        parent::__construct($services);
    }

    public function fetchWhere($where)
    {
        $paramsCache = ['name' => __FUNCTION__, 'params' => $this->tableName . '.' . $this->primary . '.' .  $where];
        $keyCache = $this->getKeyCache($paramsCache);

        if (!$this->cache->setNameSpace($this->tableName)->checkItem($keyCache)) {

            $sql = 'SELECT * FROM ' . $this->tableView . ' WHERE ' . $where;
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

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
    }
}