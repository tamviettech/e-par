<?php

/**
 * @package record
 * @author tamviettech <goat91@gmail.com>
 * @abstract 
 * Chỉ để thừa kế, gửi mail thông báo cho công dân
 */
require_once SERVER_ROOT . 'libs/Swift/lib/swift_required.php';

abstract class announce_abstract
{

    /**
     *
     * @var string 
     */
    protected $_email_username = _CONST_SMTP_ACCOUNT;

    /**
     *
     * @var string 
     */
    protected $_email_password = _CONST_SMTP_PASSWORD;

    /**
     *
     * @var string 
     */
    protected $_email_account_name = _CONST_SMTP_ACCOUNT_NAME;

    /**
     * Email nhận
     * @var string
     */
    protected $_mail_to;

    /**
     * 
     * @param string $mail_to Địa chỉ nhận
     */
    function __construct($mail_to)
    {
        $this->_mail_to = $mail_to;
    }

    /**
     * String trả về của hàm này làm body của thư
     * @return string
     */
    abstract function message_body();

    /**
     * String trả về của hàm này làm subject của thư
     * @erturn string
     */
    abstract function message_subject();

    function get_mail_to()
    {
        return $this->_mail_to;
    }

    /**
     * Bắt đầu gửi thư
     * @return int 1/0, thành công hay thất bại
     */
    function send()
    {
        $ssl = _CONST_SMTP_SSL ? 'ssl' : null;

        $transport = Swift_SmtpTransport::newInstance(_CONST_SMTP_SERVER, _CONST_SMTP_PORT, $ssl);
        $transport->setUsername($this->_email_username)
                ->setPassword($this->_email_password);
        $mailer    = Swift_Mailer::newInstance($transport);
        if ($this->get_mail_to() && $mailer)
        {
            $message = Swift_Message::newInstance();
            $message->setSubject($this->message_subject())
                    ->setBody($this->message_body())
                    ->setFrom(array($this->_email_username => $this->_email_account_name))
                    ->addTo($this->get_mail_to());

            return $mailer->send($message);
        }
        return 0;
    }

}