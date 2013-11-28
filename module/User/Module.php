<?php
/**
 * User Module Class
 * Author   : Sobin
 * 
 */

namespace User;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use User\Model\User;
use User\Model\UserTable;

class Module
{
    public function init(ModuleManager $moduleManager) {        
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();        
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
            // This event will only be fired when an ActionController under the MyModule namespace is dispatched.
            $controller = $e->getTarget();            
            $controller->layout('layout/user');
        }, 100);
    }
    
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);        
        
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
                'User\Model\UserTable' => function($sm) {
                    $tableGateway = $sm->get('UserTableGateway');                    
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                'UserTableGateway' => function ($sm) {                                        
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $hydrator = new \User\Hydrator\TableEntityMapper(
                    array(
                        'user_id' => 'user_id',
                        'user_first_name' => 'user_first_name',
                        'user_surname' => 'user_surname',
                        'user_email' => 'user_email',
                        'user_username' => 'user_username',
                        'user_password' => 'user_password'
                    ));
                    
                    $rowObjectPrototype = new \User\Model\User;
                    $resultSet = new \Zend\Db\ResultSet\HydratingResultSet(
                        $hydrator, $rowObjectPrototype
                    );
                    return new TableGateway('ks_user_general', $dbAdapter, null, $resultSet);
                    //$resultSetPrototype = new ResultSet();
                    //$resultSetPrototype->setArrayObjectPrototype(new User());
                    //return new TableGateway('ks_user_general', $dbAdapter, null, $resultSetPrototype);
                },
            ),            
        );
    }
}
