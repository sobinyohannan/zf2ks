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
    
    #MAIL CONFIGURATION
        
    const SMTP_FROM = "sobin.hills@gmail.com";
    const SMTP_FROM_NAME = "KSApp";

    #SMTP AUTH SETTTINGS
    const SMTP_SERVER = "smtp.gmail.com";
    const SMTP_PORT = 465;
    const SMTP_SECURITY = 'ssl';
    const SMTP_USER_NAME = "sobin.hills@gmail.com";
    const SMTP_PASSWORD = "";
    
        
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
                    $this->fncSendSMTPmail($to['email'], '',$subject, $message);
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
     * Send Email Using SMTP by SwiftMailer
     * @author  : Sobin
     * @date    : 29 Nov 2013
     * @param   : $strTo
     * @param   : $strFrom
     * @param   : $strSubject
     * @param   : $strMailBody
     * @param   : $strFromName
     * @param   : $attachments
     * @return  : boolean true or false(mail send or not)
     */
    
    public function fncSendSMTPmail($strTo, $strFrom, $strSubject, $strMailBody, $strFromName = '', $attachments = array()) {
        
        $path_swift = getcwd() . DIRECTORY_SEPARATOR . 'vendor' .
                DIRECTORY_SEPARATOR . 'Swift-5.0.1' .
                DIRECTORY_SEPARATOR . 'lib' .
                DIRECTORY_SEPARATOR;
        include_once($path_swift . "swift_required.php");

        $email = ($strFrom != '') ? $strFrom : self::SMTP_FROM;
        $strFromName = ($strFromName != '') ? $strFromName : self::SMTP_FROM_NAME;


        # Create the Transport
        $transport = \Swift_SmtpTransport::newInstance(self::SMTP_SERVER, self::SMTP_PORT, self::SMTP_SECURITY)
                ->setUsername(self::SMTP_USER_NAME)
                ->setPassword(self::SMTP_PASSWORD);

        \Swift_Preferences::getInstance()->setCharset('ISO-8859-1');
        # Create the Mailer using your created Transport
        $mailer = \Swift_Mailer::newInstance($transport);
        # Create a message
        $message = \Swift_Message::newInstance()
                ->setSubject($strSubject)
                ->setFrom(array($email => $strFromName))
                ->setSender(array($email => $strFromName))
                ->setReturnPath($email)
                ->setTo($strTo)
                ->setReplyTo(array($email => $strFromName))
                ->setBody($strMailBody, 'text/html');

        if (is_array($attachments) && count($attachments) > 0) {

            for ($iCount = 0; $iCount < count($attachments); $iCount++) {
                if (is_file($attachments[$iCount]))
                    $message->attach(\Swift_Attachment::fromPath($attachments[$iCount])->setFilename($attachments[$iCount]));
            }
        }
        $numSent = false;
        try {
            $numSent = $mailer->send($message);
        } catch (Exception $e) {

            echo $e->getMessage();
        }

        if ($numSent) {
            return true;
        } else {
            return false;
        }
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
