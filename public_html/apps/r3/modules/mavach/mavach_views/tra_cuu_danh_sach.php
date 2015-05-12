<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
$z = get_request_var('z',0);
if ($z == 1)
{
    echo (gzcompress(json_encode($arr_all_record_min_info))); 
}
else
{
    echo ((json_encode($arr_all_record_min_info)));
}
