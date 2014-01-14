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
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
?>
<?php
//display header
$this->template->title = 'Cập nhật loại hồ sơ';
$this->template->display('dsp_header.php');

$arr_single_record_type = $VIEW_DATA['arr_single_record_type'];
if (isset($arr_single_record_type['PK_RECORD_TYPE']))
{
    $v_record_type_id      = $arr_single_record_type['PK_RECORD_TYPE'];
    $v_xml_data            = $arr_single_record_type['C_XML_DATA'];
    $v_code                = $arr_single_record_type['C_CODE'];
    $v_name                = $arr_single_record_type['C_NAME'];
    $v_status              = $arr_single_record_type['C_STATUS'];
    $v_scope               = $arr_single_record_type['C_SCOPE'];
    $v_order               = $arr_single_record_type['C_ORDER'];
    $v_send_over_internet  = $arr_single_record_type['C_SEND_OVER_INTERNET'];
    $v_spec_code           = $arr_single_record_type['C_SPEC_CODE'];
    $v_allow_verify_record = $arr_single_record_type['C_ALLOW_VERIFY_RECORD'];
}
else
{
    $v_record_type_id      = 0;
    $v_xml_data            = '';
    $v_code                = '';
    $v_name                = '';
    $v_status              = 1;
    $v_scope               = 3;
    $v_order               = $arr_single_record_type['C_ORDER'];
    $v_send_over_internet  = 0;
    $v_allow_verify_record = 0;
}
?>
<form name="frmMain" method="post" id="frmMain" action=""><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record_type');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record_type');
    echo $this->hidden('hdn_update_method', 'update_record_type');
    echo $this->hidden('hdn_delete_method', 'delete_record_type');

    echo $this->hidden('hdn_item_id', $v_record_type_id);
    echo $this->hidden('XmlData', $v_xml_data);

    $this->write_filter_condition(array('txt_filter', 'sel_goto_page', 'sel_rows_per_page'));
    ?>
    <div class="Row">
        <div class="left-Col">Mã loại hồ sơ <label class="required">(*)</label></div>
        <div class="right-Col">
            <input type="text" name="txt_code" value="<?php echo $v_code; ?>" id="txt_code"
                   class="inputbox" maxlength="50" size="10"
                   onKeyDown="handleEnter(this, event);" onkeyup="ConverUpperCase('txt_code', this.value)"
                   data-allownull="no" data-validate="text"
                   data-name="Mã loại hồ sơ"
                   data-xml="no" data-doc="no" autofocus="autofocus"
                   />
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">Tên loại hồ sơ <label class="required">(*)</label></div>
        <div class="right-Col">
            <textarea name="txt_name" id="txt_name"
                      class="inputbox" style="width:80%"
                      data-allownull="no" data-validate="text"
                      data-name="Tên loại hồ sơ"
                      data-xml="no" data-doc="no" rows="3"
                      ><?php echo $v_name; ?></textarea>            
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">Thuộc Lĩnh vực <label class="required">(*)</label></div>
        <div class="right-Col">
            <select name="sel_spec_code" id="sel_spec_code" class="ddl" data-allownull="no" data-validate="ddli" 
                    data-name="Lĩnh vực" data-xml="yes" data-doc="no"
                    >
                <option value="">--Chọn lĩnh vực--</option>
                <?php echo $this->generate_select_option($arr_all_spec, $v_spec_code); ?>
            </select>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">Thuộc thuộc sổ theo dõi<label class="required">(*)</label></div>
        <div class="right-Col">
            <?php foreach ($arr_all_report_books as $book): ?>
                <?php
                $checked = $book['C_IS_CHECKED'] ? 'checked' : '';
                ?>
                <label for="chk_report_book_<?php echo $book['PK_LIST'] ?>">
                    <input 
                        id="chk_report_book_<?php echo $book['PK_LIST'] ?>"
                        type="checkbox" name="chk_report_book[]" 
                        value="<?php echo $book['C_CODE'] ?>" 
                        <?php echo $checked ?>
                        />
                        <?php echo $book['C_NAME'] ?>
                </label>
                <br/>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">Phạm vi</div>
        <div class="right-Col">
            <input type="radio" name="rad_scope" value="0" id="rad_scope_0" <?php echo ($v_scope == 0) ? ' checked' : ''; ?>/>
            <label for="rad_scope_0">Cấp xã/phường</label>
            <br/>
            <input type="radio" name="rad_scope" value="1" id="rad_scope_1" <?php echo ($v_scope == 1) ? ' checked' : ''; ?>/>
            <label for="rad_scope_1">Liên thông Xã -> Huyện</label>
            <br/>
            <input type="radio" name="rad_scope" value="2" id="rad_scope_2" <?php echo ($v_scope == 2) ? ' checked' : ''; ?>/>
            <label for="rad_scope_2">Liên thông Huyện -> Xã</label>

            <br/>
            <input type="radio" name="rad_scope" value="3" id="rad_scope_3" <?php echo ($v_scope == 3) ? ' checked' : ''; ?>/>
            <label for="rad_scope_3">Cấp Huyện</label>
            <br/>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            Dịch vụ công trực tuyến
        </div>
        <div class="right-Col">
            <?php $checked         = ($v_send_over_internet) ? ' checked' : ''; ?>
            <input 
                type="checkbox" name="chk_send_over_internet" value="1"
                <?php echo $checked ?> id="chk_send_over_internet"
                />
            <label for="chk_send_over_internet"><?php echo __('send over internet'); ?></label>
            <br/>
            <?php $checked         = ($v_allow_verify_record) ? ' checked' : ''; ?>
            <input 
                type="checkbox" name="chk_allow_verify_record" value="1"
                <?php echo $checked ?> id="chk_allow_verify_record"
                />
            <label for="chk_allow_verify_record">Cho phép nộp thử để kiểm tra tính hợp lệ</label>
            <br/>
        </div>
    </div>

    <div class="Row">
        <div class="left-Col"><?php echo __('order'); ?><label class="required">(*)</label></div>
        <div class="right-Col">
            <input type="text" name="txt_order" value="<?php echo $v_order; ?>" id="txt_order"
                   class="inputbox" maxlength="50" size="10"
                   onKeyDown="handleEnter(this, event);"
                   data-allownull="no" data-validate="number"
                   data-name="<?php echo __('order'); ?>"
                   data-xml="no" data-doc="no"
                   />
        </div>
    </div>

    <div class="Row">
        <div class="left-Col"><?php echo __('status'); ?></div>
        <div class="right-Col">
            <input type="checkbox" name="chk_status" value="1"
            <?php echo ($v_status > 0) ? ' checked' : ''; ?>
                   id="chk_status"
                   /><label for="chk_status"><?php echo __('active status'); ?></label><br/>
            <input type="checkbox" name="chk_save_and_addnew" value="1"
            <?php echo ($v_record_type_id > 0) ? '' : ' checked'; ?>
                   id="chk_save_and_addnew"
                   /><label for="chk_save_and_addnew"><?php echo __('save and add new'); ?></label>

            <!-- 
            <br><a href="<?php echo $this->get_controller_url('workflow'); ?>&sel_record_type=<?php echo $v_code; ?>" target="_blank">Xem quy trình xử lý hồ sơ</a>
            <br><a href="javascript:void(0)" onclick="btn_dsp_plaintext_form_struct_onclick()">Xem biểu mẫu đơn</a>
            -->
        </div>
    </div>

    <!-- XML data -->
    <?php
    $v_xml_file_name = 'xml_record_type_edit.xml';
    if ($this->load_xml($v_xml_file_name))
    {
        echo $this->render_form_display_single();
    }
    ?>
    <!-- Button -->
    <div class="clear">&nbsp;</div>
    <div class="button-area">
        <input type="button" name="update" class="button save" value="<?php echo __('update'); ?>" onclick="btn_update_onclick();"/>
        <input type="button" name="cancel" class="button close" value="<?php echo __('go back'); ?>" onclick="btn_back_onclick();"/>

        <input type="button" name="aaa" class="button lookup" value="Xem quy trình xử lý hồ sơ" onclick="window.open('<?php echo $this->get_controller_url('workflow'); ?>&sel_record_type=<?php echo $v_code; ?>');"/>
        <input type="button" name="bbb" class="button lookup" value="Xem biểu mẫu đơn" onclick="btn_dsp_plaintext_form_struct_onclick();"/>
    </div>
</form>
<script>
                       function btn_dsp_plaintext_form_struct_onclick()
                       {
                           var url = '<?php echo $this->get_controller_url(); ?>dsp_plaintext_form_struct/&sel_record_type=<?php echo $v_code; ?>';
                           url += '&pop_win=1';

                           showPopWin(url, 1000, 550);
                       }

</script>
<?php
$this->template->display('dsp_footer.php');