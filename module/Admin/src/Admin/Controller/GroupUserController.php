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

class GroupUserController extends MasterController
{
    public function __construct()
    {
        $this->module       = 'group-user';
        $this->moduleName   = 'Nhóm user';
        $this->modelName    = 'GroupUserModel';
        $this->formName     = '\Admin\Form\GroupUserForm';
        $this->status       = ['1' => 'Kích hoạt', '0' => 'Tạm dừng'];
    }

    public function indexAction()
    {
        $this->init();

        $page = $this->params()->fromQuery('page', 1);

        $records = $this->model->fetchPage($page, $this->pageItemPerCount);

        $this->data['records']      = $records;
        $this->data['paginator']    = true;
        $this->data['status']       = $this->status;
        $this->data['countTable']   = $this->countTable($page);

        $this->viewName = 'admin/' . $this->module . '/index.phtml';
        $this->viewTemplate = 'partial/table_normal.phtml';

        return $this->viewModel();
    }

    public function addAction()
    {
        $this->actionName = 'Thêm';

        $this->init();

        if ($this->getRequest()->isPost()) {

            $dataPost = $this->getRequest()->getPost();
            $this->form->setInputFilter($this->model->getInputFilter());
            $this->form->setData($dataPost);

            if ($this->formIsValid()) {

                $this->save();

                $this->flashMessenger()->addMessage('Thêm mới thành công', 'msgInfo');
                $this->redirect()->toRoute('admin/default', ['controller' => $this->module, 'action' => 'add']);
            }
        }

        $this->setFormSelectOptions();

        $this->viewName = 'admin/' . $this->module . '/form.phtml';
        $this->viewTemplate = 'partial/form_normal.phtml';

        return $this->viewModel();
    }

    public function editAction()
    {
        $this->actionName = 'Cập nhật';

        $this->init();

        $id = $this->params()->fromQuery('id');
        $record = $this->model->fetchPrimary($id);

        if ($this->getRequest()->isPost()) {

            $dataPost = $this->getRequest()->getPost();
            $this->form->setInputFilter($this->model->getInputFilter());
            $this->form->setData($dataPost);

            if ($this->formIsValid()) {

                $this->save($id);

                $this->flashMessenger()->addMessage('Cập nhật thành công', 'msgInfo');
                $this->redirect()->toRoute('admin/default', ['controller' => $this->module, 'action' => 'edit'], ['query' => ['id' => $id]]);
            }
        }

        $this->setFormSelectOptions();

        $this->form->get('frmSubmit')->setAttributes(array('value' => $this->actionName));
        $this->form->setData($record);

        $this->viewName = 'admin/' . $this->module . '/form.phtml';
        $this->viewTemplate = 'partial/form_normal.phtml';

        return $this->viewModel();
    }

    public function save($id = null)
    {
        $paramPosts = $this->params()->fromPost();

        $dataSave = [];
        $dataSave['group_users_name']      = $paramPosts['group_users_name'];
        $dataSave['group_users_status']    = $paramPosts['group_users_status'];

        $this->model->savePrimary($dataSave, $id);
    }

    public function deleteAction()
    {
        $this->init();

        if ($this->getRequest()->isPost()) {
            $id = $this->params()->fromPost('check_item');
        } else {
            $id[] = $this->params()->fromQuery('id');
        }

        if (is_array($id)) {
            foreach($id as $k => $v) {
                $this->model->deletePrimary($v);
            }
        }

        $this->flashMessenger()->addMessage('Xóa thành công', 'msgInfo');
        $this->redirect()->toRoute('admin/default', ['controller' => $this->module, 'action' => 'index']);

        return $this->response();
    }

    public function setFormSelectOptions()
    {
        $this->form->get('group_users_status')->setOptions(array('value_options' => $this->status));
    }

    public function aclAction()
    {
        $this->actionName = 'Cập nhật';

        $this->init();

        $menuModel  = $this->getServiceLocator()->get('ModelGateway')->getModel('MenuModel');
        $groupMenuModel = $this->getServiceLocator()->get('ModelGateway')->getModel('GroupMenuModel');

        $aclModel = $this->getServiceLocator()->get('ModelGateway')->getModel('AclModel');
        $groupAclModel = $this->getServiceLocator()->get('ModelGateway')->getModel('GroupAclModel');

        $id = $this->params()->fromQuery('id');

        if ($this->getRequest()->isPost()) {
            $menus = $this->params()->fromPost('menu');
            $acls = $this->params()->fromPost('acl');

            $groupMenuModel->saveWhere(['group_menu_status' => 0], ['group_users_id' => $id]);
            foreach($menus as $k => $v) {
                $checkExist = $groupMenuModel->checkExistMenu($id, $k);

                if ($checkExist > 0) {
                    $groupMenuModel->saveWhere(['group_menu_status' => $v], ['group_users_id' => $id, 'menu_id' => $k]);
                } else {
                    $groupMenuModel->savePrimary(['group_users_id' => $id, 'menu_id' => $k, 'group_menu_status' => $v]);
                }
            }

            $groupAclModel->saveWhere(['group_acl_status' => 0], ['group_users_id' => $id]);
            foreach($acls as $k => $v) {
                $checkExist = $groupAclModel->checkExistAcl($id, $k);

                if ($checkExist > 0) {
                    $groupAclModel->saveWhere(['group_acl_status' => $v], ['group_users_id' => $id, 'acl_id' => $k]);
                } else {
                    $groupAclModel->savePrimary(['group_users_id' => $id, 'acl_id' => $k, 'group_acl_status' => $v]);
                }
            }

            $this->flashMessenger()->addMessage('Cập nhật thành công', 'msgInfo');
            $this->redirect()->toRoute('admin/default', ['controller' => $this->module, 'action' => 'acl'], ['query' => ['id' => $id]]);
        }

        $groupMenuResults = $groupMenuModel->getGroupMenu($id);
        $groupMenus = [];
        foreach($groupMenuResults as $v) {
            $groupMenus[$v['menu_id']] = $v;
        }

        $menus = $menuModel->getMenus();

        $groupAclResults = $groupAclModel->getGroupAcl($id);
        $groupAcls = [];
        foreach($groupAclResults as $v) {
            $groupAcls[$v['acl_id']] = $v;
        }

        $acls = $aclModel->getAcls();

        $groupInfo = $this->model->fetchPrimary($id);

        $this->data = [
            'groupMenus'    => $groupMenus,
            'menus'         => $menus,
            'groupAcls'     => $groupAcls,
            'acls'          => $acls,
            'groupInfo'     => $groupInfo,
            'module'        => $this->module,
            'status'        => $this->status,
        ];

        $this->form->get('frmSubmit')->setAttributes(array('value' => $this->actionName));

        $this->viewName = 'admin/' . $this->module . '/acl.phtml';
        $this->viewTemplate = 'partial/form_normal.phtml';

        return $this->viewModel();
    }

}
