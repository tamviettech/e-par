<?php
defined('DS') or die();
$this->is_service = isset($this->is_service) ? $this->is_service : false;

?>
<style>
    #left, #right{float:left;padding:10px;min-height: 495px;}
    h4{font-size: 13px;}
    #left{width: 25%;background-color: #d2dfe8;}
    #right{width: 69%;background-color: #eeeeee;}
    #filemenu{padding: 5px;}
    #upload{border:4px dashed #ddd; width:80%; height:50px; padding:10px;margin:0 auto;margin-top:10px;margin-bottom: 10px;}
    #uploaded{border: 1px solid #ddd; width:80%; height:70px; padding:10px; overflow-y:auto;margin:0 auto;}
    table{background: rgba(255, 255, 255, 0);}
    table a,a:visited{color:#333;}
    #submenu{background-color: #eeeeee;line-height: 30px;}
    #txt_path{padding: 4px;}
    table tr.selected{background-color: lightsteelblue;}
    #table_container{height: 465px;overflow-y: auto;background-color: white;}
</style>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT ?>public/js/jquery/jquery.treeview.css"/>
<script src="<?php echo SITE_ROOT ?>public/js/jquery/jquery.treeview.js"></script>
<!--upload-->
<script src="<?php echo SITE_ROOT; ?>public/js/mfupload.js" type="text/javascript"></script>

<?php
echo $this->hidden('controller', $this->get_controller_url());
echo $this->hidden('extensions', strtolower(Session::get('media_extensions')));
echo $this->hidden('htaccess', file_exists('.htaccess') ? 1 : '');

echo $this->hidden('hdn_upload', 'upload');
echo $this->hidden('hdn_drag_text', __('{drag text}'));
echo $this->hidden('hdn_uploaded', __('uploaded'));
echo $this->hidden('hdn_delete_msg', __('are you sure to delete all selected object?'));
echo $this->hidden('hdn_newdir_method', 'create_dir');
echo $this->hidden('hdn_delete_method', 'delete_items');
echo $this->hidden('hdn_details_method', 'dsp_item_details');
echo $this->hidden('get_dir_content', 'get_dir_content');

echo $this->hidden('DS', DS);
echo $this->hidden('lang_newdir_msg', __('please enter folder name'));
echo $this->hidden('lang_directory', __('directory'));
echo $this->hidden('lang_up_a_level', __('up a level'));
echo $this->hidden('lang_preview', __('preview'));
?>
<div id="file">
    <div id="filemenu" class="ui-widget-header">
        <a id="btn_left_pane" href="javascript:;"><?php echo __('left pane') ?></a>
        <?php if ($this->is_service): ?>
            <!--<a id="btn_pick_file" onclick="pick_file_onclick()" href="javascript:;"><?php echo __('pick file(s)') ?></a>-->
        <?php endif; ?>
        <?php if (check_permission('THEM_MOI_MEDIA',$this->app_name)): ?>
            <a id="btn_new_dir" onclick="newdir_onclick()" href="javascript:;"><?php echo __('new directory') ?></a>
        <?php endif; ?>
        <?php if (check_permission('XOA_MEDIA',$this->app_name)): ?>
            <a id="btn_delete" onclick="delete_onclick()" href="javascript:;"><?php echo __('delete') ?></a>
        <?php endif; ?>
    </div>
    <div id="submenu">
        <input type="text" name="txt_path" id="txt_path" size="50" value="/" readonly/>
    </div>
    <div>
        <div id="left">
            <div id="folder" class="ui-widget" style="position:relative;">   
                <div class="ui-widget-header ui-state-default ui-corner-top">
                    <h4><?php echo __('folders') ?></h4>
                </div>
                <div class="ui-widget-content" style="height:200px;overflow-y: auto;" id="category-content">
                    <div class="loading_overlay" style="display:none;"></div>
                    <ul  class="filetree treeview">
                        <li class="closed">
                            <span class="folder" onclick="item_onclick(this)" data-loaded="1" data-name="<?php echo __('root directory') ?>" data-path="">
                                <?php echo __('root directory') ?>
                            </span>
                            <ul id="root_directory"></ul>
                        </li>
                    </ul>
                </div>
            </div>
            <h2></h2>
            <?php if (check_permission('THEM_MOI_MEDIA',$this->app_name)): ?>
                <div id="details" class="ui-widget">
                    <div class="ui-widget-header ui-state-default ui-corner-top">
                        <h4><?php echo __('upload') ?></h4>
                    </div>
                    <div class="ui-widget-content" style="height:200px;overflow-y: auto;position: relative;" id="category-content">
                        <div class="loading_overlay" style="display: none;text-align: center"></div>
                        <div id="upload"></div>
                        <div id="uploaded">
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div id="right">
            <table class="adminlist" width="100%" cellspacing="0" border="1">
                <colgroup>
                    <col width="10%">
                    <col width="45%">
                    <col width="20%">
                    <col width="35%">
                </colgroup>
                <tr>
                    <th><input type="checkbox" id="chk_all" onclick="chk_all_onclick()"/></th>
                    <th><?php echo __('item name') ?></th>
                    <th><?php echo __('item type') ?></th>
                    <th><?php echo __('modified') ?></th>
                </tr>
            </table>
            <div id="table_container" style="">
                <table class="adminlist data filetree" width="100%" cellspacing="0" border="1">
                    <colgroup>
                        <col width="10%">
                        <col width="45%">
                        <col width="20%">
                        <col width="35%">
                    </colgroup>
                </table>
            </div>
        </div>
    </div>
</div>

