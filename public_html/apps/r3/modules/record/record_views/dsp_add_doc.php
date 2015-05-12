<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}

deny_bad_http_referer();

/* @var $this \r3_View */
//display header
$this->template->title = 'Thêm tài liệu';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_record_id = isset($_REQUEST['hdn_item_id']) ? $_REQUEST['hdn_item_id'] : 0;

($v_record_id > 0) OR DIE('Thao tác sai');
?>

<style>
    td{
        vertical-align: middle;
        height: 30px;
        padding: 5px;
    }
    td input[type="text"]{
        margin-bottom: 0;
    }
</style>

<script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.iframe-post-form.js" type="text/javascript"></script>

<form name="frmMain" id="frmMain" method="POST" action="<?php echo $this->get_controller_url(); ?>do_add_doc" 
      enctype="multipart/form-data">
          <?php echo $this->hidden('hdn_item_id', $v_record_id); ?>
          <?php echo $this->hidden('hdn_update_method', 'do_add_doc'); ?>
          <?php echo $this->hidden('XmlData', ''); ?>
          <?php echo $this->hidden('controller', $this->get_controller_url()); ?>
          <?php echo $this->user_token(); ?>
    <div class="well">
        <table class="none-border-table" width="100%">
            <tr>
                <td>Số ký hiệu<span class="required">(*)</span>:</td>
                <td>
                    <input name="txt_record_doc_no" id="txt_record_doc_no" maxlength="50" style="width:100%"
                           type="text" value="" data-allownull="no" data-validate="text" data-name="Số ký hiệu:" data-xml="no" data-doc="no" />
                </td>
                <td>Nơi ban hành:</td>
                <td>
                    <input  name="txt_issuer" id="txt_issuer"
                            style="width:100%" type="text" value="" data-allownull="yes"
                            data-validate="text" data-name="Nơi ban hành" data-xml="no" data-doc="no" />
                </td>
            </tr>
            <tr>
                <td>Tên tài liệu <span class="required">(*)</span>:</td>
                <td colspan="3">
                    <input  name="txt_description" id="txt_description"
                            style="width:100%" type="text" value="" data-allownull="no"
                            data-validate="text" data-name="Tên tài liệu" data-xml="no" data-doc="no" />
                </td>
            </tr>
            <tr>
                <td colspan="4">Nội dung:</td>
            </tr>
            <tr>
                <td colspan="4">
                    <textarea style="width:100%;box-sizing:border-box;height:100px;" rows="2"
                              name="txt_content" id="txt_content" cols="20" maxlength="400"
                              ></textarea>
                </td>
            </tr>
        </table>
        <div class="Row">
            <label>Tài liệu đính kèm</label>
            <input type="file" style="border: solid #D5D5D5; color: #000000" class="multi accept-<?php echo _CONST_RECORD_FILE_ACCEPT; ?>" name="uploader[]"
                   id="File1" accept="<?php echo '.' . str_replace(',', ',.', _CONST_RECORD_FILE_ACCEPT) ?>"/>
            <font style="font-weight: normal;">Hệ thống chỉ chấp nhận đuôi file:
            <span style="color:red"><?php echo _CONST_RECORD_FILE_ACCEPT ?></span>
            </font><br/>
        </div>
        <div class="clear">&nbsp;</div>
        <!-- Buttons -->
        <div class="button-area">
            <button type="button" name="trash" class="btn btn-primary" onclick="btn_do_add_doc_onclick();" >
                <i class="icon-save"></i>
                Cập nhật
            </button>
            <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
            <button type="button" name="cancel" class="btn" onclick="<?php echo $v_back_action; ?>">
                <i class="icon-remove"></i>
                Đóng cửa sổ        
            </button>
        </div>
    </div>
</form>
<script>
            var f = document.frmMain;
            function btn_do_add_doc_onclick()
            {
                var v_record_doc_no = trim(f.txt_record_doc_no.value);
                var v_description = trim(f.txt_description.value);
                var v_issuer = trim(f.txt_issuer.value);
                var v_content = trim(f.txt_content.value);

                if (v_record_doc_no == '')
                {
                    alert('Bạn chưa số/ký hiệu!');
                    f.txt_record_doc_no.focus();
                    return false;
                }

                if (v_description == '')
                {
                    alert('Bạn chưa nhập tên tài liệu!');
                    f.txt_description.focus();
                    return false;
                }

                f.submit();
                return;
            }
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');
