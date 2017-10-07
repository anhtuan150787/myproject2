<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;

class UserModel extends MasterModel
{
    public function __construct($services)
    {
        $this->tableName = 'users';
        $this->primary  = 'users_id';

        $this->tableView = 'view_users';

        parent::__construct($services);
    }

    public function fetchPage($page, $pageItemPerCount)
    {
        $select = new Select($this->tableView);
        $select->order($this->primary . ' DESC');

        $paginatorAdapter   = new DbSelect($select, $this->tableGateway->getAdapter());
        $result             = new Paginator($paginatorAdapter);

        $result->setCurrentPageNumber($page);
        $result->setItemCountPerPage($pageItemPerCount);

        return $result;
    }

    public function getUser($options)
    {
        $paramsCache = ['name' => __FUNCTION__, 'params' => $this->tableName . '.' . $this->primary . '.' . serialize($options)];
        $keyCache = $this->getKeyCache($paramsCache);

        if (!$this->cache->setNameSpace($this->tableView)->checkItem($keyCache)) {

            $sql = 'SELECT * FROM ' . $this->tableView . ' WHERE 1=1';
            if ($options['users_email']) {
                $sql .= ' AND users_email = "' . $options['users_email'] . '"';
            }
            if ($options['users_status']) {
                $sql .= ' AND users_status = "' . $options['users_status'] . '"';
            }
            $statement = $this->tableGateway->getAdapter()->query($sql);
            $result = $statement->execute();

            $data = $result->current();

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
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'users_fullname',
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

            $inputFilter->add(array(
                'name'     => 'users_email',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
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
                    [
                        'name' => 'EmailAddress',
                        'options' => [
                            'messages' => [
                                EmailAddress::INVALID => 'Email không đúng định dạng',
                                EmailAddress::INVALID_HOSTNAME => 'Email không đúng định dạng',
                                EmailAddress::INVALID_FORMAT => 'Email không đúng định dạng',
                                EmailAddress::INVALID_LOCAL_PART => 'Email không đúng định dạng',
                                EmailAddress::INVALID_MX_RECORD => 'Email không đúng định dạng',
                                EmailAddress::INVALID_SEGMENT => 'Email không đúng định dạng',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Db\NoRecordExists',
                        'options' => [
                            'table' => 'users',
                            'field' => 'users_email',
                            'adapter' => $this->dbAdapter,
                            'messages' => [
                                NoRecordExists::ERROR_RECORD_FOUND => 'Email đã tồn tại. Vui lòng nhập Email khác',
                            ],
                        ],
                    ]
                ],
            ));

            $inputFilter->add([
                'name' => 'users_password',
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
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'users_picture',
                'required' => false,
                'allow_empty' => true,
                'validators' => [
                    [
                        'name' => 'Zend\Validator\File\Size',
                        'options' => [
                            'max' => '1MB'
                        ],
                    ],
                    [
                        'name' => 'Zend\Validator\File\Extension',
                        'options' => [
                            'extension' => 'png,jpg,gif,jpeg',
                        ],
                    ],
                ],
            ]);


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}