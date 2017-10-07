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

class UserController extends MasterController
{
    public function __construct()
    {
        $this->module       = 'user';
        $this->moduleName   = 'User';
        $this->modelName    = 'UserModel';
        $this->formName     = '\Admin\Form\UserForm';
        $this->status       = ['1' => 'Kích hoạt', '0' => 'Tạm khóa'];
        $this->uploadPath   = 'public/pictures/users';
        $this->imgPrefix    = 'users_';
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

            $this->form->getInputFilter()->get('users_email')->setRequired(false);
            $this->form->getInputFilter()->get('users_password')->setRequired(false);

            if ($this->formIsValid()) {

                $this->save($id);

                $this->flashMessenger()->addMessage('Cập nhật thành công', 'msgInfo');
                $this->redirect()->toRoute('admin/default', ['controller' => $this->module, 'action' => 'edit'], ['query' => ['id' => $id]]);
            }
        }

        $this->setFormSelectOptions();

        $this->form->get('frmSubmit')->setAttributes(array('value' => $this->actionName));
        $this->form->setData($record);

        $this->data['id'] = $id;
        $this->data['record'] = $record;

        $this->viewName = 'admin/' . $this->module . '/form.phtml';
        $this->viewTemplate = 'partial/form_normal.phtml';

        return $this->viewModel();
    }

    public function save($id = null)
    {
        $encrypt        = $this->getServiceLocator()->get('Encrypt');
        $paramPosts     = $this->params()->fromPost();

        $dataSave = [];

        $pictureUploadName = $this->uploadImage($this->params()->fromFiles('users_picture'), 'users_picture', $id);
        if ($pictureUploadName != '') {
            $dataSave['users_picture'] = $pictureUploadName;
        }

        $dataSave['users_fullname']     = $paramPosts['users_fullname'];
        $dataSave['users_phone']        = $paramPosts['users_phone'];

        if ($paramPosts['users_password'] != '') {
            $saltHash = $encrypt->HashSalt($paramPosts['users_password']);
            $newPasswordHash = $encrypt->HashPass($paramPosts['users_password'], $saltHash);
            $dataSave['users_password'] = $newPasswordHash;
            $dataSave['users_salt']     = $saltHash;
        }

        if ($id == null) {
            $dataSave['users_email']        = $paramPosts['users_email'];
            $dataSave['users_registered'] = date('Y-m-d H:i:s');
        }

        $dataSave['users_status']       = $paramPosts['users_status'];
        $dataSave['group_users_id']     = $paramPosts['group_users_id'];

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
                $record = $this->model->fetchPrimary($v);
                unlink($this->uploadPath . '/' . $record['users_picture']);
                $this->model->deletePrimary($v);
            }
        }

        $this->flashMessenger()->addMessage('Xóa thành công', 'msgInfo');
        $this->redirect()->toRoute('admin/default', ['controller' => $this->module, 'action' => 'index']);

        return $this->response();
    }

    public function setFormSelectOptions()
    {
        $groupUserModel = $this->getServiceLocator()->get('ModelGateway')->getModel('GroupUserModel');

        $optionsGroupAdmin = array('' => '--- Chọn Nhóm ---');
        foreach ($groupUserModel->fetchAll() as $k => $v) {
            $optionsGroupAdmin[$v['group_users_id']] = $v['group_users_name'];
        }

        $this->form->get('group_users_id')->setOptions(array('value_options' => $optionsGroupAdmin));
        $this->form->get('users_status')->setOptions(array('value_options' => $this->status));
    }
}
