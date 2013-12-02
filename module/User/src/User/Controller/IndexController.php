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
                    // To Get the Base Url
                    $uri = $this->getRequest()->getUri();
                    $scheme = $uri->getScheme();
                    $host = $uri->getHost();
                    $base = sprintf('%s://%s', $scheme, $host);
                    $url = $base.'/user/activateUser/'.$activation_code.'/'.$id;                    
                    $to = array(
                        'name' => $user->user_first_name.' '.$user->user_surname,
                        'email' => $user->user_email,
                    );
                    $subject = 'KS: User Activation';
                    $message = 'To activate the account visit the page '.$url;                    
                    $this->CommonPlugin()->fncSendSMTPmail($to['email'], '',$subject, $message);
                    //$strTo, $strFrom, $strSubject, $strMailBody, $strFromName = ''
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
     * @param   : $to
     * @param   : $subject
     * @param   : $message
     * @return  : Boolean (Mail sent or Not)
     */
    public function sendEmail($to, $subject, $message) {
        
        $message = new \Zend\Mail\Message();
        $message->addTo('sobin87@gmail.com')
                ->addFrom('sobin.hills@gmail.com')
                ->setSubject('Greetings and Salutations!')
                ->setBody("Sorry, I'm going to be late today!");

        // Setup SMTP transport using LOGIN authentication
        $transport = new \Zend\Mail\Transport\Smtp();
        $options   = new \Zend\Mail\Transport\SmtpOptions(array(
            'name'              => 'localhost.localdomain',
            'host'              => 'smtp.gmail.com',
            'port'              => '465',
            'connection_class'  => 'login',
            'connection_config' => array(
                'username' => 'sobin.hills@gmail.com',
                'password' => 'sobin123#',
            ),
        ));
        $transport->setOptions($options);
        $transport->send($message);        
        
    }  
    
    
    /*
     * Activate user By verifying activation code in the link(Accessed from mail)
     * @author      : Sobin
     * @date        : 2 Dec 2013
     * @param       : none
     *  
     */
    public function activateUserAction() {
        
        $status = false;
        $message = '';
        $key = $this->params()->fromRoute('key',0);
        $id = (int) $this->params()->fromRoute('id');
        // Check whether this code exists and within 40 days of registration
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');              
        $sql = new \Zend\Db\Sql\Sql($adapter);
        $select = $sql->select();
        $select->from('ks_user_activation_codes');
        $select->where(array(
            'user_id' => $id,
            'code'    => $key,
        ));
        
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        
        if(count($results) > 0) {
            
            // Fetch the result and check the validity expired   
            $expiry_ts = time() - 40*24*60*60;            
            foreach($results as $r) {
                $date = $r['createdOn'];
                $date_ts = strtotime($date);
                if($date_ts >= $expiry_ts) {
                    // Update the mail_status in the user_general table to 1
                    $this->getServiceLocator()->get('User\Model\UserTable')->activateUser($id);                    
                }
                
            }
            
            // Delete this key info from the table
            $sql = new \Zend\Db\Sql\Sql($adapter);
            $select = $sql->delete();
            $select->from('ks_user_activation_codes');
            $select->where(array(
                'user_id' => $id,
                'code'    => $key,
            ));

            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            
            $status = true;
            $message = 'User Activated';
            
        }
        else {
            $status = false;
            $message = 'User Not Activated';
        }
        
        $view = new ViewModel(array(
            'status' => $status,
            'message' => $message,
        ));
        return $view;
    }    
    
}
