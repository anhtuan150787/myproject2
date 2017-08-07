<?php
namespace Admin\Form;

use Zend\Form\Form;


class ForgetForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('users');

        $this->add(array(
            'name' => 'users_email',
            'type' => 'email',
            'attributes' => [
                'placeholder' => 'Email',
                'class' => 'form-control',
                'required' => 'required',
            ],
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 600
                )
            )
        ));

        $this->add(array(
            'name' => 'users_submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Gửi mật khẩu mới',
                'id' => 'submitbutton',
                'class' => 'btn btn-default submit',
            ),
        ));
    }
}