<?php
/**


This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
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