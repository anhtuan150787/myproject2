<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Admin\Form\PostSearchForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class PostController extends MasterController
{
    public function __construct()
    {
        $this->module       = 'post';
        $this->moduleName   = 'Bài viết';
        $this->modelName    = 'PostModel';
        $this->formName     = '\Admin\Form\PostForm';
        $this->status       = ['1' => 'Kích hoạt', '0' => 'Tạm dừng'];
        $this->uploadPath   = 'public/pictures/posts';
        $this->imgPrefix    = 'post_';
    }

    public function indexAction()
    {
        $this->init();

        $session = new Container();

        if (!isset($_GET['page'])) {
            $session->offsetUnset('search');
        }

        if ($session->offsetExists('search')) {
            $search = $session->offsetGet('search');
        }

        $page = $this->params()->fromQuery('page', 1);

        $formSearch = new PostSearchForm();
        $formSearch = $this->setFormPostCategoryOptions($formSearch);


        if ($this->getRequest()->isPost()) {
            $paramsSearch = $this->params()->fromPost();

            $search['post_title_search']        = ($paramsSearch['post_title_search'] != '') ? $paramsSearch['post_title_search'] : null;
            $search['post_category_search']     = ($paramsSearch['post_category_search'] != '') ? $paramsSearch['post_category_search'] : null;
            $search['post_date_from_search']    = ($paramsSearch['post_date_from_search'] != '') ? $paramsSearch['post_date_from_search'] : null;
            $search['post_date_to_search']      = ($paramsSearch['post_date_to_search'] != '') ? $paramsSearch['post_date_to_search'] : null;

            $formSearch->setData($search);
            $session->offsetSet('search', $search);
        }

        $result = $this->model->fetchPage(1, $page, $this->pageItemPerCount, $search);


        $this->data['records']      = $result['paging'];
        $this->data['data']         = $result['data'];
        $this->data['paginator']    = true;
        $this->data['status']       = $this->status;
        $this->data['countTable']   = $this->countTable($page, $result['total']);
        $this->data['formSearch']   = $formSearch;

        $this->data['total']        = $result['total'];
        $this->data['pageNo']       = $page;
        $this->data['pageSize']     = $this->pageItemPerCount;

        $this->viewName = 'admin/' . $this->module . '/index.phtml';
        $this->viewTemplate = 'partial/table_paging_special.phtml';

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

        $this->setFormSelectOptions($id);

        $postCategoryDetailModel = $this->getServiceLocator()->get('ModelGateway')->getModel('PostCategoryDetailModel');
        $postCategoryDetail = $postCategoryDetailModel->fetchWhere('post_id = ' . $id);
        $optionsPostCategoryDetail = [];
        foreach ($postCategoryDetail as $k => $v) {
            $optionsPostCategoryDetail = array_merge($optionsPostCategoryDetail, [$v['post_category_id']]);
        }
        $this->form->get('post_category_id')->setValue($optionsPostCategoryDetail);

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
        $paramPosts = $this->params()->fromPost();

        $dataSave = [];

        $pictureUploadName = $this->uploadImage($this->params()->fromFiles('post_picture'), 'post_picture', $id);
        if ($pictureUploadName != '') {
            $dataSave['post_picture'] = $pictureUploadName;
        }

        if ($id == null) {
            $dataSave['post_date_created']  = $this->timeNow;
            $dataSave['post_users_created'] = $this->user->users_id;

            $dataSave['post_date_updated']  = $this->timeNow;
            $dataSave['post_users_updated'] = $this->user->users_id;
        } else {
            $dataSave['post_date_updated']  = $this->timeNow;
            $dataSave['post_users_updated'] = $this->user->users_id;
        }
        $dataSave['post_title']     = $paramPosts['post_title'];
        $dataSave['post_quote']     = $paramPosts['post_quote'];
        $dataSave['post_body']      = $paramPosts['post_body'];
        $dataSave['post_status']    = $paramPosts['post_status'];
        $dataSave['post_type']      = 1;
        $dataSave['post_tag']       = $paramPosts['post_tag'];

        $idLastInsert = $this->model->savePrimary($dataSave, $id);

        $postCategoryDetailModel = $this->getServiceLocator()->get('ModelGateway')->getModel('PostCategoryDetailModel');

        if (!empty($paramPosts['post_category_id'])) {

            if ($id != null) {
                $postCategoryDetailModel->deleteWhere(['post_id' => $id]);
            }

            foreach ($paramPosts['post_category_id'] as $v) {
                $postCategoryDetailModel->savePrimary(['post_id' => $idLastInsert, 'post_category_id' => $v]);
            }
        }

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
                unlink($this->uploadPath . '/' . $record['post_picture']);
                $this->model->deletePrimary($v);
            }
        }

        $this->flashMessenger()->addMessage('Xóa thành công', 'msgInfo');
        $this->redirect()->toRoute('admin/default', ['controller' => $this->module, 'action' => 'index']);

        return $this->response();
    }

    public function setFormSelectOptions()
    {
        $postCategoryModel = $this->getServiceLocator()->get('ModelGateway')->getModel('PostCategoryModel');
        $postCategories = $postCategoryModel->getPostCategories();

        foreach ($postCategories as $k => $v) {
            $optionsPostCategory[$v['post_category_id']] = str_repeat('__', $v['post_category_level']) . ' ' . $v['post_category_name'];
        }

        $this->form->get('post_status')->setOptions(array('value_options' => $this->status));
        $this->form->get('post_category_id')->setOptions(array('value_options' => $optionsPostCategory));
    }

    public function setFormPostCategoryOptions($form)
    {
        $postCategoryModel = $this->getServiceLocator()->get('ModelGateway')->getModel('PostCategoryModel');
        $postCategories = $postCategoryModel->getPostCategories();

        $optionsPostCategory = ['' => '--- Chọn Danh mục ---'];
        foreach ($postCategories as $k => $v) {
            $optionsPostCategory[$v['post_category_id']] = str_repeat('__', $v['post_category_level']) . ' ' . $v['post_category_name'];
        }

        $form->get('post_category_search')->setOptions(array('value_options' => $optionsPostCategory));

        return $form;
    }
}
