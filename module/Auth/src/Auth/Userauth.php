<?php

/* 
 *  Login authentication class : Uses zend Authentication
 *  
 */

namespace User;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\Adapter;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;


Class Userauth implements \Zend\Db\Adapter\AdapterInterface
{
    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    protected $authAdapter;
    protected $dbAdapter;
    protected $auth;
    public function __construct()
    {
        $this->auth = new AuthenticationService();        
    }
    
    public function login($username, $password) {
        /*$this->dbAdapter = new \Zend\Db\Adapter\Adapter(array(
                             'driver' => 'Pdo_Mysql',
                             'database' => 'zenddemo',
                             'username' => 'root',
                             'password' => ''                             
                           )
                        );
        $this->authAdapter = new AuthAdapter($this->dbAdapter);
        $this->authAdapter
            ->setTableName('login')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')->setCredentialTreatment('MD5(?)');          
        
        $this->authAdapter
            ->setIdentity($username)
            ->setCredential($password)
        ; */
        $this->auth = $this->getServiceLocator()->get('AuthService');
        $this->auth->getAdapter()->setIdentity($request->getPost('username'))->setCredential($request->getPost('password'));
        
        $result = $this->auth->authenticate();
        //return $result->get;
        return array('code' => $result->getCode(),'Identity' => $result->getIdentity(),'message' => $result->getMessages());
    }
    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate()
    {
        //$auth = new AuthenticationService();        
        $result = $this->auth->authenticate($this->authAdapter);
        return $result;
    }
    
    /*Other Abstract Methods*/
   public function getDriver() {
       
   } 
   public function getPlatform() {
       
   }
    
   public function isLoggedIn() {
       if($this->auth->hasIdentity())
           return true;
       else 
           return false;
   }
   
   // Logout
   public function logout() {
       $this->auth->clearIdentity();
   }
}

