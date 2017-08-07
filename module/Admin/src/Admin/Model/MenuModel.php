<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class MenuModel extends MasterModel implements InputFilterAwareInterface
{
    protected $inputFilter;

    public function __construct($services)
    {
        $this->tableName = 'menu';
        $this->primary  = 'menu_id';

        parent::__construct($services);
    }

    public function getMenus($parent = 0, $level = -1, $data = array())
    {
        return $this->getMenuLoop($parent, $level, $data);
    }

    public function getMenuLoop($parent = 0, $level = -1, $data = array())
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE menu_parent = ' . $parent;
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $result = $statement->execute();

        $level++;

        foreach($result as $row) {
            $menu_id = $row['menu_id'];

            $data[$menu_id] = (array) $row;
            $data[$menu_id]['menu_level'] = $level;

            $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE menu_parent = ' . $menu_id;

            $statement = $this->tableGateway->getAdapter()->query($sql);
            $result = $statement->execute();

            if ($result->count() > 0) {
                $data = $this->getMenuLoop($menu_id, $level, $data);
            }
        }
        return $data;
    }

    public function getMenu($options) {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE 1=1';
        if ($options['menu_id']) {
            $sql .= ' AND menu_id = ' . $options['menu_id'];
        }
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function delete($options)
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE 1=1';
        if ($options['menu_id']) {
            $sql .= ' AND menu_id=' . $options['menu_id'];
        }
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $result = $statement->execute();
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
                'name' => 'menu_name',
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
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Vui lòng nhập thông tin',
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