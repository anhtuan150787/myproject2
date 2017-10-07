<?php
namespace Admin\Form;

use Zend\Form\Form;


class PostForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('post');

        $this->add([
            'name' => 'post_title',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],
            'options' => [
                'label' => 'Tiêu đề',
                'label_attributes' => ['class' => 'control-label col-md-2 col-sm-2 col-xs-12'],
            ],
        ]);

        $this->add([
            'name' => 'post_quote',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => [
                'class' => 'form-control description',
            ],
            'options' => [
                'label' => 'Tóm tắt',
                'label_attributes' => ['class' => 'control-label col-md-2 col-sm-2 col-xs-12'],
            ],
        ]);

        $this->add([
            'name' => 'post_tag',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control tags',
            ],
            'options' => [
                'label' => 'Tags',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
            ],
        ]);

        $this->add([
            'name' => 'post_body',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => [
                'class' => 'form-control content',
            ],
            'options' => [
                'label' => 'Nội dung',
                'label_attributes' => ['class' => 'control-label col-md-2 col-sm-2 col-xs-12'],
            ],
        ]);

        $this->add([
            'name' => 'post_category_id',
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'attributes' => [
                //'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Danh mục',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
                'disable_inarray_validator' => true,
            ],
        ]);

        $this->add([
            'name' => 'post_picture',
            'type' => 'file',
            'attributes' => [
                'class' => 'form-control',
                'onchange' => 'readURL(this);',
            ],
            'options' => [
                'label' => 'Hình',
                'label_attributes' => ['class' => 'control-label col-md-3 col-sm-3 col-xs-12'],
            ],
        ]);


        $this->add([
            'name' => 'post_status',
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