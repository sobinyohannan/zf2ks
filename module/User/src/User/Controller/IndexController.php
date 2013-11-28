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
     * @Method      : User registration
     * Author       : Sobin
     * Date         : 28 Nov 2013
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
            $user->exchangeArray($data);            
            if($this->getServiceLocator()->get('User\Model\UserTable')->saveUser($user) == TRUE) {
                $messages = 'Successfully Saved';
            }
            else {
                $messages = 'The submitted data not saved';
            }
            
        }        
        $view = new ViewModel(array(
            'form' => $form,
            'message' => $messages,
        ));
        return $view;
    }
}
