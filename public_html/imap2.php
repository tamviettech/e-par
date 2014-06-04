<?php
//require extension=php_imap.dll

set_time_limit(0); 
ini_set('date.timezone', 'Asia/Ho_Chi_Minh');
define('DS', DIRECTORY_SEPARATOR);
define('SERVER_ROOT', dirname(__FILE__) . DS);

require_once SERVER_ROOT . 'config.php';
require_once SERVER_ROOT . 'libs' . DS . 'adodb5' . DS . 'adodb.inc.php';
 
//Gmail Account
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'motcuacaptinh01@gmail.com'; 
$password = 'epar2010';

// try to connect
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
 
//Get newest mail
$emails = imap_search($inbox,'SUBJECT "ho so lien thong" UNDELETED');
//$emails = imap_search($inbox,'ALL');
 
//Limit
$max_emails = 16;

/* if any emails found, iterate through each email */
if($emails) 
{
    //create database connection
    $adodb = ADONewConnection(CONST_MYSQL_DSN) or die('Cannot connect to MySQL Database Server!');
    mysql_set_charset('utf8');
    
    $count = 1;
 
    //newest first
    rsort($emails);
 
    /* for every email... */
    foreach($emails as $email_number) 
    {
        /* get information specific to this email */
        $overview = imap_fetch_overview($inbox, $email_number, 0);
        
        //header object
        $header_obj = imap_headerinfo($inbox, $email_number);
        $v_message_id = $header_obj->message_id;
        $v_from_email =  $header_obj->from[0]->mailbox . '@' .  $header_obj->from[0]->host;
 
        //mail body
        $message = imap_fetchbody($inbox, $email_number, 2);
 
        // get mail structure
        $structure = imap_fetchstructure($inbox, $email_number);
 
        $attachments = array();
 
        // if any attachments found... 
        if(isset($structure->parts) && count($structure->parts)) 
        {
            for($i = 0; $i < count($structure->parts); $i++) 
            {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );
 
                if($structure->parts[$i]->ifdparameters) 
                {
                    foreach($structure->parts[$i]->dparameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'filename') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }
 
                if($structure->parts[$i]->ifparameters) 
                {
                    foreach($structure->parts[$i]->parameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'name') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }
 
                if($attachments[$i]['is_attachment']) 
                {
                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);
 
                    /* 4 = QUOTED-PRINTABLE encoding */
                    if($structure->parts[$i]->encoding == 3) 
                    { 
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    }
                    /* 3 = BASE64 encoding */
                    elseif($structure->parts[$i]->encoding == 4) 
                    { 
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
            }
        }// endif found attachment
 
        /* iterate through each attachment and save it */
        foreach($attachments as $attachment)
        {
            if($attachment['is_attachment'] == 1)
            {
                $filename = $attachment['name'];
                if(empty($filename)) 
                {
                    $filename = $attachment['filename'];
                }
 
                if(empty($filename))
                {
                    $filename = time() . '.dat';
                }
 
                //Save the attachment
                $v_attachment_content = $attachment['attachment'];
                
                //Save To database
                $v_xml_record_info = '';
                $v_xml_record_data = '';
                $v_xml_form_struct = '';
                $v_record_no = '';
                if ($filename == 'xml_record_info.xml')
                {
                    $v_xml_record_info = $v_attachment_content;
                    $v_record_no = get_xml_value(simplexml_load_string($v_xml_record_info), '//record_no');
                }
                if ($filename == 'xml_record_data.xml')
                {
                    $v_xml_record_data = $v_attachment_content;
                }
                if ($filename == 'form_struct.xml')
                {
                    $v_xml_form_struct = $v_attachment_content;
                }
                
                $stmt = 'Insert Into t_r3_co_record (
                                C_RECORD_NO, 
                                C_FROM_EMAIL, 
                                C_XML_RECORD_INFO, 
                                C_XML_RECORD_DATA, 
                                C_XML_FORM_STRUCT, 
                                C_STATUS, 
                                C_IN_GOING_DATE, 
                                )
                        values(
                                ?, 
                                ?, 
                                ?, 
                                ?, 
                                ?, 
                                ?, 
                                Now()
                        ); ';
                $params = array(
                    $v_record_no,
                    $v_from_email,
                    $v_xml_record_info,
                    $v_xml_record_data,
                    $v_xml_form_struct,
                    0
                );
                $adodb->Execute($stmt, $params);
            }
        }//end foreach attachs
 
        if($count++ >= $max_emails) break;
    }//end foreach email
    
    $adodb->Close();
} //end if have email
 
/* close the connection */
imap_close($inbox);