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
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

$pop_win = get_request_var('pop_win','');
$pop_win = ($pop_win == '1')?'_pop_win':'';

$this->template->title = 'Quản lý tài liệu';
$this->template->display('dsp_header'.$pop_win.'.php');
?>
<script>
    var SITE_ROOT = '<?php echo SITE_ROOT;?>';
    var CONST_PERMIT_DELETE_MEDIA = '<?php echo CONST_PERMIT_DELETE_MEDIA;?>';
    var CONST_PERMIT_UPLOAD_MEDIA = '<?php echo CONST_PERMIT_UPLOAD_MEDIA;?>';
</script>
<!--include tree view-->
<link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT ?>public/js/jquery/jquery.treeview.css"/>
<script src="<?php echo SITE_ROOT ?>public/js/jquery/jquery.treeview.js"></script>

<!--valid date-->
<script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery.validate.js"></script>

<!--slim scroll-->
<script src="<?php echo SITE_ROOT ?>public/js/jquery/jquery.slimscroll.min.js"></script>

<?php echo $html_content;?>

<?php $this->template->display('dsp_footer'.$pop_win.'.php');