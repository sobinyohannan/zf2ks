<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * 
 * Module Desc : Module for Admin backend
 * Author      : Sobin
 * Date        : 20-11-2013
 * 
 */

namespace Admin;

use Admin\Model\Admin;
use Admin\Model\AdminTable;
use Admin\Model\Position;
use Admin\Model\PositionTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

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
    }
    
    public function init(ModuleManager $moduleManager){        
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
            // This event will only be fired when an ActionController under the MyModule namespace is dispatched.
            $controller = $e->getTarget();            
            $controller->layout('layout/layout');
        }, 100);
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Db\Adapter\Adapter' =>  'Zend\Db\Adapter\AdapterServiceFactory',
                'Admin\Model\AdminTable' => function($sm) {
                    $tableGateway = $sm->get('AdminTableGateway');
                    $table = new AdminTable($tableGateway);
                    return $table;  
                },
                'AdminTableGateway' => function ($sm) {                                        
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Admin());
                    return new TableGateway('ks_admin_accounts', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\PositionTable' => function($sm) {                    
                    $tableGateway = $sm->get('PositionTableGateway');                    
                    $table = new PositionTable($tableGateway);                       
                    return $table;
                },
                'PositionTableGateway' => function($sm) {                                           
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');                    
                    $resultSetPrototype = new ResultSet();                     
                    $resultSetPrototype->setArrayObjectPrototype(new Position());                    
                    $tb = new TableGateway('ks_workouts', $dbAdapter, null, $resultSetPrototype);                    
                    return $tb;
                    //return new TableGateway('ks_workouts', $dbAdapter, null, $resultSetPrototype);
                }
            ),
        );
    }
}
