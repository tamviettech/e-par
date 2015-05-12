<?php
    $v_parent_folder = get_request_var('parent','');
    $v_share_user_id = get_request_var('user_id','');
    $v_public        = get_request_var('is_public','');

?>
<script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/bootstrap-fileupload.js"></script>
<form class="form-horizontal" id="frmMain" name="frmMain" action="<?php echo $this->get_controller_url().'do_upload'; ?>" 
      method="POST" enctype="multipart/form-data"
       > 
    <?php
        echo $this->hidden('hdn_folder_id',$v_parent_folder);
        echo $this->hidden('hdn_share_user_id',$v_share_user_id);
        echo $this->hidden('hdn_media_public',$v_public);
    ?>
    <div class="span12">
        <div class="content-widgets gray">
            <div class="widget-head bondi-blue">
                    <h3> File Upload</h3>
            </div>
            <div class="widget-container">
                <div class="clear" style="height: 10px">&nbsp;</div>
                <div class="control-group">
                    <label class="control-label">Tên file <i style="color:red">(*)</i></label>
                    <div class="controls">
                        <input style="height: 30px;width: 210px;line-height: 30px" type="text" name="txt_name" id="txt_name" value=""/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">File upload <i style="color:red">(*)</i></label>
                    <div class="controls">
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="input-append">
                                <div class="uneditable-input span3" style="height: 30px; width: 210px;line-height: 30px  ">
                                            <i class="icon-file fileupload-exists"></i>
                                            <span class="fileupload-preview"></span>
                                    </div>
                                    <span class="btn btn-file"><span class="fileupload-new">Select file</span>
                                    <span class="fileupload-exists">Change</span>
                                    <input style="height: 30px;" type="file" name="file_upload">
                                    </span><a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">&nbsp;</label>
                    <div class="controls">
                        <label class="required">
                            Hệ thống chỉ hỗ trợ định dạng file: <?php echo _CONST_MEDIA_FILE_ACCEPT ;?>
                        </label>
                    </div>
                </div>
                <div class="form-actions" style="text-align: center;">
                        <button type="button" onclick="btn_upload_onclick();" class="btn"><i class="icon-upload-alt"></i> Tải lên</button>
                        <button type="button" class="btn" onclick="javascript:window.parent.hidePopWin();"><i class="icon-remove"></i>Đóng cửa sổ</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    $("#frmMain").validate({
        rules: {
            txt_name: "required"
        },
        messages: {
            txt_name: "Bạn chưa nhập tên file"
        }
    });
    function btn_upload_onclick()
    {
        $('#frmMain').submit();
    }
</script>