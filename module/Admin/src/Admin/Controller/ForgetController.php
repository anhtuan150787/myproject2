<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Admin\Form\ForgetConfirmForm;
use Admin\Form\ForgetForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\MvcEvent;


class ForgetController extends AbstractActionController
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
        $forgetForm = new ForgetForm();

        $userModel      = $this->getServiceLocator()->get('ModelGateway')->getModel('UserModel');
        $forgetModel    = $this->getServiceLocator()->get('ModelGateway')->getModel('ForgetModel');
        $sendMail       = $this->getServiceLocator()->get('SendMail');
        $encrypt        = $this->getServiceLocator()->get('Encrypt');

        $authService = new AuthenticationService();

        if ($authService->hasIdentity()) {
            $this->redirect()->toRoute('admin');
        }

        $request = $this->getRequest();

        if ($request->isPost()) {

            $dataPost = $request->getPost();
            $forgetForm->setInputFilter($forgetModel->getInputFilter());
            $forgetForm->setData($dataPost);

            if ($forgetForm->isValid()) {

                $email = $dataPost['users_email'];

                $userInfoByEmail = $userModel->getUser(['users_email' => $email, 'users_status' => 1]);

                if (!empty($userInfoByEmail) && $userInfoByEmail['users_email'] != '') {

                    $keyRequestNewPassword = $encrypt->HashKeyRequestNewPass();

                    $urlGetNewPassword = $this->url()->fromRoute('admin/default',
                        [
                            'controller' => 'forget',
                            'action' => 'confirm'
                        ],
                        [
                            'query' => [
                                'token' => $keyRequestNewPassword,
                                'email' => $email]
                        ]
                    );
                    $urlGetNewPassword = 'http://' . $_SERVER['SERVER_NAME'] . $urlGetNewPassword;

                    $userModel->savePrimary(array('users_forget_request_key' => $keyRequestNewPassword), $userInfoByEmail['users_id']);

                    try {
                        $sendMail->setTo($email);
                        $sendMail->setSubject('[CMS LOGIN] Lấy lại mật khẩu');
                        $sendMail->setBody('Nhấn vào đây để thiết lập lại Mật khẩu mới: ' . $urlGetNewPassword);
                        $sendMail->send();
                    } catch (\Exception $e) {
                        $writer = $this->getServiceLocator()->get('Writer');
                        $writer->write('SEND MAIL ERROR: ' . $e->getMessage(), 'ERR');
                    }

                    $this->msgInfoLine = 'Yêu cầu lấy lại mật khẩu đã được gửi đến Email: ' . $email;
                } else {
                    $this->msgError = 'Email không tồn tại trong hệ thống hoặc đã bị khóa';
                }
            } else {
                $this->msgError = current(current($forgetForm->getMessages()));
            }
        }

        $view->setVariables([
            'form'      => $forgetForm,
            'msgError'  => $this->msgError,
            'msgInfo'   => $this->msgInfo,
            'msgInfoLine' => $this->msgInfoLine,
            'msgErrorLine' => $this->msgErrorLine,
        ]);

        return $view;
    }

    public function confirmAction()
    {
        $token = $this->params()->fromQuery('token');
        $email = $this->params()->fromQuery('email');

        $view = new ViewModel();

        $encrypt            = $this->getServiceLocator()->get('Encrypt');
        $forgetConfirmModel = $this->getServiceLocator()->get('ModelGateway')->getModel('ForgetConfirmModel');
        $userModel          = $this->getServiceLocator()->get('ModelGateway')->getModel('UserModel');

        $forgetConfirmForm = new ForgetConfirmForm();

        $request = $this->getRequest();
        if ($request->isPost()) {

            $dataPost = $request->getPost();
            $forgetConfirmForm->setInputFilter($forgetConfirmModel->getInputFilter());
            $forgetConfirmForm->setData($dataPost);

            if ($forgetConfirmForm->isValid()) {

                $userInfoByEmail = $userModel->getUser(['users_email' => $email, 'users_status' => 1]);
                $password = $dataPost['users_password'];

                if (!empty($userInfoByEmail) && $userInfoByEmail['users_email'] != '') {

                    if ($userInfoByEmail['users_forget_request_key'] == $token) {

                        $saltHash = $encrypt->HashSalt($password);
                        $newPasswordHash = $encrypt->HashPass($password, $saltHash);

                        $userModel->savePrimary(array(
                            'users_password'            => $newPasswordHash,
                            'users_salt'                => $saltHash,
                            'users_forget_request_key'  => '',
                        ), $userInfoByEmail['users_id']);

                        $this->msgInfoLine = 'Đặt lại Mật khẩu thành công. Đang chuyển sang đăng nhập...';
                        $this->msgInfoLine .= '<script>setTimeout(function(){ window.location.href="' . $this->url()->fromRoute('admin/default', ['controller' => 'login', 'action' => 'index']) . '"; }, 3000);</script>';

                    } else {
                        $this->msgErrorLine = 'Phiên xử lý không hợp lệ';
                    }

                } else {
                    $this->msgErrorLine = 'Email không tồn tại trong hệ thống hoặc đã bị khóa';
                }
            } else {
                $this->msgError = current(current($forgetConfirmForm->getMessages()));
            }
        }

        $view->setVariables([
            'form'      => $forgetConfirmForm,
            'msgError'  => $this->msgError,
            'msgInfo'   => $this->msgInfo,
            'msgErrorLine' => $this->msgErrorLine,
            'msgInfoLine' => $this->msgInfoLine,
        ]);

        return $view;
    }
}
