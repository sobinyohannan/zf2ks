<?php

/* 
 * AuthModule : IndexController
 * 
 */
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Model\Auth;
use Auth\Form\LoginForm;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

//use Auth\AuthAuth;
Class IndexController extends AbstractActionController
{
    protected $authservice;
    protected $storage;
    public function getAuthService()
    {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthServiceUser');
        }
        return $this->authservice;
    }
    public function getSessionStorage() {
        
        if(!$this->storage) {
            $this->storage = $this->getServiceLocator()->get('Auth\Model\MyAuthStorage');            
        }
        return $this->storage;
    }
    public function loginAction() {
               
       //$this->layout('layout/main');
       if($this->getAuthService()->hasIdentity()) {
           $this->redirect()->toRoute('user',array('action' => 'index'));
       }       
       $form = new LoginForm();
       $form->get('submit')->setValue('Login');
       // Handle Login submit here
       //$blogTable   =   $this->getBlogTable();
       $request = $this->getRequest();        
       if($request->isPost()) {        
           $login = new Auth();
           $form->setInputFilter($login->getInputFilter());
           $form->setData($request->getPost());
           if($form->isValid()) {                            
                           
               $this->getAuthService()->getAdapter()->setIdentity($request->getPost('username'))->setCredential($request->getPost('password'));

               $result = $this->getAuthService()->authenticate();
               //return $result->get;
               $result = array('code' => $result->getCode(),'Identity' => $result->getIdentity(),'message' => $result->getMessages());
               if($result['code'] === 1) {
                    // Set remember me
                    if($request->getPost('remember-me') == 1) {
                        $this->getSessionStorage()->setRememberMe(1);
                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    $this->getAuthService()->getStorage()->write($request->getPost('username'));
                    $this->redirect()->toRoute('user');                       
               }
                    
                else {
                    $return['messages'] = $this->setFlashMessage('Login Failed : '.$result['message'][0]);                    
                }
           }
           else {
               $return['messages'] = $this->setFlashMessage('Fill up username and password');               
           }
       }
       $return['form'] = $form;
       return $return;
    }
    
    function setFlashMessage($message) {
        
        $this->flashMessenger()->clearCurrentMessages();
        $this->flashMessenger()->addMessage($message);        
        return $this->flashMessenger()->getCurrentMessages();        
    }
    // Logout
    public function logoutAction() {
            
       $this->getAuthService()->clearIdentity();
       $this->getSessionStorage()->forgetMe();
       $this->redirect()->toRoute('auth',array('action' => 'login'));
    }
}



