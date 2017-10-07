<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class WebsiteEmailController extends MasterController
{
    public function __construct()
    {
        $this->module       = 'website-email';
        $this->moduleName   = 'Cấu hình gửi Email';
        $this->modelName    = 'WebsiteEmailModel';
        $this->formName     = '\Admin\Form\WebsiteEmailForm';
    }

    public function indexAction()
    {
        $this->redirect()->toRoute('admin');

        return $this->response;
    }

    public function editAction()
    {
        $this->init();

        $this->actionName = 'Cập nhật';

        $id = 1;
        $record = $this->model->fetchPrimary($id);

        if ($this->getRequest()->isPost()) {

            $paramPosts = $this->params()->fromPost();

            $dataSave = [];

            $dataSave['website_email_system_name']      = $paramPosts['website_email_system_name'];
            $dataSave['website_email_system_host']      = $paramPosts['website_email_system_host'];
            $dataSave['website_email_system_port']      = $paramPosts['website_email_system_port'];
            $dataSave['website_email_system_username']  = $paramPosts['website_email_system_username'];
            $dataSave['website_email_system_password']  = $paramPosts['website_email_system_password'];
            $dataSave['website_email_system_ssl']       = $paramPosts['website_email_system_ssl'];
            $dataSave['website_email_primary_email']    = $paramPosts['website_email_primary_email'];
            $dataSave['website_email_from']             = $paramPosts['website_email_from'];
            $dataSave['website_email_from_name']        = $paramPosts['website_email_from_name'];

            $this->model->savePrimary($dataSave, $id);

            $this->flashMessenger()->addMessage('Cập nhật thành công', 'msgInfo');
            $this->redirect()->toRoute('admin/default', ['controller' => $this->module, 'action' => 'edit']);

        }

        $this->form->get('frmSubmit')->setAttributes(array('value' => $this->actionName));
        $this->form->setData($record);

        $this->data['id'] = $id;
        $this->data['record'] = $record;

        $this->viewName = 'admin/' . $this->module . '/form.phtml';
        $this->viewTemplate = 'partial/form_normal.phtml';

        return $this->viewModel();
    }
}
