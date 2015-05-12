<?php
if (file_exists('.htaccess'))
{
    header('location:cores/login/do_logout');
}
else
{
    header('location:index.php?url=cores/login/do_logout');
}
?>