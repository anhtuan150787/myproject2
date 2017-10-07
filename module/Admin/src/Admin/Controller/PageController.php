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

class PageController extends MasterController
{
    public function __construct()
    {
        $this->module       = 'page';
        $this->moduleName   = 'Nội dung trang';
        $this->modelName    = 'PageModel';
        $this->formName     = '\Admin\Form\PageForm';
        $this->status       = ['1' => 'Kích hoạt', '0' => 'Tạm dừng'];
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

        if ($this->getRequest()->isPost()) {
            $paramsSearch = $this->params()->fromPost();

            $search['post_title_search']        = ($paramsSearch['post_title_search'] != '') ? $paramsSearch['post_title_search'] : null;

            $formSearch->setData($search);
            $session->offsetSet('search', $search);
        }

        $result = $this->model->fetchPage(2, $page, $this->pageItemPerCount, $search);


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
        $dataSave['post_body']      = $paramPosts['post_body'];
        $dataSave['post_status']    = $paramPosts['post_status'];
        $dataSave['post_type']      = 2;
        $dataSave['post_tag']       = $paramPosts['post_tag'];

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
        $this->form->get('post_status')->setOptions(array('value_options' => $this->status));
    }
}
