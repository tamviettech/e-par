<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}

$arr_all_list_guidance  = isset($arr_all_list_guidance) ? $arr_all_list_guidance : array();
$v_name_linh_vuc        = isset($arr_all_list_guidance[0]['C_NAME_LINH_VUC']) ? $arr_all_list_guidance[0]['C_NAME_LINH_VUC'] : 0;;
$v_total_rows           = isset($arr_all_list_guidance[0]['C_TOTAL']) ? $arr_all_list_guidance[0]['C_TOTAL'] : 0;
$v_total_page           = ceil($v_total_rows / _CONTS_LIMIT_GUIDANCE_LIST);

$this->template->template_is_metro = 'true';
$this->template->title = 'Hướng dẫn thủ tục hành chính';
$this->template->display('dsp_header.php');
?>
<!--Start #main-->
<div id="wrapper" style=""></div>
<div id="main" class="all-list" >
    <div class="list-head">
        <div class="title"><?php echo $v_name_linh_vuc; ?></div>
        <div class="box-search">
            <form>
                <input maxlength="1000" type="text" name="keyword" id="search" placeholder="Vui lòng nhập mã hoặc tên tủ tục" value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : ''; ?>"/> 
                <input type="submit" value="" id="submit" />
            </form>
        </div>
    </div>
    <table>
        <colgroup>
            <col style="width: 20px">
            <col style="width: 50px">
            <col style="width: 100%;" />
        </colgroup>
        <thead >
        <th style="border-right: solid 2px #dddddd; background: #006675;color: white; font-weight: bold; ">STT</th>
        <th style="border-right: solid 2px #dddddd;width:50px;background: #006675;color: white; font-weight: bold; ">Mã thủ tục</th>
        <th style="color:white;text-align: left;padding-left: 20px;background: #006675;color: white; font-weight: bold; ">Danh sách các thủ tục</th>
        </thead>
        <?php
            //lay so page hien tai
            $v_crr_page = isset($_GET['page']) ? intval($_GET['page']) : 0;
            $v_crr_page = ($v_crr_page <= 0) ? 1 : $v_crr_page;
            if($v_crr_page >= $v_total_page)
            {
                $v_crr_page == $v_total_page;
            }
        ?>
            <?php
            //$stt đánh số thu tu cho cac tu tuc khi chuyen trang
            $v_stt = 0;
            if(count($arr_all_list_guidance) <= 0)
            {
                if(isset($_GET['keyword']))
                {
                    echo '<tr><td colspan="3">';
                      echo '<h1 style="color:red; width:100%;text-align:center ;margin:20px 0">Không tìn thầy thủ tục nào phù hợp. <a style="color:blue;" href="#" onclick="history.back()" >Quay lại trang trước</a></h1>';
                    echo '</tr></td>';
                }
                else 
                {
                    echo '<tr><td colspan="3">';
                      echo '<h1>Lĩnh vực này không có thủ tục.</h1>';
                    echo '</tr></td>';
                }
            }
            else
            {
                for ($i = 0; $i < count($arr_all_list_guidance); $i++): 
            ?>
                <?php
                $v_name = $arr_all_list_guidance[$i]['C_NAME'];
                $v_id   = $arr_all_list_guidance[$i]['PK_RECORD_TYPE'];
                $v_code = $arr_all_list_guidance[$i]['C_CODE'];

                if($v_crr_page > 1)
                {
                    $v_stt = $i + 1 + (($v_crr_page - 1) * _CONTS_LIMIT_GUIDANCE_LIST);
                }
                else 
                {
                    $v_stt = $i + 1;
                }

                $v_url  = $this->get_controller_url() . 'dsp_single_guidance/' . $v_id.'?page='.$v_crr_page;
                ?>

                <tr class="<?php echo ($i % 2) ? 'odd' : 'even'; ?>">
                    <td class="stt"><?php echo $v_stt; ?></td>
                    <td class="stt mtt"><?php echo $v_code; ?></td>
                    <td>
                        <a href="<?php echo $v_url ?>"><span class="all-list-content"><?php echo $v_name; ?></span></a>
                    </td>
                </tr>

            <?php endfor; ?>
        <?php }?>
        <tr class="last">
            <td colspan="3" >
                
                <div class="prev-next">
                    <form method="get" name="frmPaging" id="frmPaging">
                        <?php   if(isset($_GET['keyword'])):  ?>
                            <input type="hidden" name="keyword" id="keyword" value="<?php echo trim($_GET['keyword']);?>" /> 
                        <?php endif;?>
                        <input type="hidden" name="page" id="page" value="<?php echo $v_crr_page;?> " /> 
                        <?php if($v_crr_page > 1):?>
                            <input onclick="paging(this);" id="prev" type="button" name="prev" class="prev" value="Trang trước"/>
                        <?php endif; ?>
                        <a class="back-home" href="<?php echo $this->get_controller_url(); ?>">Giao diện chính</a>
                        <?php
                            //So thu tuc con lai
                            $v_thutuc_con = $v_total_rows - $v_stt;
                            if($v_thutuc_con > 0):
                        ?> 
                        <input onclick="paging(this);" id="next" type="button" name="next" class="next" value="Trang sau - còn:&nbsp;<?php echo $v_thutuc_con; ?>"/>
                        <?php endif;?>
                    </form>
                </div>
            </td>
        </tr>
    </table>
</div>
<!--End #main-->
<script>
    //function paging
    function paging(name) 
    {
        var f = document.frmPaging;
        total_page = parseInt(<?php echo $v_total_page; ?>);
        curr_page = parseInt($('#page').val());
        if(name.id ==  'prev')
        {
            curr_page -= 1;
            if(curr_page <= 0)
            {
                curr_page = 0;
            }
        }
        else
        {
            curr_page += 1 ; 
            if(curr_page  >=  total_page)
            {
                curr_page = total_page;
            }
        }
        $('#page').removeAttr('value');
        $('#page').attr('value',curr_page);
        if(curr_page > 0)
        {
            f.submit();   
        }
    }    
</script>
<?
$this->template->display('dsp_footer.php');
?>
