<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;

class MasterModel {

    protected $tableGateway;

    protected $dbAdapter;
    protected $tableName;
    protected $primary;

    protected $services;

    public function __construct($services)
    {
        $this->services = $services;
        $this->dbAdapter = $this->services->get('Zend/Db/Adapter/Adapter');
        $this->tableGateway = new TableGateway($this->tableName, $this->dbAdapter);
    }

    public function save($data, $id = null)
    {
        if ($id == null) {
            $this->tableGateway->insert($data);
        } else {
            $this->tableGateway->update($data, array($this->primary => $id));
        }
    }

}