<?php

if (file_exists('.htaccess'))
{
    header('location:cores/login/');
}
else
{
    header('location:index.php?url=cores/login/');
}
?>