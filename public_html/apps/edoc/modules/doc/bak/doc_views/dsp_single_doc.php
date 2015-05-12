<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
//display header
$this->template->title = 'Cập nhật văn bản';

$v_pop_win          = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
//------------------------------------------------------------------------------
$v_doc_id           = $VIEW_DATA['doc_id'];
$arr_single_doc     = $VIEW_DATA['arr_single_doc'];
$arr_direction_text = $VIEW_DATA['arr_direction_text'];
$arr_type_option    = $VIEW_DATA['arr_type_option'];

if (isset($arr_single_doc['PK_DOC']))
{
    $v_doc_id    = $arr_single_doc['PK_DOC'];
    $v_type      = $arr_single_doc['C_TYPE'];
    $v_direction = $arr_single_doc['C_DIRECTION'];
    $v_xml_data  = $arr_single_doc['C_XML_DATA'];
    $v_is_folded = $arr_single_doc['C_FOLDED'];
}
else
{
    $v_doc_id    = 0;
    $v_type      = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'CONG_VAN';
    $v_direction = isset($_REQUEST['direction']) ? $_REQUEST['direction'] : 'VBDEN';
    $v_xml_data  = '';
    $v_is_folded = 0;
}

if (isset($_POST['type']))
{
    $v_type = $_POST['type'];
}
if (isset($_POST['direction']))
{
    $v_direction = $_POST['direction'];
}
?>
<form name="frmMain" method="post" id="frmMain" action="#" enctype="multipart/form-data"><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_doc');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_doc');
    echo $this->hidden('hdn_update_method', 'update_doc');
    echo $this->hidden('hdn_delete_method', 'delete_doc');

    echo $this->hidden('hdn_item_id', $v_doc_id);
    echo $this->hidden('XmlData', $v_xml_data);
    echo $this->hidden('direction', $v_direction);

    echo $this->hidden('pop_win', $v_pop_win);

    echo $this->hidden('hdn_deleted_doc_file_id_list', '');

    echo $this->hidden('seq_name', '');
    echo $this->hidden('inc_seq', '0');
    ?>
    <!-- Toolbar -->
    <h2 class="module_title">Vào sổ <?php echo $arr_direction_text[strtoupper($v_direction)]; ?></h2>
    <!-- /Toolbar -->

    <!-- Cot tuong minh -->
    <div class="Row">
        <div class="left-Col">Loại văn bản <span class="required">(*)</span> </div>
        <div class="right-Col">
            <select name="type" id="type" onchange="this.form.submit()"
                    data-allownull="no" data-validate="ddli" data-name="Loại văn bản"
                    data-xml="no" data-doc="no">
                <option value="">--Chọn loại văn bản--</option>
<?php echo $this->generate_select_option($arr_type_option, $v_type); ?>
            </select>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">File đính kèm</div>
        <div class="right-Col">
            <?php
            if (isset($VIEW_DATA['arr_all_doc_file']))
            {
                $arr_all_doc_file = $VIEW_DATA['arr_all_doc_file'];
                for ($i = 0; $i < sizeof($arr_all_doc_file); $i++)
                {
                    $v_file_id   = $arr_all_doc_file[$i]['PK_DOC_FILE'];
                    $v_file_name = $arr_all_doc_file[$i]['C_FILE_NAME'];
                    $v_file_path = CONST_SITE_DOC_FILE_UPLOAD_DIR . $v_file_name;

                    echo '<span id="file_' . $v_file_id . '">';
                    echo '<img src="' . SITE_ROOT . 'public/images/trash.png" style="cursor:pointer" onclick="delete_doc_file(' . $v_file_id . ')">&nbsp;';
                    echo '<a href="' . $v_file_path . '" target="_blank">' . $v_file_name . '</a><br/>';
                    echo '</span>';
                }
            }
            ?>
            <input type="file" name="user_file" id="user_file" value="Chọn file tải lên" accept="<?php echo '.' . str_replace(',', ',.', _CONST_RECORD_FILE_ACCEPT) ?>"/>
            <br>
            Hệ thống chỉ chấp nhận <?php echo _CONST_RECORD_FILE_ACCEPT ?>
        </div>
    </div>
    <?php
    //Dynamic XML
    $v_xml_file_name = strtolower('xml_' . $v_type . '_' . $v_direction . '_edit.xml');
    if ($this->load_xml(trim($v_xml_file_name)))
    {
        echo $this->render_form_display_single();
    }
    else
    {
        echo 'Chưa khai báo thuộc tính chi tiết cho: ' . $arr_direction_text[$v_direction] . ' -> ' . $arr_type_option[$v_type];
        echo '<br>Cần khai bá tên theo tên file quy định: ' . $v_xml_file_name;
    }
    ?>

    <!-- Button -->
    <div class="button-area">
        <?php
        $v_is_granted_update = FALSE;
        if (strtoupper($v_direction) === 'VBDEN')
        {
            $v_is_granted_update = $this->check_permision('VAO_SO_VAN_BAN_DEN');
        }
        if (strtoupper($v_direction) === 'VBDI')
        {
            //$v_is_granted_update = $this->check_permision('SOAN_THAO_VAN_BAN_DI');
            $v_is_granted_update = $this->check_permision('SOAN_THAO_VAN_BAN_DI') OR $this->check_permision('VAO_SO_VAN_BAN_DI');
        }
        if (strtoupper($v_direction) === 'VBTRACUU')
        {
            $v_is_granted_update = FALSE;
        }
        if (strtoupper($v_direction) === 'VBNOIBO')
        {
            $v_is_granted_update = $this->check_permision('SOAN_THAO_VAN_BAN_NOI_BO');
        }
        if ($v_is_folded > 0)
        {
            $v_is_granted_update = FALSE;
            echo '<label class="doc-is-folded-notice">Văn bản này đã được đưa vào Hồ sơ lưu!</label><br/>';
        }
        ?>
<?php if ($v_is_granted_update): ?>
            <input type="button" name="update" class="button save" value="<?php echo _LANG_UPDATE_BUTTON; ?>" onclick="btn_update_onclick();"/>
<?php endif; ?>
<?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        <input type="button" name="cancel" class="button close" value="<?php echo _LANG_CLOSE_WINDOW_BUTTON; ?>" onclick="<?php echo $v_back_action; ?>"/>
    </div>
</form>
<script type="text/javascript">
                $(document).ready(function () {
                    //Fill data
                    var formHelper = new DynamicFormHelper('', '', document.frmMain);
                    formHelper.BindXmlData();

<?php if ($v_direction == 'VBDI'): ?>
    <?php if (!$this->check_permision('VAO_SO_VAN_BAN_DI')): ?>
                            $('#txt_so_di').attr('readonly', 'readonly');
                            $('#btnSeq').hide();
    <?php endif; ?>
<?php endif; ?>
                });

                function get_next_doc_seq(element_id)
                {
                    var f = document.frmMain;
                    var v_direction = '<?php echo $v_direction; ?>';
                    var v_type = $('#type').val();

                    var v_url = SITE_ROOT + 'edoc/doc/get_next_doc_seq/?direction=' + v_direction + '&type=' + v_type;

                    var q = '#' + element_id;
                    $.ajax({url: v_url
                        , success: function (result) {
                            $(q).val(result);
                        }
                    });
                    f.inc_seq.value = $(q).val();
                }
                setPopTitle();
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');
