<?php
$v_back = $_GET['b'];
if (file_exists('.htaccess'))
{
    header('location:cores/login/do_logout?b='. $v_back);
}
else
{
    header('location:index.php?url=cores/login/do_logout&b='. $v_back);
}
?>