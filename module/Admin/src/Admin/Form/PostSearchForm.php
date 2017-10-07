<?php
namespace Admin\Form;

use Zend\Form\Form;


class PostSearchForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('post_search');

        $this->add([
            'name' => 'post_title_search',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Tiêu đề',
            ],
        ]);

        $this->add([
            'name' => 'post_category_search',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Danh mục',
                'disable_inarray_validator' => true,
            ],
        ]);

        $this->add([
            'name' => 'post_date_from_search',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control datepicker',
            ],
            'options' => [
                'label' => 'Ngày cập nhật từ',
            ],
        ]);

        $this->add([
            'name' => 'post_date_to_search',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control datepicker',
            ],
            'options' => [
                'label' => 'Ngày cập nhật đến',
            ],
        ]);

        $this->add([
            'name' => 'post_status_search',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Trạng thái',
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
            'name' => 'frmSubmit',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Tìm kiếm',
                'id' => 'frmSubmit',
                'class' => 'btn btn-primary',
            ],
        ]);
    }
}