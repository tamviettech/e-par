<?php
/**


This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}

$arr_all_list_guidance  = isset($arr_all_list_guidance) ? $arr_all_list_guidance : array();
$v_name_linh_vuc        = isset($arr_all_list_guidance[0]['C_NAME_LINH_VUC']) ? $arr_all_list_guidance[0]['C_NAME_LINH_VUC'] : '';
$v_total_rows           = isset($arr_all_list_guidance[0]['C_TOTAL']) ? $arr_all_list_guidance[0]['C_TOTAL'] : 0;
$v_total_page           = ceil($v_total_rows / _CONTS_LIMIT_GUIDANCE_LIST);
$this->template->v_name_linh_vuc = $v_name_linh_vuc;
$this->template->template_is_metro = 'true';
$this->template->title = 'Hướng dẫn thủ tục hành chính';
//$this->template->display('dsp_header.php');
$this->template->display('header_list_and_detail.php');
?>
<style>body{overflow: hidden}</style>
    
<!--Start #main-->
<div id="wrapper" style=""></div>
<div id="main" class="all-list"  >
    <div class="list-head">
         <div class="record-type-title" style="float: left;">
                <h3><span>Lĩnh vực: &nbsp;</span><?php echo $v_name_linh_vuc; ?></h3>
        </div>
        <div class="box-search">
            <form>
                <input maxlength="1000" type="text" name="keyword" id="search" placeholder="Vui lòng nhập mã hoặc tên tủ tục" value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : ''; ?>"/> 
                <input type="submit" value="" id="submit" />
            </form>
        </div>
    </div>
    <div id="box-list-type" style="overflow: scroll">
    <table border="0" cellpadding="0" cellspacing="0">
        <colgroup>
            <col style="width: 5%">
            <col style="width: 5%">
            <col style="width: 70%;" />
            <col style="width: 20%; "/>
        </colgroup>
        <thead style="cursor: context-menu;background: rgba(250, 250, 250, 0.29);" >
        <th style=" color: rgb(94, 86, 86);; font-weight: bold; cursor: context-menu ">STT</th>
        <th style="width:100px;color:  rgb(94, 86, 86);; font-weight: rgb(94, 86, 86);;cursor: context-menu ">Mã thủ tục</th>
        <th style="color:white;text-align: left;padding-left: 20px;color:  rgb(94, 86, 86);; font-weight: bold;cursor: context-menu ">Danh sách các thủ tục</th>
        <th style="color:white;text-align: left;padding-left: 20px;color:  rgb(94, 86, 86);; font-weight: bold;cursor: context-menu ">Phạm vi</th>
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
                    echo '<tr style="background:white;"><td colspan="3">';
                      echo '<h1 style="color:red; width:100%;text-align:center ;margin:20px 0">Không tìn thầy thủ tục nào phù hợp. <a style="color:blue;" href="javascript::void()" onclick="window.history.back(-1)" >Quay lại trang trước</a></h1>';
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
                $v_name         = $arr_all_list_guidance[$i]['C_NAME'];
                $v_id           = $arr_all_list_guidance[$i]['PK_RECORD_TYPE'];
                $v_code         = $arr_all_list_guidance[$i]['C_CODE'];
                $v_village_name = isset($arr_all_list_guidance[$i]['C_SCOPE']) ? $arr_all_list_guidance[$i]['C_SCOPE'] : '';
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
                    <td><?php echo $v_village_name;?></td>
                </tr>

            <?php endfor; ?>
        <?php }?>
           
    </table>
    </div>
</div>
<!--End #main-->
<script>
    //function paging
    function paging(name) 
    {
        var f = document.frmPaging;
        total_page = parseInt(<?php echo $v_total_page; ?>);
        curr_page = parseInt(f.page.value);
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
        f.page.value = 0;
        f.page.value = curr_page;
        if(curr_page > 0)
        {
            f.submit();   
        }
    }    
</script>
    <!--===================- End  main -====================-->
    <!--Start footer-->
    <div id="list-detail-footer">
        <img src="<?php echo SITE_ROOT . 'apps/guidance/' ?>images/bg-bot.png" width="100%" /> 
         <div class="last btn-paging">
        <div class="prev-next">
            <form method="get" name="frmPaging" id="frmPaging">
                <?php   if(isset($_GET['keyword'])):  ?>
                    <input type="hidden" name="keyword" id="keyword" value="<?php echo trim($_GET['keyword']);?>" /> 
                <?php endif;?>
                <input type="hidden" name="page" id="page" value="<?php echo $v_crr_page;?>" /> 
                    <input onclick="<?php  if($v_crr_page > 1){echo 'paging(this);';} else {echo 'window.history.back();';}?>" id="prev" type="button" name="prev" class="prev" value=""/>
                    <input  class="back-home" type="button" name="home" onclick="window.location.href = '<?php echo $this->get_controller_url();?>'"  value=""/>
                    
                <?php
                    //So thu tuc con lai
                    $v_thutuc_con = $v_total_rows - $v_stt;
                ?> 
                <input onclick="paging(this);" id="next" type="button" name="next" class="next" value="<?php // echo $v_thutuc_con; ?>"/>
            </form>
        </div>
    </div>
        <script>
            $(document).ready(function(){
                var height_window  = window.outerHeight || 0;                
                var height_header  =  $('header').outerHeight() ||0;
                var height_footer  =  $('#list-detail-footer').outerHeight() ||0;
                var height_box_srch = $('.list-head').outerHeight() ||0
                $('#box-list-type').height(height_window - (height_header + height_footer + 120 +height_box_srch));  
                $(window).resize(function(){
                    var height_window  = window.outerHeight || 0;                
                    var height_header  =  $('header').outerHeight() ||0;
                    var height_footer  =  $('#list-detail-footer').outerHeight() ||0;
                    var height_box_srch = $('.list-head').outerHeight() ||0
                    $('#box-list-type').height(height_window - (height_header + height_footer + 120 +height_box_srch));  
                });
            });
        </script>      
<?
$this->template->display('dsp_list_and_detail_footer.php');
?>
