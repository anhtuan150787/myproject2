<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\NotEmpty;

class AclModel extends MasterModel implements InputFilterAwareInterface
{
    protected $inputFilter;

    public function __construct($services)
    {
        $this->tableName = 'acl';
        $this->primary  = 'acl_id';

        parent::__construct($services);
    }

    public function getAcls($parent = 0, $level = -1, $data = array())
    {
        $paramsCache = ['name' => __FUNCTION__, 'params' => $this->tableName . '.' . $this->primary . '.' . $parent . '.' . $level . '.' . serialize($data)];
        $keyCache = $this->getKeyCache($paramsCache);

        if (!$this->cache->setNameSpace($this->tableName)->checkItem($keyCache)) {

            $result = $this->getAcl($parent, $level, $data);

            $this->cache->setNameSpace($this->tableName)->set($keyCache, $result);

            return $result;

        } else {
            return $this->cache->setNameSpace($this->tableName)->get($keyCache);
        }
    }

    public function getAcl($parent = 0, $level = -1, $data = array()) {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE acl_parent = ' . $parent;
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $result = $statement->execute();

        $level++;

        foreach($result as $row) {
            $acl_id = $row['acl_id'];

            $data[$acl_id] = (array) $row;
            $data[$acl_id]['acl_level'] = $level;

            $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE acl_parent = ' . $acl_id;

            $statement = $this->tableGateway->getAdapter()->query($sql);
            $result = $statement->execute();

            if ($result->count() > 0) {
                $data = $this->getAcl($acl_id, $level, $data);
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
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'acl_name',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'Stringtrim'],
                ],
                'validators' => [
                    [
                        'name' => 'not_empty',
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'Vui lòng nhập thông tin',
                            ]
                        ],
                        'break_chain_on_failure' => true,
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }


}