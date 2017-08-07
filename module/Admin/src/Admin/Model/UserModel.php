<?php
namespace Admin\Model;

class UserModel extends MasterModel
{
    public function __construct($services)
    {
        $this->tableName = 'users';
        $this->primary  = 'users_id';

        parent::__construct($services);
    }

    public function getUser($options)
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE 1=1';
        if ($options['users_email']) {
            $sql .= ' AND users_email = "' . $options['users_email'] . '"';
        }
        if ($options['users_status']) {
            $sql .= ' AND users_status = "' . $options['users_status'] . '"';
        }
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $result = $statement->execute();

        return $result->current();
    }
}