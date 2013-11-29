<?php
/*
 * Created By : Sobin
 * Date       : 27 Nov 2013
 * @Class     : User actions 
 */

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Form\RegisterForm;
use User\Model\User;

class IndexController extends AbstractActionController
{
    /*
     * @Method  : Display user index page
     * Author   : Sobin
     * Date     : 27 Nov 2013
     * @Params  : none
     */
    public function indexAction()
    {
        // Redirect if user not logged in
        if(!$this->getServiceLocator()->get('AuthServiceUser')->hasIdentity()) {
            $this->redirect()->toRoute('auth',array('action' => 'login'));
        }
        $this->layout()->auth = $this->getServiceLocator()->get('AuthServiceUser')->hasIdentity();
        $this->layout()->logged_in_as = $this->getServiceLocator()->get('AuthServiceUser')->getIdentity();
        
        // Get the free positions details
        $positionTable = $this->getServiceLocator()->get('Admin\Model\PositionTable');
        $positions = $positionTable->fetchAll();        
        $this->layout('layout/main');
        return new ViewModel(array(
            'positions' => $positions
        ));
    }
    
    /*
     * User registration
     * @author       : Sobin
     * @date         : 28 Nov 2013
     * @Params      : None
     */
    public function registerAction() {
        
        $form = new RegisterForm();
        $form->get('submit')->setValue('Save');
        $messages = '';
        // Handle form submit
        $request = $this->getRequest();
        if($request->isPost()) {            
            $data = (array) $request->getPost();            
            $user = new User();            
            $form->setData($data);
            $form->setInputFilter($user->getInputFilter());            
            if($form->isValid()) {
                $data['user_password'] = md5($data['user_password']);
                $user->exchangeArray($data); 
                $result = $this->getServiceLocator()->get('User\Model\UserTable')->saveUser($user);
                if(isset($result['status']) && ($result['status'] == TRUE)) {
                    // Delete the captcha Images after Successful save
                    array_map('unlink', glob("./public/images/captcha/*"));
                    $id = $result['id'];
                    $activation_code = mt_rand();
                    // Save activation code to its table
                    $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                    $sql = new \Zend\Db\Sql\Sql($adapter);
                    $insert = $sql->insert();
                    $insert->into('ks_user_activation_codes');
                    $insert->values(array(
                        'user_id' => $id,
                        'code' => $activation_code
                    ));

                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    // Prepare the Activation Mail
                    $url = $this->url()->fromRoute('user',array('action' => 'activateUser',array('key' => $activation_code, 'email' => $user->user_email)));                    
                    $to = array(
                        'name' => $user->user_first_name.' '.$user->user_surname,
                        'email' => $user->user_email,
                    );
                    $subject = 'KS: User Activation';
                    $message = 'To activate the account visit the page '.$url;
                    $this->sendEmail($to, $subject, $message);
                    $messages = 'Successfully Saved';
                }
                else {
                    $messages = 'The submitted data not saved';
                }
            }
            
        }        
        $view = new ViewModel(array(
            'form' => $form,
            'message' => $messages,
        ));
        return $view;
    }
    
    /*
     * Send Email To The user
     * @author  : Sobin
     * @date    : 29 Nov 2013
     * @params  : to, subject, message
     * @return  : Boolean
     */
    public function sendEmail($to, $subject, $message) {
        /*$mail = new \Zend\Mail\Message();
        $mail->setBody($message);
        //$mail->setFrom('Freeaqingme@example.org', 'Sender\'s name');
        $mail->addTo($to['email'], $to['name']);
        $mail->setSubject($subject);

        $transport = new \Zend\Mail\Transport\Sendmail();
        if($transport->send($mail)) {
            return TRUE;
        }
        else {
            return FALSE;
        }*/
        
        $message = new \Zend\Mail\Message();
        $message->addTo($to)
                ->addFrom('sobin87@gmail.com')
                ->setSubject($subject)
                ->setBody($message);

        // Setup SMTP transport using LOGIN authentication
        $transport = new \Zend\Mail\Transport\Smtp();
        $options   = new \Zend\Mail\Transport\SmtpOptions(array(
            'name'              => 'localhost.localdomain',
            'host'              => '127.0.0.1',
            'connection_class'  => 'login',
            'connection_config' => array(
                'username' => 'user',
                'password' => 'pass',
            ),
        ));
        $transport->setOptions($options);
        $transport->send($message);
    }
}
