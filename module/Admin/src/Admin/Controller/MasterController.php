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
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;

class MasterController extends AbstractActionController
{
    protected $msgError;
    protected $msgInfo;
    protected $msgErrorLine;
    protected $msgInfoLine;

    protected $model;
    protected $form;
    protected $view;

    protected $module;
    protected $moduleName;
    protected $modelName;
    protected $formName;
    protected $actionName;
    protected $data;
    protected $viewName;

    public function onDispatch(MvcEvent $e)
    {
        $this->layout('layout/admin');

        $auth = new AuthenticationService();

        if (!$auth->hasIdentity()) {
            $this->redirect()->toRoute('admin/default', array('controller' => 'login', 'action' => 'index'));
        } else {
            $user = $auth->getIdentity();
        }

        $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
        $viewModel->user = $user;

        return parent::onDispatch($e);
    }

    public function init()
    {
        $this->model    = $this->getServiceLocator()->get('ModelGateway')->getModel($this->modelName);
        $this->form     = new $this->formName();
        $this->view     = new ViewModel();

        if ($this->flashMessenger()->hasMessages('msgInfo')) {
            $messages = $this->flashMessenger()->getMessages('msgInfo');
            $this->msgInfo = $messages[0];
        }

        if ($this->flashMessenger()->hasMessages('msgError')) {
            $messages = $this->flashMessenger()->getMessages('msgError');
            $this->msgError = $messages[0];
        }

        if ($this->flashMessenger()->hasMessages('msgInfoLine')) {
            $messages = $this->flashMessenger()->getMessages('msgInfoLine');
            $this->msgInfoLine = $messages[0];
        }

        if ($this->flashMessenger()->hasMessages('msgErrorLine')) {
            $messages = $this->flashMessenger()->getMessages('msgErrorLine');
            $this->msgErrorLine = $messages[0];
        }
    }

    public function viewModel()
    {
        $view = new ViewModel();

        $viewVariables = [];
        $viewVariables['module']        = $this->module;
        $viewVariables['moduleName']    = $this->moduleName;

        $viewVariables['actionName']  = $this->actionName;

        $viewVariables['msgError']      = $this->msgError;
        $viewVariables['msgInfo']       = $this->msgInfo;
        $viewVariables['msgErrorLine']  = $this->msgErrorLine;
        $viewVariables['msgInfoLine']   = $this->msgInfoLine;

        $viewVariables['form']          = $this->form;

        $viewVariables['data']          = $this->data;
        $viewVariables['viewName']      = $this->viewName;

        $this->view->setVariables($viewVariables);
        return $this->view;
    }
}
