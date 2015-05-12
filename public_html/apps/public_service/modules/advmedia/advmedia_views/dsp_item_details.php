<?php
defined('DS') or die();

$finfo     = pathinfo($fname);
$extension = strtolower($finfo['extension']);
$img_ext   = explode(',', str_replace(' ', '', strtolower(EXT_IMAGE)));
if (in_array($extension, $img_ext))
{
    $img = $furl;
}
else
{
    $img     = SITE_ROOT . "public/images/ext/$extension.png";
    $imgpath = SERVER_ROOT . "public" . DS . "images" . DS . "ext" . DS . "$extension.png";
    if (!file_exists($imgpath))
    {
        $img = SITE_ROOT . "public/images/ext/file.png";
    }
}
?>
<img style="max-height: 98%;max-width: 100%;" src="<?php echo $img ?>"/><br/>



