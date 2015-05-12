<?php

/**
 * Upload over HTTP protocol
 *
 *
 *
 * @author  Lien Ngo <liennd@gmail.com>
 * @access   public
 * @version 1.0: <2012-06-26>Upload file with real name, no error handle yet.
 */
class zUpload{

    private $_is_error;
    private $_error_message;
    private $_uploaded_file_name;
    private $_limit_file_extension = array('pdf', 'zip', 'rar', 'txt', 'jpg', 'png', 'doc', 'doc', 'docx');

    function __construct()
    {
        $this->_is_error = 1;
    }

    public function set_limit_file_extension($ext_array)
    {
        if (is_array($ext_array))
        {
            $this->_permit_file_extension = $ext_array;
        }
    }


    private function _move($from, $to)
    {
        if (is_file($from))
        {
            $bdata = file_get_contents($from);
            $handle = fopen($to, "wb");
            fwrite($handle, $bdata);
            fclose($handle);
        }
    }

    public function upload_single_file($file_obj='userfile', $upload_dir='./')
    {
        $arr_file       = $_FILES[$file_obj];
        $v_file_name    = $arr_file['name'];
        $v_tmp_name     = $arr_file['tmp_name'];

        //copy
        $v_file_ext = array_pop(explode('.', $v_file_name));
        if (in_array($v_file_ext, $this->_permit_file_extension))
        {
            if (is_file($v_tmp_name))
            {
                $this->_move($v_tmp_name, $upload_dir . $v_file_name);
                return $v_file_name;
            }
        }
        else
        {
            $ret_array['message'] = 'Kiểu file không được chấp nhận';
        }

        return NULL;
    }

    public function upload($file_obj='userfile', $upload_dir='./', $des_file_name_kind='real')
    {
        $ret_array = array('error' => 0,'message' => '','des_file_name' => '');

        $arr_file = $_FILES[$file_obj];
        if (is_array($arr_file['name']))
        {
            //Xy ly Mutil files
            $arr_file_name = $arr_file['name'];
            $arr_file_type = $arr_file['type'];
            $arr_tmp_name   = $arr_file['tmp_name'];

            for ($i=0; $i<sizeof($arr_file_name); $i++)
            {
                $v_file_name    = $arr_file_name[$i];
                $v_file_type    = $arr_file_type[$i];
                $v_tmp_name     = $arr_tmp_name[$i];

                //copy
                if (is_file($v_tmp_name))
                {
                    $v_file_ext = array_pop(explode('.', $v_file_name));
                    echo '<br>$v_file_ext=' . $v_file_ext;
                    $names = array();
                    if (in_array($v_file_ext, $this->_permit_file_extension))
                    {
                        $bdata = file_get_contents($v_tmp_name);
                        $handle = fopen($upload_dir . $v_file_name , "wb");
                        fwrite($handle, $bdata);
                        fclose($handle);

                        $ret_array['error'] = 0;
                        array_push($names, $v_file_name);
                    }
                    else
                    {
                        $ret_array['message'] = 'Kiểu file không được chấp nhận';
                    }
                }

                $ret_array['des_file_name'] = $names;
            }
        }
        else
        {
            $v_file_name    = $arr_file['name'];
            $v_file_type    = $arr_file['type'];
            $v_tmp_name     = $arr_file['tmp_name'];

            //copy
            $v_file_ext = array_pop(explode('.', $v_file_name));
            $names = array();
            if (in_array($v_file_ext, $this->_permit_file_extension))
            {

                if (is_file($v_tmp_name))
                {
                    $bdata = file_get_contents($v_tmp_name);
                    $handle = fopen($upload_dir . $v_file_name , "wb");
                    fwrite($handle, $bdata);
                    fclose($handle);

                    $ret_array['error'] = 0;
                    array_push($names, $v_file_name);
                }
            }
            else
            {
                $ret_array['message'] = 'Kiểu file không được chấp nhận';
            }

                $ret_array['des_file_name'] = $names;

            $ret_array['des_file_name'] = $names;
        }

        return $ret_array;
    }
}
?>
