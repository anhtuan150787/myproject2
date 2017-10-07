<?php
namespace Admin\Form;

use Zend\Form\Form;


class WebsiteGeneralForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('website_general');

        $this->add([
            'name' => 'website_general_title',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Tên Website',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
            ],
        ]);

        $this->add([
            'name' => 'website_general_keyword',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Keyword',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
            ],
        ]);

        $this->add([
            'name' => 'website_general_description',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Description',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
            ],
        ]);

        $this->add([
            'name' => 'website_general_favicon',
            'type' => 'file',
            'attributes' => [
                'class' => 'form-control',
                'onchange' => 'readURL(this);',
            ],
            'options' => [
                'label' => 'Favicon',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
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