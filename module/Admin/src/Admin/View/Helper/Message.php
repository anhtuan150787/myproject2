<?php
/**
 * Created by PhpStorm.
 * User: tri.le
 * Date: 4/1/2015
 * Time: 2:52 PM
 */

namespace Admin\View\Helper;


use Zend\View\Helper\AbstractHelper;

class Message extends AbstractHelper
{
    public function __invoke($params)
    {
        return $this->view->partial('admin/partial/message', $params);
    }
}