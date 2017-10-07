<?php
namespace Admin\Form;

use Zend\Form\Form;


class GroupNavigationForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('group_navigation');

        $this->add([
            'name' => 'group_navigation_name',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],
            'options' => [
                'label' => 'Menu',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
            ],
        ]);

        $this->add([
            'name' => 'group_navigation_status',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Trạng thái',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
                'disable_inarray_validator' => true,
            ],
        ]);

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 600
                )
            )
        ));

        $this->add([
            'name' => 'frmReset',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Làm lại',
                'id' => 'frmReset',
                'class' => 'btn btn-primary',
            ],
        ]);

        $this->add([
            'name' => 'frmSubmit',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Thêm mới',
                'id' => 'frmSubmit',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}