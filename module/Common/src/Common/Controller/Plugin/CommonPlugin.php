<?php

/* 
 * Plugin Class for implementing commonly used functions globally
 * @project     : KS
 * @author      : Sobin
 * @date        : 2 Dec 2013
 */

namespace Common\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;

class CommonPlugin extends AbstractPlugin {
    
    public $path_swift;
    
    #MAIL CONFIGURATION
        
    const SMTP_FROM = "sobin.hills@gmail.com";
    const SMTP_FROM_NAME = "KSApp";

    #SMTP AUTH SETTTINGS
    const SMTP_SERVER = "smtp.gmail.com";
    const SMTP_PORT = 465;
    const SMTP_SECURITY = 'ssl';
    const SMTP_USER_NAME = "sobin.hills@gmail.com";
    const SMTP_PASSWORD = "";
    
    public function __construct() {
        
        $this->path_swift = getcwd() . DIRECTORY_SEPARATOR . 'vendor' .
                DIRECTORY_SEPARATOR . 'Swift-5.0.1' .
                DIRECTORY_SEPARATOR . 'lib' .
                DIRECTORY_SEPARATOR;
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
        
        include_once($this->path_swift . "swift_required.php");

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
}
