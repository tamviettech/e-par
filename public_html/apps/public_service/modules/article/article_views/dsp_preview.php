<?php
defined('DS') or die('no direct access');

$v_media_file_name = $arr_single_article['C_FILE_NAME'];
if (file_exists(SERVER_ROOT . 'upload' . DS . $v_media_file_name) && !is_dir(SERVER_ROOT . 'upload' . DS . $v_media_file_name))
{
    $v_img = SITE_ROOT . 'upload/' . $v_media_file_name;
}
else
{
    $v_img                     = '';
}
Session::set('VIDEO_THUMBNAIL', $v_img);

$arr_single_article['C_SUMMARY'] = htmlspecialchars_decode($arr_single_article['C_SUMMARY']);

$arr_single_article['C_CONTENT'] = htmlspecialchars_decode($arr_single_article['C_CONTENT']);

?>
<style>.video_container{height: 400px;}</style>

<div class="article-view">
    <h3><?php echo $arr_single_article['C_TITLE'] ?></h3>
    <h4>(<?php echo $arr_single_article['C_SUB_TITLE'] ?>)</h4>
    <div class="summary-container">
        <b><?php echo $arr_single_article['C_SUMMARY']; ?></b>
    </div>
    <?php error_reporting(E_ALL) ?>
    <?php $pattern = "/\[VIDEO\](.*)\[\/VIDEO\]/i"; ?>
    <?php echo preg_replace_callback($pattern, 'replace_video', $arr_single_article['C_CONTENT'], -1, $count) ?>
    <div>
        <div style="float:right"><b><?php echo $arr_single_article['C_PEN_NAME']; ?></b></div>
    </div>

</div>
<div class="clear"></div>
<div class="button-area">
    <input type="button" class="ButtonBack" value="<?php echo __('goback to list') ?>" onClick="btn_back_onclick();"/>
</div>