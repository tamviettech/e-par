<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');
$this->template->title = 'Danh sách hồ sơ bị cảnh báo';
$this->template->display('dsp_header_pop_win.php');
?>
<div class="container-fluid">
    <form name="frmMain" id="frmMain" action="" method="POST" class="form-horizontal" style="min-height: 400px;margin-top: 10px">
        <?php
            echo $this->hidden('controller',$this->get_controller_url());
        ?>
        <div class="widget-head blue">
            <h3>Danh sách hồ <?php echo $type_name;?></h3>
        </div>
        <table width="100%" class="adminlist table table-bordered table-striped">
            <colgroup>
                <col width="*%">
                <col width="16%">
                <col width="20%">
                <col width="20%">
                <col width="14%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr>
                    <th>Mã hồ sơ</th>
                    <th>Người đăng ký</th>
                    <th>Ngày giờ tiếp nhận</th>
                    <th>Ngày giờ hẹn trả</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($arr_all_record as $arr_record):
                        $v_record_id              = $arr_record['PK_RECORD'];
                        $v_citizen_name           = $arr_record['C_CITIZEN_NAME'];
                        $v_doing_step_days_remain = $arr_record['C_RETURN_DAYS_REMAIN'];
                        $v_record_no              = $arr_record['C_RECORD_NO'];
                        $v_receive_date           = $arr_record['C_RECEIVE_DATE'];
                        $v_return_date            = $arr_record['C_RETURN_DATE'];
                        $v_xml_processing         = $arr_record['C_XML_PROCESSING'];
                        
//                        $v_step_time              = $arr_record['C_STEP_TIME'];
//                        $v_step_begin_date        = $arr_record['C_DOING_STEP_BEGIN_DATE'];
//                        $v_step_deadline_date     = $arr_record['C_DOING_STEP_DEADLINE_DATE'];
//                        $v_next_role              = $arr_record['C_NEXT_TASK_CODE'];
                        
                        $v_reason = '';
                        $v_next_task_is_no_chain = false;
                        if($v_xml_processing != '')
                        {
                            $dom = simplexml_load_string($v_xml_processing);
                            $result = $dom->xpath('//step[last()]/reason');
//                            $v_next_task_is_no_chain = get_xml_value($dom, "//task[@code='$v_next_role']/../@no_chain" );
                            $v_reason = (string) $result[0];
                        }
                ?>
                <tr class="row0">
                    <td>
                        <?php echo $v_record_no?>
                        <?php if(!empty($v_reason)):?>
                        <br/>
                        <span class="reason"><i><img src="/tp-bacgiang/public/images/icon-32-message.png" width="20" height="20"><?php echo $v_reason?></i></span>
                        <?php endif;?>
                    </td>
                    <td><?php echo $v_citizen_name;?></td>
                    <td><?php echo $v_receive_date;?></td>
                    <td >
                        <?php echo $v_return_date;?>
                        <br/>
                        <span class="days-remain during">(Còn <?php echo $v_doing_step_days_remain?> ngày)</span>
                    </td>
                    <td style="text-align: center;">
                        <div class="quick_action">
                            <a href="javascript:void(0)" onclick="dsp_record_statistics('<?php echo $v_record_id;?>');" class="quick_action">
                                <img src="/tp-bacgiang/public/images/statistics-16x16.png" title="Xem tiến độ">
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach;?>
                <?php
                    $rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
                    if(count($arr_all_record) < $rows_per_page)
                    {
                        $v_loop = $rows_per_page - count($arr_all_record);
                        for($i=0;$i<$v_loop;$i++)
                        {
                            echo '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>';
                        }
                    }
                ?>
            </tbody>
        </table>
        <div><?php echo $this->paging2($arr_all_record);?></div>
    </form>
</div>
<script>
    function dsp_record_statistics(record_id, tab)
    {
        var url = $("#controller").val() + 'statistics/' + record_id + '&hdn_item_id=' + record_id + '&pop_win=1' ;
        if (typeof(tab) !== 'undefined')
        {
            url += '&tab=' + tab;
        }
        showPopWin(url, 700, 500);
    }
</script>
<?php $this->template->display('dsp_footer_pop_win.php');