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
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

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
    protected $viewTemplate;
    protected $status;

    protected $uploadPath;
    protected $imgPrefix;

    protected $pageItemPerCount = 20;
    protected $timeNow;

    protected $user;


    public function onDispatch(MvcEvent $e)
    {
        /*
         * Set layout
         */
        $this->layout('layout/admin');

        $auth = new AuthenticationService();

        /*
         * Check login
         */
        if (!$auth->hasIdentity()) {
            $this->redirect()->toRoute('admin/default', array('controller' => 'login', 'action' => 'index'));
        } else {
            $this->user = $auth->getIdentity();
        }

        /*
         * Check permission
         */
        $this->checkPermission();

        /*
         * Menu
         */
        $groupMenuModel = $e->getApplication()->getServiceManager()->get('ModelGateway')->getModel('GroupMenuModel');
        $menu = $groupMenuModel->getGroupMenu($this->user->group_users_id);

        /*
         * View layout
         */
        $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
        $viewModel->user = $this->user;
        $viewModel->menu = $menu;

        return parent::onDispatch($e);
    }

    public function init()
    {
        $this->timeNow = date('Y-m-d H:i:s');

        if ($this->modelName) {
            $this->model = $this->getServiceLocator()->get('ModelGateway')->getModel($this->modelName);
        }

        if ($this->formName) {
            $this->form = new $this->formName();
        }

        $this->view = new ViewModel();

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

        $viewVariables['actionName']    = $this->actionName;

        $viewVariables['msgError']      = $this->msgError;
        $viewVariables['msgInfo']       = $this->msgInfo;
        $viewVariables['msgErrorLine']  = $this->msgErrorLine;
        $viewVariables['msgInfoLine']   = $this->msgInfoLine;

        $viewVariables['form']          = $this->form;

        $viewVariables['data']          = $this->data;
        $viewVariables['viewName']      = $this->viewName;

        $this->view->setVariables($viewVariables);
        $this->view->setTemplate($this->viewTemplate);
        return $this->view;
    }

    public function formIsValid()
    {
        if ($this->form->isValid()) {
            return true;
        } else {
            $this->msgError = current(current($this->form->getMessages()));
            return false;
        }
    }

    public function countTable($page = null, $total = null)
    {
        if ($total === null) {
            $total = $this->model->countAll();
        }

        $from   = null;
        $to     = null;

        if ($page != null && $total > 0) {
            $page = $page - 1;

            $pageItemPerCount = $this->pageItemPerCount;

            if ($this->pageItemPerCount >= $total) {
                $pageItemPerCount = $total;
            }

            $from = ($pageItemPerCount * $page) + 1;
            $to = $from + $pageItemPerCount - 1;

            if ($to > $total) {
                $to = null;
            }
        } else {
            $from = $total;
        }

        return ['total' => $total, 'from' => $from, 'to' => $to];
    }

    public function checkPermission()
    {
        $auth = new AuthenticationService();
        $acl = new Acl();
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        if ($auth->hasIdentity()) {
            $controllerArr = explode('\\', $this->params()->fromRoute('controller'));
            $controller = strtolower($controllerArr[2]);
            $module = strtolower($controllerArr[0]);
            $action = $this->params()->fromRoute('action');
            $user = $auth->getIdentity();

            $groupUserModel = $this->getServiceLocator()->get('ModelGateway')->getModel('GroupUserModel');
            $groupAclModel  = $this->getServiceLocator()->get('ModelGateway')->getModel('GroupAclModel');


            $groups = $groupUserModel->fetchAll();
            $dataGroups = [];
            foreach ($groups as $v) {
                $dataGroups[] = $v;
            }
            $groups = $dataGroups;


            foreach ($groups as $group) {
                $acl->addRole(new Role($group['group_users_id']));
            }

            //Add Resouces
            $resources = $groupAclModel->getGroupAclForPermission($user->group_users_id);

            $dataResources = [];
            foreach ($resources as $v) {
                $dataResources[] = $v;
            }
            $resources = $dataResources;

            foreach ($resources as $resource) {
                $acl->addResource(new Resource($resource['acl_controller']));
            }

            //Add Permission Allow
            $permissionAllows = $groupAclModel->getGroupAclForPermission($user->group_users_id, false);

            $dataPermissionAllows = [];
            foreach ($permissionAllows as $v) {
                $dataPermissionAllows[] = $v;
            }
            $permissionAllows = $dataPermissionAllows;

            foreach ($permissionAllows as $permission) {
                $acl->allow($permission['group_users_id'], $permission['acl_controller'], $permission['acl_action']);
            }

            $allow = false;
            if ($acl->hasResource($controller)) {
                if ($acl->isAllowed($user->group_users_id, $controller, $action)) {
                    $allow = true;
                }
            }

            if (!$allow) {
                $this->redirect()->toRoute('admin/default', ['controller' => 'message', 'action' => 'index'], ['query' => ['type' => 'deny']]);
                return;
            }
        }
    }

    public function deletePictureAction()
    {
        $this->init();

        $id     = $this->params()->fromPost('id');
        $field  = $this->params()->fromPost('field');
        $url    = $this->params()->fromPost('url');

        $record = $this->model->fetchPrimary($id);

        $tableName = $this->model->tableName;

        unlink('public/' . $url . $record[$field]);

        $params = array();
        $params[$field]     = null;
        $this->model->savePrimary($params, $id);

        echo json_encode(array('return' => 1));

        return $this->response;
    }

    public function uploadImage($imgObject, $field, $id = null)
    {
        $uploadService  = $this->getServiceLocator()->get('UploadFile');

        $pictureNewName = '';
        $pictureInfo = $imgObject;
        if (!empty($pictureInfo) && $pictureInfo['name'] != '') {
            $uploadService->setPath($this->uploadPath);
            $uploadService->setFile($pictureInfo['name']);
            $uploadService->setPrefix($this->imgPrefix);
            $uploadService->upload();
            $pictureNewName = $uploadService->getNewFile();

            if ($id != null) {
                $record = $this->model->fetchPrimary($id);
                unlink($this->uploadPath . '/' . $record[$field]);
            }
        }
        return $pictureNewName;
    }
}
