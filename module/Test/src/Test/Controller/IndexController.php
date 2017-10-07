<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Test\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        /*
         * Set layout
         */
        $this->layout('layout/test');

        $postModel = $this->getServiceLocator()->get('ModelGateway')->getModel('PostModel');
        $postCategoryDetailModel = $this->getServiceLocator()->get('ModelGateway')->getModel('PostCategoryDetailModel');

        $get = $this->params()->fromQuery('action');

        if ($get == 'do') {
            for ($i = 0; $i <= 1000000; $i++) {
                $dataSave['post_date_created'] = date('Y-m-d H:i:s');
                $dataSave['post_users_created'] = 3;
                $dataSave['post_date_updated'] = date('Y-m-d H:i:s');
                $dataSave['post_users_updated'] = 3;
                $dataSave['post_title'] = 'Phát hiện hàng tỷ thiết bị Android, Linux và Windows có thể bị tấn công bằng kết nối Bluetooth';
                $dataSave['post_quote'] = 'Phát hiện hàng tỷ thiết bị Android, Linux và Windows có thể bị tấn công bằng kết nối Bluetooth';
                $dataSave['post_body'] = 'Phát hiện hàng tỷ thiết bị Android, Linux và Windows có thể bị tấn công bằng kết nối Bluetooth';
                $dataSave['post_status'] = 1;
                $dataSave['post_type'] = 1;
                $dataSave['post_tag'] = 'test1,test2,test3';


                $idLastInsert = $postModel->savePrimary($dataSave);

                $paramPosts['post_category_id'] = [5, 6, 7];

                if (!empty($paramPosts['post_category_id'])) {

                    foreach ($paramPosts['post_category_id'] as $v) {
                        $postCategoryDetailModel->savePrimary(['post_id' => $idLastInsert, 'post_category_id' => $v]);
                    }
                }
                echo $i . ' | ' . json_encode($dataSave) . "<br>";
                echo '==================================<br>';

            }

        }
        return new ViewModel();
    }

    public function userAction()
    {
        /*
         * Set layout
         */
        $this->layout('layout/test');

        $encrypt = $this->getServiceLocator()->get('Encrypt');
        $userModel = $this->getServiceLocator()->get('ModelGateway')->getModel('UserModel');

        $menuModel  = $this->getServiceLocator()->get('ModelGateway')->getModel('MenuModel');
        $groupMenuModel = $this->getServiceLocator()->get('ModelGateway')->getModel('GroupMenuModel');

        $aclModel = $this->getServiceLocator()->get('ModelGateway')->getModel('AclModel');
        $groupAclModel = $this->getServiceLocator()->get('ModelGateway')->getModel('GroupAclModel');

        $menus = $menuModel->fetchAll();
        $acls = $aclModel->fetchAll();

        $get = $this->params()->fromQuery('action');

        if ($get == 'do') {
            for ($i = 0; $i <= 1000000; $i++) {
                $dataSave['users_picture'] = 'users_1503035528_1910224703_hop-190x120x60mm.png';
                $dataSave['users_fullname'] = 'Fullname_' . microtime(true);
                $dataSave['users_phone'] = '0966554433';

                $pass = 'Fullname_' . microtime(true);
                $saltHash = $encrypt->HashSalt($pass);
                $newPasswordHash = $encrypt->HashPass($pass, $saltHash);
                $dataSave['users_password'] = $newPasswordHash;
                $dataSave['users_salt'] = $saltHash;


                $dataSave['users_email'] = 'Email_' . microtime(true) . '@yahoo.com';
                $dataSave['users_registered'] = date('Y-m-d H:i:s');


                $dataSave['users_status'] = 1;
                $dataSave['group_users_id'] = 1;

                $userModel->savePrimary($dataSave);

                foreach($menus as $k => $v) {
                    $groupMenuModel->savePrimary(['group_users_id' => 1, 'menu_id' => $k, 'group_menu_status' => 1]);
                }

                $groupAclModel->saveWhere(['group_acl_status' => 0], ['group_users_id' => 1]);
                foreach($acls as $k => $v) {
                    $groupAclModel->savePrimary(['group_users_id' => 1, 'acl_id' => $k, 'group_acl_status' => 1]);
                }
            }
        }
        return new ViewModel();
    }
}
