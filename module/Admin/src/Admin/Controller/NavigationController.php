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

class NavigationController extends MasterController
{
    public function __construct()
    {
        $this->module       = 'navigation';
        $this->moduleName   = 'Liên kết';
        $this->modelName    = 'NavigationModel';
        $this->formName     = '\Admin\Form\NavigationForm';
        $this->status       = ['1' => 'Kích hoạt', '0' => 'Tạm dừng'];
        $this->navigationTypes = [
            1 => 'Nội dung trang',
            2 => 'Bài viết',
            3 => 'Danh mục bài viết',
            4 => 'Liên kết ngoài',
        ];
    }

    public function indexAction()
    {
        $this->init();

        $groupNavigationId  = $this->params()->fromQuery('group_navigation_id');
        $id       = $this->params()->fromQuery('id', null);

        //Group navigation
        $groupNavigationModel = $this->getServiceLocator()->get('ModelGateway')->getModel('GroupNavigationModel');
        $groupNavigation = $groupNavigationModel->fetchPrimary($groupNavigationId);

        //Navigation parent
        $navigationParent = $this->model->getNavigations($groupNavigationId);

        //Post category
        $postCategoryModel = $this->getServiceLocator()->get('ModelGateway')->getModel('PostCategoryModel');
        $postCategory = $postCategoryModel->getPostCategories();

        //Page
        $postModel = $this->getServiceLocator()->get('ModelGateway')->getModel('PostModel');
        $page = $postModel->fetchWhere(' post_type = 2 ');

        //Post
        $post = $postModel->fetchWhere(' post_type = 1', 5);

        if ($id != null) {
            $record = $this->model->fetchPrimary($id);
            $this->data['record'] = $record;
        }

        $this->data['postCategory']     = $postCategory;
        $this->data['page']             = $page;
        $this->data['post']             = $post;
        $this->data['navigationParent'] = $navigationParent;
        $this->data['navigation']       = $navigationParent;
        $this->data['navigationTypes']  = $this->navigationTypes;
        $this->data['id']               = $id;
        $this->data['groupNavigation']  = $groupNavigation;

        $this->viewName = 'admin/' . $this->module . '/index.phtml';
        $this->viewTemplate = 'partial/table_simple.phtml';

        return $this->viewModel();
    }

    public function addAction()
    {
        $this->init();

        $this->save();

        $paramPosts = $this->params()->fromPost();

        $this->flashMessenger()->addMessage('Thêm mới thành công', 'msgInfo');
        $this->redirect()->toRoute('admin/default', ['controller' => $this->module, 'action' => 'index'], ['query' => ['group_navigation_id' => $paramPosts['group_navigation_id']]]);

        return $this->response();
    }

    public function editAction()
    {
        $this->init();

        $paramPosts = $this->params()->fromPost();

        $this->save($paramPosts['id']);

        $this->flashMessenger()->addMessage('Cập nhật thành công', 'msgInfo');
        $this->redirect()->toRoute('admin/default', ['controller' => $this->module, 'action' => 'index'], ['query' => ['group_navigation_id' => $paramPosts['group_navigation_id']]]);

        return $this->viewModel();
    }

    public function save($id = null)
    {
        $paramPosts = $this->params()->fromPost();

        $dataSave = [];
        $dataSave['navigation_name']        = $paramPosts['navigation_name'];
        $dataSave['navigation_url_id']      = $paramPosts['navigation_url_id'];
        $dataSave['navigation_url_name']    = $paramPosts['navigation_url_name'];
        $dataSave['navigation_position']    = $paramPosts['navigation_position'];
        $dataSave['navigation_parent']      = $paramPosts['navigation_parent'];
        $dataSave['navigation_type']        = $paramPosts['navigation_type'];
        $dataSave['group_navigation_id']    = $paramPosts['group_navigation_id'];
        $dataSave['navigation_status']      = 1;

        $this->model->savePrimary($dataSave, $id);
    }


    public function deleteAction()
    {
        $this->init();

        $groupNavigationId = $this->params()->fromQuery('group_navigation_id');

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
        $this->redirect()->toRoute('admin/default', ['controller' => $this->module, 'action' => 'index'], ['query' => ['group_navigation_id' => $groupNavigationId]]);

        return $this->response();
    }
}
