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

class WebsiteGeneralController extends MasterController
{
    public function __construct()
    {
        $this->module       = 'website-general';
        $this->moduleName   = 'Cấu hình chung';
        $this->modelName    = 'WebsiteGeneralModel';
        $this->formName     = '\Admin\Form\WebsiteGeneralForm';
        $this->uploadPath   = 'public/pictures/websites';
        $this->imgPrefix    = 'favicon_';
    }

    public function indexAction()
    {
        $this->redirect()->toRoute('admin');

        return $this->response;
    }

    public function editAction()
    {
        $this->init();

        $uploadService  = $this->getServiceLocator()->get('UploadFile');

        $this->actionName = 'Cập nhật';

        $id = 1;
        $record = $this->model->fetchPrimary($id);

        if ($this->getRequest()->isPost()) {

            $paramPosts = $this->params()->fromPost();

            $dataSave = [];

            $pictureUploadName = $this->uploadImage($this->params()->fromFiles('website_general_favicon'), 'website_general_favicon', $id);
            if ($pictureUploadName != '') {
                $dataSave['website_general_favicon'] = $pictureUploadName;
            }

            $dataSave['website_general_title']      = $paramPosts['website_general_title'];
            $dataSave['website_general_keyword']    = $paramPosts['website_general_keyword'];
            $dataSave['website_general_description']= $paramPosts['website_general_description'];

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
