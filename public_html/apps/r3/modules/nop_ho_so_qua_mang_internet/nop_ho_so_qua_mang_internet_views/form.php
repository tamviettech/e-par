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

        <h4 style="margin:15px 0; font-weight: bold"><?php echo isset($before_title) ? $before_title : '' ?><?php echo $v_record_type ?></h4>
        <div class="panel_color" style="font-weight: bold;margin-top: 10px; margin-bottom: 10px;font-size: 1.3em">Thông tin chung</div>
        <?php if (isset($response) && $response->message): ?>
            <span class="required"><?php echo $response->message ?></span>
        <?php endif; ?>
            <div class="Row">
                <div class="left-Col">
                    <label for="sel_record_type">Loại hồ sơ</label>
                </div>
                <div class="right-Col">
                    <select name="sel_record_type" id="sel_record_type" style="width: 50%; color: #000000;"
                            data-validate="text" data-name="Loại hồ sơ" data-xml="no"
                            data-doc="no" disabled
                            >
                                <?php echo $this->generate_select_option($arr_all_record_type_option, $active_record_type_id); ?>
                    </select>
                </div>
            </div>
            
             <div class="Row">
                <div class="left-Col">
                    <label for="txt_record_no">Mã hồ sơ: <span class="required">(*)</span></label>
                </div>
                <div class="right-Col">
                    <input readonly="readonly" name="txt_record_no"
                           id="txt_record_no" maxlength="50" style="width: 200px" type="text"
                           value="<?php echo $v_record_no; ?>" data-allownull="no"
                           data-validate="text" data-name="M&atilde; h&#7891; s&#417;"
                           data-xml="no" data-doc="no" />
                </div>
            </div>
            
            <div class="Row">
                <div class="left-Col">
                <label for="txt_name">Họ và tên: <span class="required">(*)</span>
                </div>
                <div class="right-Col">
                     <input name="txt_name"
                           id="txt_name" maxlength="20" style="width: 200px"
                           type="text" value="<?php echo $v_name ?>"
                           data-allownull="no" data-validate="text"
                           data-name="Họ và tên"
                           onkeyup="ConverUpperCase('txt_name', this.value)"
                           data-xml="no" data-doc="no" />
                </div>
            </div>
            <!--End .Row-->
            
             <div class="Row">
                <div class="left-Col">
                <label for="txt_return_phone_number">Số điện thoại: <span class="required">(*)</span></label>
                </div>
                <div class="right-Col">
                    <input name="txt_return_phone_number"
                           id="txt_return_phone_number" maxlength="20" style="width: 200px"
                           type="text" value="<?php echo $v_phone ?>"
                           data-allownull="no" data-validate="phone"
                           data-name="Số điện thoại"
                           data-xml="no" data-doc="no" />
                </div>
            </div>
            <!--End .Row-->
             <div class="Row">
                <div class="left-Col">
                <label for="txt_return_email">Email: <span class="required">(*)</span></label>
                </div>
                <div class="right-Col">
                    <input name="txt_return_email" id="txt_return_email"
                           maxlength="255" style="width: 200px" type="text"
                           value="<?php echo $v_email ?>" data-allownull="no" 
                           data-validate="email" data-name="Địa chỉ email" data-xml="no"
                           data-doc="no" />
                </div>
            </div>
            <!--End .Row-->
            
             <div class="Row">
                <div class="left-Col">
                <label for="tbxNote">Ghi chú:</label>
                </div>
                <div class="right-Col">
                   <textarea 
                        style="width:50%;height:40px" rows="2" name="tbxNote" 
                        maxlength="2000" id="tbxNote" cols="20"
                        ><?php echo $v_note ?></textarea>
                </div>
            </div>
            <!--End .Row-->
            
            <div class="Row">
                <div class="left-Col">
                    <label>File đính kèm:</label>
                </div>
                <div class="right-Col">
                  <input type="file" class="multi accept-<?php echo _CONST_RECORD_FILE_ACCEPT; ?>" name="uploader[]" id="File1" accept="<?php echo '.' . str_replace(',', ',.', _CONST_RECORD_FILE_ACCEPT) ?>"/>
                    <span class="fileUploaderMessage">Hệ thống chỉ chấp nhận file dạng: <?php echo str_replace('|', '; ', _CONST_RECORD_FILE_ACCEPT); ?></span>
                </div>
            </div>
            <!--End .Row-->
            <div class="Row">
                <div class="left-Col">
                
                </div>
                <div class="right-Col">
                  
                </div>
            </div>
            <!--End .Row-->
            <style>
                #recaptcha_area, #recaptcha_table 
                {
                    width: 318px!important;
                    text-align: center;
                    margin: 0 auto;
                }
            </style>
            <div class="Row" style="text-align: center;margin: 0 auto">
                <label for="recaptcha_response_field" style="font-weight: bold">Vui lòng nhập mã xác thực</label>
                <?php echo recaptcha_get_html(_CONST_RECAPCHA_PUBLIC_KEY) ?>
                <div class="clear">&nbsp;</div>
                <div class="button-area">
                    <!--button update-->
                    <button type="button" name="trash" class="btn btn-primary" onclick="btn_update_onclick();">
                        <i class="icon-save"></i>
                        Nộp hồ sơ
                    </button>
                    <!--button back-->
                    <button type="button" name="trash" class="btn btn-primary" onclick="history.go(-1);">
                        <i class="icon-reply"></i>
                        <?php echo __('go back'); ?>
                    </button>
                </div>
                <!--<div id="detail" style="display: none">-->
            </div>
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

