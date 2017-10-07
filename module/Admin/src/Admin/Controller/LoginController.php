<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Admin\Form\LoginForm;
use Admin\Model\UserModel;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\Adapter\DbTable;
use Zend\Authentication\Storage\Session;
use Zend\Authentication\AuthenticationService;

class LoginController extends AbstractActionController
{
    private $msgError;
    private $msgInfo;
    private $msgErrorLine;
    private $msgInfoLine;

    public function onDispatch(MvcEvent $e)
    {
        $this->layout('layout/login');
        return parent::onDispatch($e);
    }

    public function indexAction()
    {
        $view = new ViewModel();

        $userModel  = $this->getServiceLocator()->get('ModelGateway')->getModel('UserModel');
        $loginModel = $this->getServiceLocator()->get('ModelGateway')->getModel('LoginModel');
        $dbAdapter  = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $encrypt    = $this->getServiceLocator()->get('Encrypt');

        $authService = new AuthenticationService();
        $loginForm   = new LoginForm();

        if ($authService->hasIdentity()) {
            $this->redirect()->toRoute('admin');
        }

        $request = $this->getRequest();

        if ($request->isPost()) {

            $dataPost = $request->getPost();
            $loginForm->setInputFilter($loginModel->getInputFilter());
            $loginForm->setData($dataPost);

            if ($loginForm->isValid()) {

                $email      = $dataPost['users_email'];
                $password   = $dataPost['users_password'];

                $userInfoByEmail = $userModel->getUser(['users_email' => $email, 'users_status' => 1]);

                $salt = $userInfoByEmail['users_salt'];
                $authAdapter = new DbTable($dbAdapter);
                $authAdapter->setTableName('users')->setIdentityColumn('users_email')->setCredentialColumn('users_password');
                $authAdapter->setIdentity($email)->setCredential($encrypt->HashPass($password, $salt));

                $result = $authAdapter->authenticate();
                $isValid = $result->isValid();

                if ($isValid) {

                    $userModel->savePrimary([
                        'users_login_time' => date('Y-m-d H:i:s'),
                        'users_user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'users_forget_request_key' => '',

                    ], $userInfoByEmail['users_id']);

                    $storage = new Session();
                    $storage->write($authAdapter->getResultRowObject([
                        'users_id',
                        'users_email',
                        'users_fullname',
                        'users_picture',
                        'group_users_id',
                        'users_login_time',
                    ]));
                    $authService->setStorage($storage)->setAdapter($authAdapter);

                    $this->redirect()->toRoute('admin/default', ['controller' => 'index', 'action' => 'index']);

                } else {
                    $this->msgError = 'Thông tin đăng nhập không đúng hoặc tài khoản đã bị khóa';
                }
            } else {
                $this->msgError = current(current($loginForm->getMessages()));
            }
        }

        $view->setVariables([
            'form'      => $loginForm,
            'msgError'  => $this->msgError,
            'msgInfo'   => $this->msgInfo
        ]);

        return $view;
    }
}
