<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MessageController extends MasterController
{
    public function indexAction()
    {
        $this->init();

        $type = $this->params()->fromQuery('type');

        switch($type) {
            case 'deny':
                $this->msgErrorLine = 'Không có quyền truy cập';
                break;

            default:
                break;
        }

        $this->viewTemplate = 'admin/message/index.phtml';

        return $this->viewModel();
    }
}
