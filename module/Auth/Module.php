<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Auth;

use Auth\Model\Auth;
use Auth\Model\AuthTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        //Shared Event Manager
        /*$sharedEventManager = $eventManager->getSharedManager();

        $sharedEventManager->attach('Blog\BlogeventTest', 'testEvent', function($e) {
            var_dump($e);
        }, 100);*/
        
        /*$eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_RENDER, function($e) {
            $flashMessenger = new \Zend\Mvc\Controller\Plugin\FlashMessenger();
            if ($flashMessenger->hasMessages()) {
                $e->getViewModel()->setVariable('flashMessages', $flashMessenger->getMessages());
            }
        });*/ 
        /*$sharedEvents = $eventManager->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
            // This event will only be fired when an ActionController under the MyModule namespace is dispatched.
            $controller = $e->getTarget();
            $controller->layout('layout/main');
        }, 100);*/
        
    }
    
    public function init(ModuleManager $moduleManager){
        //echo "Init in Auth";
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
            // This event will only be fired when an ActionController under the MyModule namespace is dispatched.
            $controller = $e->getTarget();            
            //echo "target controller: ";            
            $controller->layout('layout/auth');
        }, 100);
    }
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
            __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    // get service configuration => configuring model to the service manager
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Db\Adapter\Adapter'   =>  'Zend\Db\Adapter\AdapterServiceFactory',
                'Auth\Model\MyAuthStorage' => function($sm){
                    return new \Auth\Model\MyAuthStorage('KS');  
                },
                'Auth\Model\AuthTable' => function($sm) {
                    $tableGateway = $sm->get('AuthTableGateway');
                    $table = new AuthTable($tableGateway);
                    return $table;  
                },
                'AuthTableGateway' => function ($sm) {                                        
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Blog());
                    return new TableGateway('ks_admin_accounts', $dbAdapter, null, $resultSetPrototype);
                },
                'AuthService' => function($sm) {
		    $dbAdapter      = $sm->get('\Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter, 'ks_admin_accounts','admin_username','admin_password', 'MD5(?)');
		    
		    $authService = new AuthenticationService();
		    $authService->setAdapter($dbTableAuthAdapter);
		    $authService->setStorage($sm->get('Auth\Model\MyAuthStorage'));		     
		    return $authService;
		},
                'Auth\Model\AuthTableUser' => function($sm) {
                    $tableGateway = $sm->get('AuthTableGatewayUser');
                    $table = new AuthTable($tableGateway);
                    return $table;  
                },
                // Auth table gateway for user authentication
                'AuthTableGatewayUser' => function ($sm) {                                        
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Blog());
                    return new TableGateway('ks_user_general', $dbAdapter, null, $resultSetPrototype);
                },
                'AuthServiceUser' => function($sm) {
		    $dbAdapter      = $sm->get('\Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter, 'ks_user_general','user_username','user_password', 'MD5(?)');
		    
		    $authService = new AuthenticationService();
		    $authService->setAdapter($dbTableAuthAdapter);
		    $authService->setStorage($sm->get('Auth\Model\MyAuthStorage'));		     
		    return $authService;
		},
            ),
        );
    }
}
