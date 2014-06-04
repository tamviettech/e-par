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
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>

<script src="<?php echo FULL_SITE_ROOT; ?>/public/themes/bootstrap/js/jquery.flot.js"></script>
<script src="<?php echo FULL_SITE_ROOT; ?>public/themes/bootstrap/js/jquery.flot.pie.js"></script>
<script src="http://www.google.com/jsapi"></script>
<script>
$(document).ready(function (){
    create_process_pie_chart();
    create_process_bar_chart();
});

    //xu ly select year
    function sel_year_onclick(seleted)
    {
        year = $(seleted).val();

        func = $(seleted).find('option:selected').attr('data-function');

        //goi ham theo data-function cua option dc chon
        window[func](year);
    }

    function create_process_pie_chart(year)
    {
         if(typeof(year) == 'undefined')
        {
            year = '';
        }

        $.ajax({
            url: "<?php echo $this->get_controller_url() . 'arp_get_record_process/';?>" + year,
            success: function(res){
                obj = jQuery.parseJSON(res);

                //tong so ban ghi
                total_record = obj.C_TOTAL_RECORD;

                //tinh % dang xu ly - cham tien do
                dxl_cham = (obj.C_COUNT_CHUA_TRA_CHAM_TIEN_DO/total_record)*100;
                dxl_cham = Math.round(dxl_cham);
                //tinh % dang xu ly - chua den han
                dxl_chua_den_han = (obj.C_COUNT_CHUA_TRA_CHUA_DEN_HAN/total_record)*100;
                dxl_chua_den_han = Math.round(dxl_chua_den_han);
                //tinh % da tra - dung han
                dt_som_han = (obj.C_COUNT_DA_TRA_SOM_HAN/total_record)*100;
                dt_som_han = Math.round(dt_som_han);
                //tinh % da tra - dung han
                dt_dung_han = (obj.C_COUNT_DA_TRA_DUNG_HAN/total_record)*100;
                dt_dung_han = Math.round(dt_dung_han);
                //tinh % da tra - cham han
                dt_cham_han = 100 - (dxl_cham + dxl_chua_den_han + dt_som_han + dt_dung_han);



                var data = [{
                        label: "Đang xử lý - Chậm tiến độ",
                        data: dxl_cham,
                        color: '#DA3610'
                    }, {
                        label: "Đang xử lý - Chưa đến hạn",
                        data: dxl_chua_den_han,
                        color: '#3498DB'
                    }, {
                        label: "Đã trả - Sớm hạn",
                        data: dt_som_han,
                        color: '#4DA74D'
                    },  {
                        label: "Đã trả - Đúng hạn",
                        data: dt_dung_han,
                        color: '#AFD8F8'
                    },  {
                        label: "Đã trả - Chậm hạn",
                        data: dt_cham_han,
                        color: '#EDC240'
                    }
                ];

                var options = {
                        series: {
                            pie: {
                                show: true                            
                            }
                        },
                        legend: {
                            show: false
                        }
                    };

                $.plot($("#chartContainer-pie"), data, options);
            }
          });
    }

    //Ve bieu do hinh thanh cot
        google.load("visualization", "1", {packages:["corechart"]});
        google.setOnLoadCallback(drawChart);
     
        function drawChart(obj) 
        {       
            var TIEP_NHAN = obj.TIEP_NHAN || [];
            var DA_TRA    = obj.DA_TRA || [];
            var data = google.visualization.arrayToDataTable([
                ['',        'Tiếp nhận',                        'Đã trả'],
                ['Tháng 1',  parseInt(get_val_of_obj(TIEP_NHAN,1,0)),     parseInt(get_val_of_obj(DA_TRA,1,0))],
                ['Tháng 2',  parseInt(get_val_of_obj(TIEP_NHAN,2,0)),     parseInt(get_val_of_obj(DA_TRA,2,0))],
                ['Tháng 3',  parseInt(get_val_of_obj(TIEP_NHAN,3,0)),     parseInt(get_val_of_obj(DA_TRA,3,0))],
                ['Tháng 4',  parseInt(get_val_of_obj(TIEP_NHAN,4,0)),     parseInt(get_val_of_obj(DA_TRA,4,0))],
                ['Tháng 5',  parseInt(get_val_of_obj(TIEP_NHAN,5,0)),     parseInt(get_val_of_obj(DA_TRA,5,0))],
                ['Tháng 6',  parseInt(get_val_of_obj(TIEP_NHAN,6,0)),     parseInt(get_val_of_obj(DA_TRA,6,0))],
                ['Tháng 7',  parseInt(get_val_of_obj(TIEP_NHAN,7,0)),     parseInt(get_val_of_obj(DA_TRA,7,0))],
                ['Tháng 8',  parseInt(get_val_of_obj(TIEP_NHAN,8,0)),     parseInt(get_val_of_obj(DA_TRA,8,0))],
                ['Tháng 9',  parseInt(get_val_of_obj(TIEP_NHAN,9,0)),     parseInt(get_val_of_obj(DA_TRA,9,0))],
                ['Tháng 10', parseInt(get_val_of_obj(TIEP_NHAN,10,0)),    parseInt(get_val_of_obj(DA_TRA,10,0))],
                ['Tháng 11', parseInt(get_val_of_obj(TIEP_NHAN,11,0)),    parseInt(get_val_of_obj(DA_TRA,11,0))],
                ['Tháng 12', parseInt(get_val_of_obj(TIEP_NHAN,12,0)),    parseInt(get_val_of_obj(DA_TRA,12,0))]
            ]);
            var options = {
                title: 'Tiêu đề',
                hAxis: {title: 'Biểu đồ tiếp nhận và đã trả hồ sơ', titleTextStyle: {color: 'red'}},
                series: [{color: '#5b3ab6'},{color: '#a300aa'}]
            };
            var chart = new google.visualization.ColumnChart(document.getElementById('chartContainer-bar'));
            chart.draw(data, options);
        } 
        
    function create_process_bar_chart(year) 
    {
        if(typeof(year) == 'undefined')
        {
            year = '';
        }
        
        $.ajax({
            url: "<?php echo $this->get_controller_url() . 'arp_get_record_receive_respond/'; ?>" + year,
            success: function(res){
                    obj = jQuery.parseJSON(res);
                    drawChart(obj);
                    console.log(obj);
                }
            });
       }
    function get_val_of_obj(obj,val_name,default_val)
    {
        if(typeof(obj[val_name]) == 'undefined')
        {
            if(typeof(default_val) == 'undefined')
            {
                return '';
            }
            else
            {
                return default_val;
            }
        }
        else
        {
            return obj[val_name]
        }
    }
