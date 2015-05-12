<?php
class Email_reader {

    // imap server connection

    public $conn;
    // inbox storage and inbox message count

    public $inbox;
    private $msg_cnt;
    // email login credentials

    private $server;
    private $user;
    private $pass;
    private $port; // adjust according to server settings

    // connect to the server and get the inbox emails

    function __construct() {
        $this->server = 'imap.gmail.com';
        $this->port = 993;
    }
    
    public function SetValueCnn($server, $user, $pass, $port)
    {
        $this->server = $server;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
    }

    // close the server connection
    function close() {

        $this->inbox = array();

        $this->msg_cnt = 0;

        imap_close($this->conn);
    }
    function connect() {
        if(empty($this->server) OR empty($this->user) OR empty($this->pass) OR empty($this->port))
        {
            return FALSE;
        }
        $this->conn = imap_open('{'.$this->server.':'.$this->port.'/imap/ssl/novalidate-cert}INBOX', $this->user, $this->pass);
        if($this->conn != FALSE)
        {
            return true;
        }
        else
        {
            return FALSE;
        }
    }

    // move the message to a new folder

    function move($msg_index, $folder = 'INBOX.Processed') {

        // move on server

        imap_mail_move($this->conn, $msg_index, $folder);

        imap_expunge($this->conn);



        // re-read the inbox

        $this->inbox();
    }

    // get a specific message (1 = first email, 2 = second email, etc.)

    function get($msg_index = NULL) {

        if (count($this->inbox) <= 0) {

            return array();
        } elseif (!is_null($msg_index) && isset($this->inbox[$msg_index])) {

            return $this->inbox[$msg_index];
        }



        return $this->inbox[0];
    }

    // read the inbox

    function inbox() {

        $this->msg_cnt = imap_num_msg($this->conn);

        $in = array();

        for ($i = 1; $i <= $this->msg_cnt; $i++) {

            $in[] = array(
                'index' => $i,
                'header' => imap_headerinfo($this->conn, $i),
                'body' => imap_body($this->conn, $i),
                'structure' => imap_fetchstructure($this->conn, $i)
            );
        }


        $this->inbox = $in;
    }
    
    public function search_mail($criteria)
    {
        $arr_index = imap_search($this->conn, $criteria);
        return $arr_index;
    }
    
    public function get_file_attach($mindex, $dir_file = '')
    {
        if($dir_file == '')
        {
            $dir_file = 'email_store';
        }
        $file_name = '';
        $body = imap_fetchstructure($this->conn, $mindex);
        $att = count($body->parts);
        if ($att >= 2) {
            for ($a = 0; $a < $att; $a++) {
                if (isset($body->parts[$a]->disposition) && $body->parts[$a]->disposition == 'ATTACHMENT') {
                    $file_content = imap_base64(imap_fetchbody($this->conn, $mindex, $a + 1));
                    $file_name    = strval($body->parts[$a]->dparameters[0]->value);
                    
                    if (!file_exists($dir_file)) 
                    {
                        mkdir($dir_file,0777);
                    }
                    file_put_contents($dir_file.DS.$file_name, $file_content);
                }
            }
        }
    }
}
?>
