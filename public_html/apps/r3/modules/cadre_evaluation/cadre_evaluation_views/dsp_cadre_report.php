<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>

<?php
$this->template->title = 'Danh sách chi tiết kết quả đánh giá cán bộ';
$this->template->display('dsp_header_pop_win.php');

?>
<?php
$v_begin_date   = isset($_REQUEST['txt_begin_date']) ? trim($_REQUEST['txt_begin_date']) : '';
$v_end_date     = isset($_REQUEST['txt_end_date']) ? trim($_REQUEST['txt_end_date']) : '';
?>
<div class="container-fluid">    
    <form name="frmMain" id="frmMain" action="" method="POST" class="form-horizontal">
        <?php
        echo $this->hidden('controller', $this->get_controller_url());
        echo $this->hidden('hdn_dsp_all_method', 'dsp_cadre_report');
        ?>
        <!--Start div.row-fluid boxsearch-->
        <div class="row-fluid">
            <div class="widget-head blue">
                <h3>Danh sách chi tiết kết quả đánh giá cán bộ</h3>
            </div>
            <br />
            Bắt đầu từ
            <div class="input-append">
                <input  style="height: 20px"  
                        type="textbox" 
                        id="txt_begin_date" 
                        name="txt_begin_date" 
                        class="text valid"
                        value="<?php echo $v_begin_date; ?>"
                        onkeydown="return handleEnter(this, event);" 
                        data-allownull="no" data-validate="date" 
                        data-name="Ngày bắt đầu" 
                        data-xml="no" 
                        data-doc="">
                >
                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT ?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp; Đến
            <div class="input-append">
                <input style="height: 20px" 
                       type="textbox" 
                       id="txt_end_date" 
                       name="txt_end_date" 
                       value="<?php echo $v_end_date; ?>"
                       class="text valid" 
                       onkeydown="return handleEnter(this, event);" 
                       data-allownull="no" data-validate="date" 
                       data-name="Ngày kết thúc" 
                       data-xml="no" 
                       data-doc="">

                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT ?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                &nbsp;&nbsp;
                <button style="height: 26px;line-height: 10px;" type="button" class="btn btn-file" onclick="btn_filter_onclick();" name="btn_filter">
                    <i class="icon-search"></i>
                    <?php echo __('filter'); ?>
                </button>
            </div>           
            <button style="float: right;margin-right: 10px;" onclick="btn_print_onlick('<?php echo $v_begin_date; ?>','<?php echo $v_end_date; ?>');" class="btn" type="button">
                <i class="icon-print"></i>
                In báo cáo
            </button>
        </div>
        <!--End  div.row-fluid boxsearch-->
        <br />
        <table width="100%" class="table table-bordered table-striped">
            <col width="5%" />
            <col width="35%" />
            <?php 
                foreach ($arr_all_criteria as $key => $val)
                {
                   $v_list_name      = $val['C_NAME'];
                   $v_list_code    = $val['C_CODE'];
                   echo '<col width="'.  ceil(60/count($arr_all_criteria)).'%" />';
                }
            ?>
            <thead>
                <tr>
                    <th class="center" rowspan="2" >STT</th>
                    <th class="left" rowspan="2" >Họ tên cán bô</th>
                    <th class="center" rowspan="1" colspan="4" >Kết quả đánh giá (Số lần / %)</th>
                </tr>
                <tr>
                    <?php 
                         foreach ($arr_all_criteria as $key => $val)
                         {
                            $v_list_name      = $val['C_NAME'];
                            $v_list_code    = $val['C_CODE'];
                            echo "<th class='center' >$v_list_name</th>";
                         }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php if(sizeof($arr_all_staff)>0) :?>
                <?php for($i= 0;$i<sizeof($arr_all_staff);$i ++):?>
                <?php
                    $v_citizen_name = $arr_all_staff[$i]['C_NAME'];
                    $v_staff_id     = $arr_all_staff[$i]['PK_USER'];
                ?>
                <tr>
                    <td><?php echo $i +1; ?></td>
                    <td><?php echo $v_citizen_name ; ?></td>
                     <?php 
                         foreach ($arr_all_criteria as $key => $val)
                         {
                            $v_list_code    = $val['C_CODE'];
                            $v_vote         = isset($arr_all_report[$v_staff_id]['C_VOTE_'.$v_list_code]) ? $arr_all_report[$v_staff_id]['C_VOTE_'.$v_list_code] : 0;
                            $v_vote_total   = isset($arr_all_report[$v_staff_id]['C_TOTAL_VOTE']) ? $arr_all_report[$v_staff_id]['C_TOTAL_VOTE'] : 0;
                            $v_percent      = round(($v_vote/$v_vote_total),2);
                            echo "<th class='center' >$v_vote&nbsp;(Lần) <br /> $v_percent%</span></th>";
                         }
                    ?>
                </tr>  
                <?php endfor; ?>
                <?php else:;?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>  
                <?php endif;?>
            </tbody>

        </table>
</div>
</form>
</div>
<script>
    
    function btn_print_onlick() 
    {
       var v_url = '<?php echo $this->get_controller_url(); ?>' + 'dsp_print_report' + '&txt_begin_date=<?php echo $v_begin_date ?>' + '&txt_end_date=<?php echo $v_end_date; ?>';
       window.close();
        showPopWin(v_url,800,500,false,true);
    }
</script>
<?php $this->template->display('dsp_footer_pop_win.php'); ?>