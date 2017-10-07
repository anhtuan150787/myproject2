<?php
/**
 * Created by PhpStorm.
 * User: tri.le
 * Date: 4/1/2015
 * Time: 2:52 PM
 */

namespace Admin\View\Helper;


use Zend\View\Helper\AbstractHelper;

class FormElement extends AbstractHelper
{
    public function __invoke()
    {
        return $this;
    }

    public function input($formElement)
    {
        return $this->view->partial('admin/partial/form_element/input', ['formElement' => $formElement]);
    }

    public function input_tag($formElement)
    {
        return $this->view->partial('admin/partial/form_element/input_tag', ['formElement' => $formElement]);
    }

    public function input2($formElement)
    {
        return $this->view->partial('admin/partial/form_element/input2', ['formElement' => $formElement]);
    }

    public function picture($formElement, $module, $record, $url, $primary)
    {
        return $this->view->partial('admin/partial/form_element/picture', ['formElement' => $formElement, 'module' => $module, 'record' => $record, 'url' => $url, 'primary' => $primary]);
    }

    public function text($name, $value)
    {
        return $this->view->partial('admin/partial/form_element/text', ['name' => $name, 'value' => $value]);
    }

    public function textarea($formElement)
    {
        return $this->view->partial('admin/partial/form_element/textarea', ['formElement' => $formElement]);
    }

    public function checkbox($formElement)
    {
        return $this->view->partial('admin/partial/form_element/checkbox', ['formElement' => $formElement]);
    }
}