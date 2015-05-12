<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}

if (isset($arr_single_record['PK_RECORD']))
{
    $v_record_id           = $arr_single_record['PK_RECORD'];
    $v_record_no           = $arr_single_record['C_RECORD_NO'];
    $v_receive_date        = $arr_single_record['C_RECEIVE_DATE'];
    $v_return_phone_number = $arr_single_record['C_RETURN_PHONE_NUMBER'];
    $v_return_date         = $arr_single_record['C_RETURN_DATE'];

    $v_citizen_name     = $arr_single_record['C_CITIZEN_NAME'];
    $v_record_type_name = $arr_single_record['C_RECORD_TYPE_NAME'];
    $v_record_type_code = $arr_single_record['C_RECORD_TYPE_CODE'];

    $v_xml_data       = $exec_result_data;
    $v_task_code      = $arr_single_record['C_NEXT_TASK_CODE'];

    $dom_record_result = simplexml_load_string($arr_single_record['XML_RECORD_RESULT']);

    $v_receive_date        = jwDate::yyyymmdd_to_ddmmyyyy($v_receive_date, 1);
    $v_return_date_by_text = $this->return_date_by_text($v_return_date);
}
else
{
    die();
}

//display header
$this->template->title = 'Kết quả thụ lý hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
?>
<form name="frmMain" id="frmMain" action="" method="POST" enctype="multipart/form-data">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', $v_record_id);
    echo $this->hidden('hdn_item_id_list', '');

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'do_update_exec_result');
    echo $this->hidden('hdn_delete_method', 'delete_record');

    echo $this->hidden('XmlData', $xml_require_form_data);

    echo $this->hidden('hdn_return_date', $v_return_date);
    echo $this->hidden('hdn_total_time', $v_total_time);

    echo $this->hidden('MY_TASK', $MY_TASK);

    echo $this->hidden('hdn_deleted_file_id_list', '');
    echo $this->hidden('hdn_require_form', $v_require_form);
    
    ?>
    <!--<h3 class="page-header">Tiếp nhận hồ sơ</h3>-->
    <div class="content-widgets ">
        <div class="widget-head blue">
            <h3>Thông tin chung</h3>
        </div>
        <style type="text/css">table.none td{border:0px}</style>
        <div class="widget-container" style="border: 1px solid #3498DB">
            <table border="0" cellpadding="4" cellspacing="0" width="100%" class="none">
                <tr>
                    <td style="width: 150px; font-weight: bold">
                        Loại hồ sơ:
                    </td>
                    <td colspan="4">
                        <?php echo $v_record_type_code; ?> - <?php echo $v_record_type_name; ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold">
                        Tên người đăng ký:
                    </td>
                    <td style="width: 200px">
                        <?php echo $v_citizen_name; ?>
                    </td>
                    <td style="width: 150px; font-weight: bold">
                        Mã hồ sơ:
                    </td>
                    <td>
                        <?php echo $v_record_no; ?>
                    </td>
                </tr>
                <tr>
                    <td>Thêm file đính kèm:</td>
                    <td colspan="2">
                        <?php
                        $arr_accept = explode(',', _CONST_RECORD_FILE_ACCEPT);
                        $class      = '';
                        foreach ($arr_accept as $ext)
                        {
                            $class .= " accept-$ext";
                        }
                        ?>
                        <input type="file"
                               style="border: solid #D5D5D5; color: #000000"
                               class="multi <?php echo $class; ?>"
                               name="uploader[]" id="File1" accept="<?php echo '.' . str_replace(',', ',.', _CONST_RECORD_FILE_ACCEPT) ?>"/> 
                        <font style="font-weight: normal;">Hệ thống chỉ chấp nhận đuôi file <?php echo _CONST_RECORD_FILE_ACCEPT ?></font><br/>
                        <?php
                        if (isset($VIEW_DATA['arr_all_record_file']))
                        {
                            $arr_all_record_file = $VIEW_DATA['arr_all_record_file'];
                            for ($i = 0; $i < sizeof($arr_all_record_file); $i++)
                            {
                                $v_file_id   = $arr_all_record_file[$i]['PK_RECORD_FILE'];
                                $v_file_name = $arr_all_record_file[$i]['C_FILE_NAME'];
                                $v_media_id  = $arr_all_record_file[$i]['FK_MEDIA'];
                                if($v_media_id != NULL && $v_media_id != '' && $v_media_id > 0)
                                {
                                    $v_year            = $arr_all_record_file[$i]['C_YEAR'];
                                    $v_month           = $arr_all_record_file[$i]['C_MONTH'];
                                    $v_day             = $arr_all_record_file[$i]['C_DAY'];
                                    $v_media_file_name = $arr_all_record_file[$i]['C_MEDIA_FILE_NAME'];
                                    $v_file_name       = $arr_all_record_file[$i]['C_NAME'];
                                    
                                    $v_file_path = CONST_FILE_UPLOAD_LINK . "$v_year/$v_month/$v_day/$v_media_file_name";
                                }
                                else
                                {
                                    $v_file_path = SITE_ROOT . 'uploads/r3/' . $v_file_name;
                                }

                                echo '<span id="file_' . $v_file_id . '">';
                                echo '<a href="' . $v_file_path . '" target="_blank">' . $v_file_name . '</a><br/>';
                                echo '</span>';
                            }
                        }
                    ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div id="record_detail">
        <div id="xml_part">
            <?php 
            $v_file_path =  SERVER_ROOT . 'apps/' . $this->app_name .'/xml-config/' . $v_record_type_code . '/' . $v_require_form;
            echo $this->transform($v_file_path); ?>
        </div>
    </div>

    <!-- Button -->
    <div class="button-area">
        <!--button update-->
        <button type="button" name="trash" class="btn btn-primary" onclick="btn_update_onclick();" accesskey="2">
            <i class="icon-save"></i>
            <?php echo __('update'); ?> (Alt+2)
        </button>

        <!--button back-->
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        <!--Button close window-->
        <button type="button" name="trash" class="btn" onclick="<?php echo $v_back_action; ?>" >
            <i class="icon-remove"></i>
            <?php echo __('close window'); ?>
        </button> 
    </div>
</form>
<!---->
<script>
    $(document).ready(function() {
        //Fill data
        var formHelper = new DynamicFormHelper('', '', document.frmMain);
        formHelper.BindXmlData();
    });
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');