</script>

<div class="clear" ></div>
<div  class="group-option" id="home"> 
    <div class="span12" >
        <div id="pie-chart" class="span5">
            <div class="content-widgets light-gray">
                <div class="widget-head blue">
                    <h3>Biểu đồ sử lý tiến độ hồ sơ</h3>
                </div>
                <!--End .widget-head blue-->

                <div id="box-chart" class="widget-container" >
                    <div class="span12">                        
                        <div id="chartContainer-pie" class="case-container" style="width: 100%; height: 300px;"></div>
                        
                        <div style="width: 100%;text-align: right">
                        <strong>Năm:</strong>&nbsp;&nbsp;
                        <select onchange="sel_year_onclick(this);">
                            <?php
                            $min_year = $arr_year['C_MIN_YEAR'];
                            $max_year = $arr_year['C_MAX_YEAR'];
                            $year = $min_year;

                            $v_loop = $max_year - $min_year;
                            for ($i = 0; $i <= $v_loop; $i++):
                                $year = $year + $i;
                                ?>
                                <option <?php echo ($year == DATE('Y')) ? 'selected' : ''; ?> value="<?php echo $year ?>" data-function="create_process_pie_chart">
                                    <?php echo $year ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>            
        <!--End #box-chart-->
        <!--Start sticky-->
        <div  class="span3">
            <div class="content-widgets light-gray">
                <div class="widget-head blue">
                    <h3>Tin tiêu điểm</h3>
                </div>
                <!--End .widget-head blue-->
                <div id="box-sticky" class="widget-container" >
                    <div class="gp-silde">
                        <ul>
                            <?php
                            $arr_all_sticky = isset($arr_all_sticky) ? $arr_all_sticky :array();
                            $v_condition = (count($arr_all_sticky) > CONST_LIMIT_STICKY_HOME_PAGE) ? CONST_LIMIT_STICKY_HOME_PAGE : count($arr_all_sticky);
                            
                            for ($i = 0; $i < $v_condition; $i++):
                                
                                $arr_sticky     = $arr_all_sticky[$i];
                                $v_category_id  = $arr_sticky['FK_CATEGORY'];
                                $v_slug_cat     = $arr_sticky['C_SLUG_CATEGORY'];
                                $v_article_id   = $arr_sticky['FK_ARTICLE'];
                                $v_title        = $arr_sticky['C_TITLE'];
                                $v_summary      = remove_html_tag(htmlspecialchars_decode($arr_sticky['C_SUMMARY']));
                                $v_content      = isset($arr_sticky['C_CONTENT']) ? $arr_sticky['C_CONTENT'] : ''; 
                                $v_content      = remove_html_tag(htmlspecialchars_decode($v_content));
                                $v_slug_art     = $arr_sticky['C_SLUG_ARTICLE'];
                                $v_order        = $arr_sticky['C_ORDER'];
                                $v_file_name    = isset($arr_sticky['C_FILE_NAME']) ? $arr_sticky['C_FILE_NAME'] : '';
                                $v_begin_date   = $arr_sticky['C_BEGIN_DATE'];
                                $v_has_video    = $arr_sticky['C_HAS_VIDEO'];
                                $v_has_photo    = $arr_sticky['C_HAS_PHOTO'];
                                
                                $interval   = date_diff(date_create($v_begin_date), new DateTime("now"))->format('%a');
                                $v_img_url  = '';
                                $v_img_path = SERVER_ROOT . "uploads". DS ."public_service" . DS . $v_file_name;
                                if(is_file($v_img_path))
                                {
                                    $v_img_url = SITE_ROOT . "uploads/public_service/" . $v_file_name;
                                }
                                ?>
                            <li class="Row <?php echo ($i % 2 != 0) ? 'even' : ''; ?>">
                                    <div class="item">
                                        <?php if($v_img_url != ''): ?>
                                        <div class="item-left">
                                            <a href="<?php echo build_url_article($v_slug_cat, $v_slug_art, $v_category_id, $v_article_id) ?>">
                                            <img  src="<?php echo $v_img_url; ?>" width="50px" height="auto"/>
                                            </a>
                                        </div>
                                        <?php else:?>
                                        <i style="float: left" class=" icon-caret-right"></i>
                                        <?php endif;?>
                                        <div class="item-right">
                                            <a href="<?php echo build_url_article($v_slug_cat, $v_slug_art, $v_category_id, $v_article_id) ?>"><h3 class="title"><?php echo $v_title; ?></h3></a>
                                             <?php 
                                                if(trim($v_summary) != '')
                                                {
                                                    echo get_leftmost_words(remove_html_tag($v_summary), 30);
                                                }
                                                else
                                                {
                                                    echo get_leftmost_words(remove_html_tag($v_content), 30);
                                                }
                                                     ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                    <script>
                        $(function(){
                            $('#box-sticky .gp-silde').slimScroll({
                                height: '371px'
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
        <!--End Sticky-->
        
        <div id="box-search" class="span4">
            <div  class="content-widgets light-gray">
                <div class="widget-head blue">
                    <h3>Tra cứu</h3>
                </div>
                <!--End .widget-head blue-->

                <div id="box-search-content" class="widget-container">
                    <form class="form-horizontal" name="frmMain" id="frmMain" action="<?php echo $this->get_controller_url();?>dsp_search" method="post"  >
                        <div class="control-group">

                            <label class="control-label span3">Mã hồ sơ &nbsp;</label>
                            <div class="controls" style="margin-left: 65px">                             
                                <input class="span7" type="text" style="" name="txt_record_no" onchange="$(this).val($(this).val().toUpperCase());" id="txt_record_no" maxlength="100" onkeypress=" txt_filter_onkeypress(this.form.btn_filter, event)" value="">
                                <button type="submit" class="btn btn-primary" onclick=""> Xác nhận <i class="icon-search"></i></button>
                            </div>
                        </div>
                        <!--End #txt_code-->


                        <!--End btn submit-->
                    </form>
                    <!--End #frmMain-->
                </div>
            </div>
            <!--End #box-serach-->
            
            <!--Start box poll-->
        </div>

        
        <div  class="span4">
            <div id="box-poll" class="content-widgets light-gray">
                <div class="widget-head blue">
                    <h3>Thăm dò ý kiến</h3>
                </div>
                <!--End .widget-head blue-->
                <div id="box-poll-content" class="widget-container">
                    <?php if (!empty($arr_single_poll)): ?>
                        <?php
                        $v_poll_name = $arr_single_poll['C_NAME'];
                        $v_poll_id   = $arr_single_poll['PK_POLL'];
                        $ck_begin    = $arr_single_poll['CK_BEGIN_DATE'];
                        $ck_end      = $arr_single_poll['CK_END_DATE'];
                        $v_disable   = Cookie::get('WIDGET_POLL_' . $v_poll_id) ? 'disabled' : ''; 
                    ?>

                        <p class='widget-content-title'><?php echo $v_poll_name ?></p>

                        <form>
                            <input type='hidden' id="hdn_poll_id" name='hdn_poll_id' value='<?php echo $v_poll_id ?>'/>
                            <input type='hidden' name='hdn_answer_id' value=''/>
                            <?php 
                                $arr_all_opt = isset($arr_all_opt) ? $arr_all_opt :array();
                                $n = count($arr_all_opt); 
                            ?>
                            <?php
                            for ($i = 0; $i < $n; $i++):
                                ?>
                                <?php
                                $item = $arr_all_opt[$i];
                                $v_opt_val = $item['PK_POLL_DETAIL'];
                                $v_opt_answer = $item['C_ANSWER'];
                                ?>
                                <label>
                                    <input type="radio" name='rad_widget_poll_<?php echo $v_index ?>' value='<?php echo $v_opt_val ?>' onclick="this.form.hdn_answer_id.value=this.value"/>
                                    <?php echo $v_opt_answer ?></br>
                                </label>

                            <?php endfor; ?>
                            </br>
                            <?php if ($ck_begin < 0 or $ck_end < 0): ?>
                                <?php echo __('this poll is expired'); ?>
                            <?php else: ?>
                                <a class="vote" href="javascript:;" onCLick='btn_vote_onclick(this);' >
                                    <span>
                                        <?php echo $v_disable ? __('thank you for voting') : __('vote') ?>
                                    </span>
                                </a>
                            <?php endif; ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <a class="a_poll_result" href='javascript:;' onClick="dsp_poll_result(this)">
                                <?php echo __('see result') ?>
                            </a>
                        </form>
                    <?php endif; ?>
                    
                </div>
            </div>
            <!--End box poll-->
        </div>
    </div>
    <!--End .span12-->

    <div class="full-width">
        <div id="box-bar-chart" class="span12">
            <div class="content-widgets light-gray">
                <div class="widget-head blue">
                    <h3>Biểu đồ sử lý tiến độ hồ sơ</h3>
                </div>
                <!--End .widget-head blue-->

                <div id="box-chart" class="widget-container" >
                    <div id="legendcontainer26"></div>
                    <div id="chartContainer-bar" class="case-container" style="width: 100%; height: 300px;"></div>
                      <div style="width: 100%;text-align: right">
                        <strong>Năm:</strong>&nbsp;&nbsp;
                            <select onchange="sel_year_onclick(this);">
                                <?php
                                    $min_year = $arr_year['C_MIN_YEAR'];
                                    $max_year = $arr_year['C_MAX_YEAR'];
                                    $year = $min_year;

                                    $v_loop = $max_year - $min_year;
                                    for ($i = 0; $i <= $v_loop; $i++):
                                    $year = $year + $i;
                                ?>
                                    <option <?php echo ($year == DATE('Y')) ? 'selected' : ''; ?> value="<?php echo $year ?>" data-function="create_process_bar_chart">
                                        <?php echo $year ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                </div>
            </div>
        </div>
        <!--End #box-bar-chart-->
    </div>
    <!--End .span12-->
</div>
<div id="widget_poll_modal" name="widget_poll_modal" style="display: none;"></div>

<script>
        function dsp_poll_result($a_obj)
        {
            $v_id = $($a_obj).parents('form:first').find('#hdn_poll_id').val();
            $url = "<?php echo $this->get_controller_url() ?>" + 'dsp_poll_result/' + $v_id;
            $('#widget_poll_modal').attr('title','<?php echo __('poll result') ?>').html('<iframe src="'+ $url +'" style="width:100%;height:100%;border:none;"></iframe>').dialog({
                width: 500,
                height: 300,
                modal: true
            });
        }
        function btn_vote_onclick($btn_obj){
            aid = $($btn_obj).parents('form:first').find('[name=hdn_answer_id]').val();   
            pid = $($btn_obj).parents('form:first').find('[name=hdn_poll_id]').val();  
            if(!aid || !pid){
                return;
            }
            url= "<?php echo $this->get_controller_url() ?>" + 'handle_widget_poll/';
            url += '&code=poll';
            url += '&pid=' + pid;
            url += '&aid=' + aid;
            $('#widget_poll_modal').attr('title','<?php echo __('please enter captcha') ?>').html('<iframe src="'+ url +'" style="width:100%;height:100%;border:none;"></iframe>').dialog({
                width: 500,
                height: 300,
                modal: true
            });
        }
        function close_widget_poll_model(){
            $('#widget_poll_modal').dialog('close');
        }
                                
    </script>