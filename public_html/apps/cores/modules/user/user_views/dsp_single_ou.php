<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

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
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
//display header
$this->template->title = __('update ou info');

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
//------------------------------------------------------------------------------

$arr_single_ou      = $VIEW_DATA['arr_single_ou'];
$arr_parent_ou_path = $VIEW_DATA['arr_parent_ou_path'];

if (isset($arr_single_ou['PK_OU']))
{
    $v_ou_id    = $arr_single_ou['PK_OU'];
    $v_name     = $arr_single_ou['C_NAME'];
    $v_order    = $arr_single_ou['C_ORDER'];
    $v_status   = $arr_single_ou['C_STATUS'];
    $v_xml_data = $arr_single_ou['C_XML_DATA'];
    $v_level    = $arr_single_ou['C_LEVEL'];
}
else
{
    $v_ou_id    = 0;
    $v_name     = '';
    $v_order    = $arr_single_ou['C_ORDER'];
    $v_status   = 1;
    $v_xml_data = '';
    $v_level    = 1;
}
?>
<form name="frmMain" method="post" id="frmMain" action=""><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_ou');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_ou');
    echo $this->hidden('hdn_update_method', 'update_ou');
    echo $this->hidden('hdn_delete_method', 'delete_ou');

    echo $this->hidden('hdn_item_id', $v_ou_id);
    echo $this->hidden('XmlData', $v_xml_data);

    echo $this->hidden('pop_win', $v_pop_win);
    ?>
    <!-- Toolbar -->
    <h2 class="module_title"><?php echo __('update ou') ?></h2>

    <!-- Cot tuong minh -->
    <div class="Row">
        <div class="left-Col">Trực thuộc</div>
        <div class="right-Col">
            <?php foreach ($arr_parent_ou_path as $id => $name): ?>
                <label>/<?php echo $name; ?></label>
            <?php endforeach; ?>
            <?php echo $this->hidden('hdn_parent_ou_id', $id); ?>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">Tên đơn vị <label class="required">(*)</label> </div>
        <div class="right-Col">
            <input type="text" name="txt_name" id="txt_name" value="<?php echo $v_name; ?>"
                   class="inputbox" maxlength="255" style="width:80%"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="no" data-validate="text"
                   data-name="Tên đơn vị"
                   data-xml="no" data-doc="no"
                   autofocus="autofocus"
                   />
        </div>
    </div>
    <div class="Row">
        <div class="left-Col"><?php echo __('order'); ?></div>
        <div class="right-Col">
            <input type="text" name="txt_order" value="<?php echo $v_order; ?>" id="txt_order"
                   class="inputbox" size="4" maxlength="3"
                   data-allownull="no" data-validate="number"
                   data-name="<?php echo __('order'); ?>"
                   data-xml="no" data-doc="no"
                   /><label class="required">(*)</label>
        </div>
    </div>
    <div class="Row">
        <?php
        $arr_levels = array(1 => 'Cấp Sở/Uỷ ban nhân dân huyện'
            , 2 => 'Phòng ban chuyên môn thuộc Sở/Huyện'
            , 3 => 'Uỷ ban nhân dân cấp xã');
        ?>
        <div class="left-Col">Cấp đơn vị</div>
        <div class="right-Col">
            <?php foreach ($arr_levels as $k => $v): ?>
                <?php $v_checked = $v_level == $k ? 'checked' : '' ?>
                <label>
                    <input 
                        type="radio" <?php echo $v_checked ?> 
                        name="rad_level" value="<?php echo $k ?>"
                        />
                        <?php echo $v ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Button -->
    <div class="button-area">
        <input type="button" name="update" class="button save" value="<?php echo __('update'); ?>" onclick="btn_update_onclick();"/>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('cancel'); ?>" onclick="<?php echo $v_back_action; ?>"/>
    </div>

</form>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');