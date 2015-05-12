<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>

<?php
$this->template->title = 'Báo cáo kết quả đánh giá cán bộ';
$this->template->display('dsp_header_pop_win.php');
?>
<?php
$v_begin_date = isset($_REQUEST['txt_begin_date']) ? trim($_REQUEST['txt_begin_date']) : '';
$v_end_date = isset($_REQUEST['txt_end_date']) ? trim($_REQUEST['txt_end_date']) : '';
$dom_unit_info = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');

?>
<style> *{background: white;}</style>
<link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/reset.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/text.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/printer.css" type="text/css" media="all" />
<script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
<div class="print-button">
    <input type="button" value="In trang" onclick="window.print(); return false;" />
    <input type="button" value="Đóng cửa sổ" onclick="window.parent.hidePopWin()" />
</div>
<!-- header -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="header">
    <tr>
        <td align="center" class="unit_full_name">
            <strong><?php echo get_xml_value($dom_unit_info, '/unit/full_name'); ?></strong><br/>
        </td>
        <td align="center">
            <span style="font-size: 12px">
                <strong>CỘNG HOÀ XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong>
            </span>
            <br/>
            <strong>
                <u style="font-size: 10px">Độc lập - Tự do - Hạnh phúc</u>
            </strong>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="report-title">
            <span class="title-1">BÁO CÁO KẾT QUẢ ĐÁNH GIÁ CÁN BỘ</span><br/>
            <span class="title-2"><strong>Vào lúc: </strong><?php echo date('d/m/Y H:i:s '); ?></span>
        </td>
    </tr>
</table>
<div class="container-fluid" style="border: none;background: white;">
    <br/>
    <label style="margin-bottom: 5px;" >
        <strong>Ngày bắt đầu đánh giá: </strong> <i><?php echo (trim($v_begin_date) == '') ? '&nbsp;&nbsp;............&nbsp;&nbsp;&nbsp;' : '&nbsp;&nbsp;'.$v_begin_date; ?></i>
        &nbsp;&nbsp;&nbsp;<strong>ngày kết thúc</strong> &nbsp;&nbsp;&nbsp;&nbsp;<i><?php echo (trim($v_end_date) == '') ? '&nbsp;&nbsp;............&nbsp;&nbsp;&nbsp;' : '&nbsp;&nbsp;&nbsp;'.$v_end_date; ?></i></label>    

    
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
                    <td  style="font-weight: normal"><?php echo $i+1; ?></td>
                    <td ><?php echo $v_citizen_name ; ?></td>
                     <?php 
                         foreach ($arr_all_criteria as $key => $val)
                         {
                            $v_list_code    = $val['C_CODE'];
                            $v_vote         = isset($arr_all_report[$v_staff_id]['C_VOTE_'.$v_list_code]) ? $arr_all_report[$v_staff_id]['C_VOTE_'.$v_list_code] : 0;
                            $v_vote_total   = isset($arr_all_report[$v_staff_id]['C_TOTAL_VOTE']) ? $arr_all_report[$v_staff_id]['C_TOTAL_VOTE'] : 0;
                            $v_percent      = round(($v_vote/$v_vote_total),2);
                            echo "<th  style='font-weight: normal' class='center' >$v_vote&nbsp;(Lần) <br /> $v_percent%</span></th>";
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
      <!--FOOTER-->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tbl-signer">
           <tr>
               <td style="height: 150px; text-align:right;padding-right: 70px;padding-top: 10px;">
                   <div style="text-align: center;float: right">
                       <strong>CÁN BỘ IN BÁO CÁO</strong><br />
                        <i>(Ký, ghi rõ họ tên)</i><br />
                        <i style="display: block;height: 30px;">&nbsp;</i>
                        <i><?php echo session::get('user_name')?></i>                      
                   </div>
               </td>
           </tr>
       </table>
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