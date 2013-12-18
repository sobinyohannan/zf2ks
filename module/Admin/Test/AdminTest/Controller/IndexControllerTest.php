<?php

/**
 * Test file for IndexController
 * @author Sobin Yohannan
 * @date    12/12/2013
 */

namespace AdminTest\Controller;

//use AdminTest\Bootstrap;
/*use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Admin\Controller\IndexController;
use \Zend\Http\Request;
use \Zend\Http\Response;
use \Zend\Mvc\MvcEvent;
use \Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;*/

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
/*use Admin\Controller;*/
use Auth;
use Zend\ServiceManager\ServiceManager;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;
    
    private   $controllerName;
    protected $authService;
    protected function setController($controller, $controllerName)
    {
        $this->controller = $controller;
        $this->controllerName = $controllerName;
    }
    
    protected $traceError = true;
    protected function setUp()
    {        
        $this->setApplicationConfig(
                //D:/wamp/www/KS/Modules/Admin/Test/AdminTest/Controller
            include 'config/application.config.php'
        );
        parent::setUp();        
        //$this->controller = new \Admin\Controller\IndexController();
        
        //$serviceManager = Bootstrap::getServiceManager();
        /*$this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => $this->controllerName));
        $this->event      = new MvcEvent();
        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : array();
        $router = HttpRouter::factory($routerConfig);
 
        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        //$this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
 
        $this->setApplicationConfig($serviceManager->get('ApplicationConfig')); */       
        
    }
    
    /**
     * Mock Login method
     * @from http://www.afewmorelines.com/mocking-user-identities-in-zf2-action-controller-unit-tests/
     */
    public function mockLogin() {
        
        $userSessionModel = new \Zend\Authentication\Storage\Session(null,'name');
        $userSessionModel->write('Sobin');
        //$userSessionModel->setName('Sobin');
        
        $this->authService = $this->getMock("Zend\Authentication\AuthenticationService");
        $this->authService->expects($this->any())->method('getIdentity')->will($this->returnValue($userSessionModel));
        
        $this->authService->expects($this->any())->method('hasIdentity')->will($this->returnValue(true));
        
        $sm = new \Zend\ServiceManager\ServiceManager();
        $sm->setAllowOverride(true);
        $sm->setService('AuthService', $this->authService);        
        
        //$this->authService = $authService;
        
        //$auth = new \Auth\Controller\IndexController();
        //$authService = $auth->getAuthService();        
        //$sm->setAllowOverride(true);
        //$sm->setService('Zend\Authentication\AuthenticationService', $authService);
        
        //$serviceLocator = $auth->getServiceLocator();
        //$serviceLocator->setAllowOverride(true);
        //$serviceLocator->setService('Zend\Authentication\AuthenticationService', $authService);
    }
    
    protected function setServiceLocator($serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function setEvent($event) {
        $this->event = $event;
    }
    
    /**
     * Test index can be accessed
     * @author Sobin 
     */
    public function testIndexActionCanBeAccessed()
    {        
        $authMock = $this->getMockBuilder('Zend\Authentication\AuthenticationService')
                            ->disableOriginalConstructor()
                            ->getMock();

        $authMock->expects($this->any())
                        ->method('hasIdentity')
                        ->will($this->returnValue(true));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('AuthService', $authMock);
    
        $result = $this->dispatch('/admin/index');
        $this->assertResponseStatusCode(200);
        /*if(!$this->authService->hasIdentity()) {
            echo "Inside If";
            $this->assertResponseStatusCode(200,"Logged In");
        }
        else {
            echo "Inside Else";
            $this->assertResponseStatusCode(302,"Not Logged in");
        }*/
    }
    
}

