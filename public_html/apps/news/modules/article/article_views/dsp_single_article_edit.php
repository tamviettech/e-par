<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}
?>
<?php
//display header
$this->template->title = 'Cập nhật tin bài';
$this->template->display('dsp_header.php');

include_once(SERVER_ROOT . 'public/ckeditor/ckeditor_php5.php');
$arr_single_article     = $VIEW_DATA['arr_single_article'];

if (isset($arr_single_article['PK_ARTICLE']))
{
    $v_article_id           = $arr_single_article['PK_ARTICLE'];
    $v_title                = $arr_single_article['C_TITLE'];
    $v_short_content        = $arr_single_article['C_SHORT_CONTENT'];
    $v_content              = $arr_single_article['C_CONTENT'];
    $v_attach_file_name     = $arr_single_article['C_ATTACH_FILE_NAME'];
    $v_xml_data             = $arr_single_article['C_XML_DATA'];
    $v_begin_date           = $arr_single_article['C_BEGIN_DATE'];

    $v_begin_date = jwDate::yyyymmdd_to_ddmmyyyy($v_begin_date);

    $v_link_to_attach       = $arr_single_article['C_LINK_TO_ATTACH'];
    $v_status				= $arr_single_article['C_STATUS'];
}
else
{
    $v_article_id           = 0;
    $v_title                = '';
    $v_short_content        = '';
    $v_content              = '';
    $v_attach_file_name     = '';
    $v_xml_data             = '';
    $v_begin_date           = date('d/m/Y');
    $v_link_to_attach       = 0;
    $v_status				= 0;
}
?>
<form name="frmMain" method="post" id="frmMain" action="" enctype="multipart/form-data"><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_article');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_article');
    echo $this->hidden('hdn_update_method', 'update_article');
    echo $this->hidden('hdn_delete_method', 'delete_article');

    echo $this->hidden('hdn_item_id', $v_article_id);
    echo $this->hidden('XmlData', $v_xml_data);

    // Luu dieu kien loc
    $v_filter = isset($_POST['txt_filter']) ? $_POST['txt_filter'] : '';
    $v_page = isset($_POST['sel_goto_page']) ? Model::replace_bad_char($_POST['sel_goto_page']) : 1;
    $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? Model::replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;

    echo $this->hidden('txt_filter', $v_filter);
    echo $this->hidden('sel_goto_page', $v_page);
    echo $this->hidden('sel_rows_per_page', $v_rows_per_page);

    echo $this->hidden('hdn_deleted_attach_file', '');
    ?>
    <h2 class="module_title">Cập nhật tin bài</h2>

    <table width="100%" class="adminform" >
        <col width="20%"><col width="80%">
        <tr>
            <td>
                <?php echo LANG_ARTICLE_TITLE_LABEL;?>
            </td>
            <td style="padding-top:4px">
                <input type="text" name="txt_title" class="inputbox" id="txt_title"
                    value="<?php echo $v_title;?>"
                    style="width:99%" autofocus="autofocus"
                    data-allownull="no" data-validate="text"
                    data-name="<?php echo LANG_ARTICLE_TITLE_LABEL;?>"
                    data-xml="no" data-doc="no"
                />
            </td>
        </tr>
        <tr>
            <td>Ngày bắt đầu</td>
            <td style="padding-top:4px">
                <input type="text" name="txt_begin_date" class="inputbox" id="txt_begin_date"
                    value="<?php echo $v_begin_date;?>" size="10"
                    data-allownull="no" data-validate="text"
                    data-name="Ngày bắt đầu"
                    data-xml="no" data-doc="no"
                />&nbsp;
                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')" />
            </td>
        </tr>
        <?php
        /*
        <tr>
            <td>Hình ảnh</td>
            <td style="padding-top:4px">
                <input type="file" name="file_image" id="file_image" value="Chọn file tải lên"/>
            </td>
        </tr>
         *
         */?>

        <tr>
            <td>&nbsp;</td>
            <td>
                <input type="checkbox" name="chk_status" id="chk_status" <?php echo ($v_status >0) ? 'checked' : ''; ?> />
                <label for="chk_status">Hiển thị</label>
            </td>
        </tr>
        <tr>
            <td valign="TOP" colspan="2">
                Nội dung
                <?php //echo LANG_SHORT_CONTENT_LABEL;?><br/>
                <?php
                $CKEditor = new CKEditor();
                $CKEditor->basePath = SITE_ROOT . 'public/ckeditor/';
                $CKEditor->editor("short_content", $v_short_content);
                ?>
            </td>
        </tr>
        <?php
        /*
        <tr>
            <td valign="top" colspan="2">
                <?php echo LANG_FULL_CONTENT_LABEL;?><br/>
                <?php $CKEditor->editor("content", $v_content);?>
            </td>
        </tr>
         *
         */?>
        <tr>
            <td>File đính kèm</td>
            <td style="padding-top:4px">
                <?php if (strlen($v_attach_file_name) > 0)
                {
                    $v_file_path = SITE_ROOT . 'uploads/news/' . $v_attach_file_name;
                    echo '<span id="attach_file_' . $v_article_id . '">';
                    echo '<img src="' . SITE_ROOT . 'public/images/trash.png" style="cursor:pointer" onclick="delete_attach_file(' . $v_article_id . ')">&nbsp;';
                    echo '<a href="' . $v_file_path .'" target="_blank">' .$v_attach_file_name . '</a><br/>';
                    echo '</span>';
                }?>
                <input type="file" name="file_attach" id="file_attach" value="Chọn file tải lên" />
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <input type="checkbox" name="chk_link_to_attach" id="chk_link_to_attach" <?php echo ($v_link_to_attach > 0) ? ' checked' : '';?>/>
                <label for="chk_link_to_attach">Truy cập ngay vào file đính kèm</label>
            </td>
        </tr>
    </table>
    <div class="button-area">
        <input type="button" name="update" class="button" value="<?php echo _LANG_UPDATE_BUTTON; ?>" onclick="btn_update_onclick();"/>
        <input type="button" name="cancel" class="button" value="<?php echo _LANG_GO_BACK_BUTTON; ?>" onclick="btn_back_onclick();"/>
    </div>
</form>
<script>
    function delete_attach_file(file_id)
    {
        $('#attach_file_' + file_id).hide();
        $('#hdn_deleted_attach_file').val(file_id);
    }
</script>
<?php $this->template->display('dsp_footer.php');