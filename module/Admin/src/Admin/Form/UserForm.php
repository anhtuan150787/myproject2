<?php
namespace Admin\Form;

use Zend\Form\Form;


class UserForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('user');

        $this->add([
            'name' => 'users_fullname',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],
            'options' => [
                'label' => 'Tên',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
            ],
        ]);

        $this->add([
            'name' => 'users_phone',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Điện thoại',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
            ],
        ]);

        $this->add([
            'name' => 'users_picture',
            'type' => 'file',
            'attributes' => [
                'class' => 'form-control',
                'onchange' => 'readURL(this);',
            ],
            'options' => [
                'label' => 'Hình đại diện',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
            ],
        ]);

        $this->add([
            'name' => 'users_email',
            'type' => 'email',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],
            'options' => [
                'label' => 'Email',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
            ],
        ]);

        $this->add(array(
            'name' => 'users_password',
            'type' => 'Password',
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Mật khẩu',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
            ],
        ));

        $this->add(array(
            'name' => 'users_password_confirm',
            'type' => 'Password',
            'attributes' => [
                'class' => 'form-control',
                'data-validate-linked' => 'users_password',
            ],
            'options' => [
                'label' => 'Nhập lại Mật khẩu',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
            ],
        ));

        $this->add([
            'name' => 'group_users_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'class' => 'form-control required',
            ],
            'options' => [
                'label' => 'Nhóm',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
                'disable_inarray_validator' => true,
            ],
        ]);

        $this->add([
            'name' => 'users_status',
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