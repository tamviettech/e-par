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
defined('SERVER_ROOT') or die();
?>
<?php if (!isset($v_record_type_code) || !$v_record_type_code): ?>
    <h4>Thủ tục này không hỗ trợ nộp trực tuyến.</h4>
<?php else: ?>
    <?php
    $v_record_no   = $v_record_type_code . '-' . strtoupper(base_convert(preg_replace('[\D]', '', Date('ymdHis')), 10, 16));
    $v_record_type = $arr_all_record_type_option[$active_record_type_id];
    $v_phone       = get_post_var('txt_return_phone_number');
    $v_email       = get_post_var('txt_return_email');
    $v_note        = get_post_var('tbxNote');
    $v_name        = get_post_var('txt_name');
    ?>
    <form name="frmMain" id="frmMain" action="" method="POST" enctype="multipart/form-data">
        <?php
        echo $this->hidden('hdn_record_type_id', $active_record_type_id);
        echo $this->hidden('hdn_update_method', $action . '/' . $active_record_type_id);
        echo $this->hidden('controller', SITE_ROOT . 'nop_ho_so/');
        echo $this->hidden('hdn_help_method', 'tro_giup');
        echo $this->hidden('XmlData', '<data/>');
        ?>

        <h4 style="margin:0;"><?php echo isset($before_title) ? $before_title : '' ?><?php echo $v_record_type ?></h4>
        (
        <a href="<?php echo SITE_ROOT . 'nop_ho_so/danh_sach_thu_tuc' ?>">
            Chọn thủ tục khác để nộp?
        </a>
        )
        <div class="panel_color">Thông tin chung</div>
        <?php if (isset($response) && $response->message): ?>
            <span class="required"><?php echo $response->message ?></span>
        <?php endif; ?>
        <table style="width: 100%;" class="none-border-table adminform">
            <tr>
                <td width="20%"><label for="sel_record_type">Loại hồ sơ</label></td>
                <td colspan="3">
                    <select name="sel_record_type" id="sel_record_type" style="width: 77%; color: #000000;"
                            data-validate="text" data-name="Loại hồ sơ" data-xml="no"
                            data-doc="no" disabled
                            >
                                <?php echo $this->generate_select_option($arr_all_record_type_option, $active_record_type_id); ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="txt_record_no">Mã hồ sơ: <span class="required">(*)</span></label></td>
                <td>
                    <input readonly="readonly" name="txt_record_no"
                           id="txt_record_no" maxlength="50" style="width: 200px" type="text"
                           value="<?php echo $v_record_no; ?>" data-allownull="no"
                           data-validate="text" data-name="M&atilde; h&#7891; s&#417;"
                           data-xml="no" data-doc="no" />
                </td>
            </tr>
            <tr>
                <td><label for="txt_name">Họ và tên: <span class="required">(*)</span></label></td>
                <td>
                    <input name="txt_name"
                           id="txt_name" maxlength="20" style="width: 200px"
                           type="text" value="<?php echo $v_name ?>"
                           data-allownull="no" data-validate="text"
                           data-name="Họ và tên"
                           onkeyup="ConverUpperCase('txt_name', this.value)"
                           data-xml="no" data-doc="no" />
                </td>
            </tr>
            <tr>
                <td><label for="txt_return_phone_number">Số điện thoại: <span class="required">(*)</span></label></td>
                <td><input name="txt_return_phone_number"
                           id="txt_return_phone_number" maxlength="20" style="width: 200px"
                           type="text" value="<?php echo $v_phone ?>"
                           data-allownull="no" data-validate="phone"
                           data-name="Số điện thoại"
                           data-xml="no" data-doc="no" />
                </td>
                <td><label for="txt_return_email">Email: <span class="required">(*)</span></label></td>
                <td><input name="txt_return_email" id="txt_return_email"
                           maxlength="255" style="width: 200px" type="text"
                           value="<?php echo $v_email ?>" data-allownull="no" 
                           data-validate="email" data-name="Địa chỉ email" data-xml="no"
                           data-doc="no" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="tbxNote">Ghi chú:</label>
                </td>
                <td colspan="3">
                    <textarea 
                        style="width:540px;height:40px" rows="2" name="tbxNote" 
                        maxlength="2000" id="tbxNote" cols="20"
                        ><?php echo $v_note ?></textarea>
                </td>
            </tr>
            <tr>
                <td>File đính kèm:</td>
                <td colspan="3">
                    <input type="file" class="multi accept-<?php echo _CONST_RECORD_FILE_ACCEPT; ?>" name="uploader[]" id="File1" />
                    <span class="fileUploaderMessage">Hệ thống chỉ chấp nhận file dạng: <?php echo str_replace('|', '; ', _CONST_RECORD_FILE_ACCEPT); ?></span><br />
                </td>
            </tr>
        </table>
        <label for="recaptcha_response_field">Vui lòng nhập mã xác thực</label>
        <?php echo recaptcha_get_html(_CONST_RECAPCHA_PUBLIC_KEY) ?>
        <div class="clear">&nbsp;</div>
        <div class="button-area">
            <input type="button" class="button save" value="Nộp hồ sơ" onclick="btn_update_onclick()"/>
            <input type="button" class="button cancel" value="Quay lại" onclick="history.go(-1)"/>
        </div>
        <!--<div id="detail" style="display: none">-->
    </form>
<?php endif; ?>
<script>
                               function btn_gui_hs_onclick()
                               {
                                   btn_update_onclick();
                               }

                               function dsp_help() {
                                   w = window.open($('#controller').val() + $('#hdn_help_method').val(), '', 'width=400,height=600');
                                   w.focus();
                               }
</script>

