<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php

class MailSender
{
    private $mailer;
    private $message;
    private $log_dir = _CONTS_MAIL_LOG_DIR;
    
    public $smtp_server;
    public $smtp_port;
    public $smtp_ssl;
    public $smtp_account;
    public $smtp_account_name;
    public $smtp_password;
    public $subject = ' ';
    
    public function __construct()
    {
        require_once (SERVER_ROOT . 'libs/Swift/lib/swift_required.php');
        require_once (SERVER_ROOT . 'libs/Log.class.php');
        
        //lay mail theo system config
        $this->smtp_server       = _CONST_SMTP_SERVER;
        $this->smtp_port         = _CONST_SMTP_PORT;
        $this->smtp_ssl          = _CONST_SMTP_SSL;
        $this->smtp_account      = _CONST_SMTP_ACCOUNT;
        $this->smtp_account_name = _CONST_SMTP_ACCOUNT_NAME;
        $this->smtp_password     = _CONST_SMTP_PASSWORD;
        
        //tao instance
        $ssl = $this->smtp_ssl ? 'ssl' : NULL;
        $transport = Swift_SmtpTransport::newInstance($this->smtp_server,$this->smtp_port,$ssl);
        $transport->setUsername($this->smtp_account);
        $transport->setPassword($this->smtp_password);
        $this->mailer  = Swift_Mailer::newInstance($transport); 
        $this->message = Swift_Message::newInstance($this->subject . Date('d-m-Y H:i:s'));
    }
    /**
     * khoi tao lai cac bien gui mail quan trong khi NSD da config tham so
     */
    public function init()
    {
        $ssl = $this->smtp_ssl ? 'ssl' : NULL;
        $transport = Swift_SmtpTransport::newInstance($this->smtp_server,$this->smtp_port,$ssl);
        $transport->setUsername($this->smtp_account);
        $transport->setPassword($this->smtp_password);
        $this->mailer  = Swift_Mailer::newInstance($transport); 
        $this->message = Swift_Message::newInstance($this->subject);
    }
    
    /**
     * gui file dinh kem tu server
     * @param type $path
     * @param type $type
     */
    public function Attach_file($path,$type=null)
    {
        $attachment = Swift_Attachment::fromPath($path,$type);
        $this->message->attach($attachment);
    }
    
    /**
     * gui file dinh kem dang on the fly (ko can tao file tren server)
     * @param type $data   -  Du lieu trong file
     * @param type $file_name   - ten file
     * @param type $type   - loai file 
     */
    public function OnTheFly_Attach($data,$file_name,$type = 'text/xml')
    {
        $attachment = Swift_Attachment::newInstance($data, $file_name, 'text/xml');
        $this->message->attach($attachment);
    }
    
    /**
     * gui mail
     * @param type $to  - nguoi nhan mail
     * @param type $body - noi dung mail
     * @param type $body_type  - dang hien thi noi dung
     */
    public function SendMail($to,$body,$body_type = 'text/plain')
    {
        //Send mail
        $this->message->setBody($body, $body_type);
        $this->message->setFrom($this->smtp_account);
        $this->message->addTo($to);
        $result = $this->mailer->send($this->message);//TRUE - FALSE
        
        //log 	
        $v_send_message = 'Ngay ' .  Date('d-m-Y') . ' gui mail cho '.$to.' ERROR';
        if ($result){
            $v_send_message = 'Ngay ' .  Date('d-m-Y') . ' gui mail cho '.$to.' THANH CONG';
        }
        $log = new Log($this->log_dir, 'mail_log', 'Nhat ky gui mail');
        $log->logThis('f:nl'); //writes a new line, "\n"
        $log->logThis($log->get_formatted_date() . ' ' . $v_send_message);
        
        return $result;
    }
}
