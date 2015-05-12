<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//header
$this->template->title = 'Biểu đồ tiến độ';
$this->template->display('dsp_header_pop_win.php');

?>
<style>
    /*.flot-y-axis .flot-tick-label.tickLabel{left:-15px !important}*/
    .width_50
    {
        width: 50px;
        display: inline-table !important;
    }
</style>
<script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery-ui-1.8.16.custom.min.js"></script>
<script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery.flot.js"></script>
<script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery.flot.selection.js"></script>
<script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery.flot.pie.js"></script>
<script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery.flot.stack.js"></script>
<script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery.flot.tooltip.js"></script>
<script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery.flot.resize.js"></script>

<script>
$(document).ready(function (){
    create_progress_pie_chart();
    create_recevice_respond_bar_chart();
});

//xu ly select year
function sel_year_onclick(seleted)
{
    var year = $(seleted).val();
    
    var func = $(seleted).find('option:selected').attr('data-function');
    
    //goi ham theo data-function cua option dc chon
    window[func](year);
}

//tao bieu do hinh cot tiep nhan - ban giao ho so
function create_recevice_respond_bar_chart(year)
{
    if(typeof(year) == 'undefined')
    {
        year = '';
    }
    
    $.ajax({
        url: "<?php echo $this->get_controller_url() . 'arp_get_record_receive_respond/';?>" + year,
        success: function(res){
            var obj = jQuery.parseJSON(res);
            var TIEP_NHAN = obj.TIEP_NHAN;
            var DA_TRA    = obj.DA_TRA;
            var data_da_tra = [
                [34, get_val_of_obj(DA_TRA,12,0)],
                [31, get_val_of_obj(DA_TRA,11,0)],
                [28, get_val_of_obj(DA_TRA,10,0)],
                [25, get_val_of_obj(DA_TRA,9,0)],
                [22, get_val_of_obj(DA_TRA,8,0)],
                [19, get_val_of_obj(DA_TRA,7,0)],
                [16, get_val_of_obj(DA_TRA,6,0)],
                [13, get_val_of_obj(DA_TRA,5,0)],
                [10, get_val_of_obj(DA_TRA,4,0)],
                [7, get_val_of_obj(DA_TRA,3,0)],
                [4, get_val_of_obj(DA_TRA,2,0)],
                [1, get_val_of_obj(DA_TRA,1,0)],
            ];
            
            var data_tiep_nhan = [
                [33, get_val_of_obj(TIEP_NHAN,12,0)],
                [30, get_val_of_obj(TIEP_NHAN,11,0)],
                [27, get_val_of_obj(TIEP_NHAN,10,0)],
                [24, get_val_of_obj(TIEP_NHAN,9,0)],
                [21, get_val_of_obj(TIEP_NHAN,8,0)],
                [18, get_val_of_obj(TIEP_NHAN,7,0)],
                [15, get_val_of_obj(TIEP_NHAN,6,0)],
                [12, get_val_of_obj(TIEP_NHAN,5,0)],
                [9, get_val_of_obj(TIEP_NHAN,4,0)],
                [6, get_val_of_obj(TIEP_NHAN,3,0)],
                [3, get_val_of_obj(TIEP_NHAN,2,0)],
                [0, get_val_of_obj(TIEP_NHAN,1,0)]
            ];
            var ticks = [
                [0, "Tháng 1"],
                [1, ""],
                [2, ""],
                [3, "Tháng 2"],
                [4, ""],
                [5, ""],
                [6, "Tháng 3"],
                [7, ""],
                [8, ""],
                [9, "Tháng 4"],
                [10, ""],
                [11, ""],
                [12, "Tháng 5"],
                [13, ""],
                [14, ""],
                [15, "Tháng 6"],
                [16, ""],
                [17, ""],
                [18, "Tháng 7"],
                [19, ""],
                [20, ""],
                [21, "Tháng 8"],
                [22, ""],
                [23, ""],
                [24, "Tháng 9"],
                [25, ""],
                [26, ""],
                [27, "Tháng 10"],
                [28, ""],
                [29, ""],
                [30, "Tháng 11"],
                [31, ""],
                [32, ""],
                [33, "Tháng 12"],
                [34, ""]
            ];
            var data = [
                {   
                    label: "Tiếp nhận",
                    data: data_tiep_nhan,
                    bars: {
                        show: true
                    }
                },{
                    label: "Đã trả",
                    data: data_da_tra,
                    bars: {
                        show: true
                    }
                }
            ];
            var options = {
                xaxis: {
                    ticks: ticks,
                    axisLabelPadding: 10
                },
                tooltip: true,
                series: {
                    shadowSize: 0
                },
                grid: {
                    hoverable: true,
                    clickable: true,
                    borderWidth: 1,
                    autoHighlight: true,
                    
                    labelMargin: 20
                },
                colors: ["#b086c3", "#ea701b"],
                tooltipOpts: {
                    defaultTheme: false,
                    content: function(label,xval,yval){
                        var content = "%s: " + xval + ' hồ sơ';
                        return content;
                    }
                },
                legend: {
                    labelBoxBorderColor: "#000000",
                    container: $("#legendcontainer26"),
                    noColumns: 0
                },
            };
            var plot = $.plot($("#combine-chart #combine-chartContainer"),data, options);
        }
    });
}
//tao bieu do hinh tron - tien do cua ho so
function create_progress_pie_chart(year)
{
    if(typeof(year) == 'undefined')
    {
        year = '';
    }
    
    $.ajax({
        url: "<?php echo $this->get_controller_url() . 'arp_get_record_progress/';?>" + year,
        success: function(res){
            var obj = jQuery.parseJSON(res);
            //tong so ban ghi
            var total_record = obj.C_TOTAL_RECORD;
            
            //tinh % dang xu ly - cham tien do
            var dxl_cham = (obj.C_COUNT_CHUA_TRA_CHAM_TIEN_DO/total_record)*100;
            var dxl_cham = Math.round(dxl_cham);
            //tinh % dang xu ly - chua den han
            var dxl_chua_den_han = (obj.C_COUNT_CHUA_TRA_CHUA_DEN_HAN/total_record)*100;
            var dxl_chua_den_han = Math.round(dxl_chua_den_han);
            //tinh % da tra - dung han
            var dt_som_han = (obj.C_COUNT_DA_TRA_SOM_HAN/total_record)*100;
            var dt_som_han = Math.round(dt_som_han);
            //tinh % da tra - dung han
            var dt_dung_han = (obj.C_COUNT_DA_TRA_DUNG_HAN/total_record)*100;
            var dt_dung_han = Math.round(dt_dung_han);
            //tinh % da tra - cham han
            var dt_cham_han = 100 - (dxl_cham + dxl_chua_den_han + dt_som_han + dt_dung_han);
            
//             var dxl_cham         = obj.C_COUNT_CHUA_TRA_CHAM_TIEN_DO;
//             var dxl_chua_den_han = obj.C_COUNT_CHUA_TRA_CHUA_DEN_HAN;
//             var dt_som_han       = obj.C_COUNT_DA_TRA_SOM_HAN;
//             var dt_dung_han      = obj.C_COUNT_DA_TRA_DUNG_HAN;
//             var dt_cham_han      = total_record - (dxl_cham + dxl_chua_den_han + dt_som_han + dt_dung_han);
            var value_tra_cham = parseInt(total_record) - (parseInt(obj.C_COUNT_DA_TRA_DUNG_HAN) + parseInt(obj.C_COUNT_DA_TRA_SOM_HAN) + parseInt(obj.C_COUNT_CHUA_TRA_CHUA_DEN_HAN) + parseInt(obj.C_COUNT_CHUA_TRA_CHAM_TIEN_DO));
			
            var data = [{
                    label: "Đang xử lý - Quá hạn",
                    data: dxl_cham,
                    value: obj.C_COUNT_CHUA_TRA_CHAM_TIEN_DO,
                    color: '#DA3610',
                    note: 'Hồ sơ đang xử lý quá hạn so với ngày hẹn trả'
                }, {
                    label: "Đang xử lý - Chưa đến hạn",
                    data: dxl_chua_den_han,
                    value: obj.C_COUNT_CHUA_TRA_CHUA_DEN_HAN,
                    color: '#3498DB',
                    note: 'Hồ sơ đang xử lý chưa đến hạn so với ngày hẹn trả'
                }, {
                    label: "Đã trả - Sớm hạn",
                    data: dt_som_han,
                    value: obj.C_COUNT_DA_TRA_SOM_HAN,
                    color: '#4DA74D',
                    note: 'Hồ sơ đã trả sớm hạn so với ngày hẹn trả'
                },  {
                    label: "Đã trả - Đúng hạn",
                    data: dt_dung_han,
                    value: obj.C_COUNT_DA_TRA_DUNG_HAN,
                    color: '#AFD8F8',
                    note: 'Hồ sơ đã trả đúng hạn so với ngày hẹn trả'
                },  {
                    label: "Đã trả - Chậm hạn",
                    data: dt_cham_han,
                    value: value_tra_cham,
                    color: '#EDC240',
                    note: 'Hồ sơ đã trả chậm hạn so với ngày hẹn trả'
                }
            ];
            
            var options = {
                    series: {
                        pie: {
                            show: true,
                        },
                    },
                   tooltip: true,
                    grid: {
                        hoverable: true,
                        clickable: true,
                        borderWidth: 1
                    },
                    tooltipOpts: {
                        defaultTheme: false,
                        content: function(label,xval,yval){
                            var content = "%s: " + xval + '%';
                            return content;
                        }
                    },
                    legend: {
                        show: false
                    },
                };
                
            $.plot($("#pie-chart #pie-chartContainer"), data, options);
            show_chart_info($('#pie_chart_info'),data)
        }
      });
}
function show_chart_info(div_note,data)
{
    var label = '';
    var color = '';
    var note = '';
    var value  = '';
    for (var key in data)
    {
        label = data[key].label;
        color = data[key].color;
        value = number_format(parseInt(data[key].value),0);
        note = data[key].note;
        if(note != '' && parseInt(data[key].data) > 0)
        {
            $(div_note).append('<div><label class="width_50" style="background-color: '+color+'">&nbsp;</label>&nbsp;'+value+' - '+note+'</div>');
        }
    }
}
function number_format(n,d)
{
    var number = String(n.toFixed(d).replace('.',','));
    return number.replace(/./g, function(c, i, a) {
                return i > 0 && c !== "," && (a.length - i) % 3 === 0 ? "." + c : c;
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

function show_chart_onclick(chart_type)
{
   //div chart
   index = $(chart_type).index('#chart-type button');
   $('.div-chart').hide();
   selector_chart_show = '.div-chart:eq('+ index + ')';
   $(selector_chart_show).show();
   //button
   $('#chart-type button').attr('class','btn btn-info');
   
   $(chart_type).attr('class','btn btn-danger');
}
</script>
<!--pie chart-->
<div class="row-fluid" >
    <style>
        .row-fluid #pie-chart,.row-fluid .content-widgets{background: white}
        .row-fluid .span2 {
            width: 11.893617%;
        }
        .row-fluid
        {
            margin-top: 20px;
            /*overflow: hidden;*/
            width: 100%;
            height: auto;
            background: white;
            border: white;
        }
        .widget-container
        {
            border:solid 1px #3498DB;
        }
        body
        {
            background: white;
        }
    </style>
    <!--chart-->
    <div class="span10" >
        <div style="width: 100%;" class="div-chart">
            <div class="content-widgets light-gray">
                <div class="widget-head blue">
                    <h3>Biểu đồ tiến độ xử lý hồ sơ</h3>
                </div>
                <div class="widget-container">
                    <div id="pie-chart" class="pie-chart" style="background: white">
                        <div id="pie-chartContainer" style="width: 100%;height:400px; text-align: left;">
                        </div>
                        <div id="pie_chart_info">
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear" style="10px">&nbsp;</div>
            <div style="width: 100%;text-align: right">
                <strong>Năm:</strong>&nbsp;&nbsp;
                <select onchange="sel_year_onclick(this);">
                <?php
                    $min_year = $arr_year['C_MIN_YEAR'];
                    $max_year = $arr_year['C_MAX_YEAR'];
                    $year = $min_year;

                    $v_loop = $max_year - $min_year;
                    for($i = 0; $i <= $v_loop;$i++):
                        $year = $year + $i;
                ?>
                    <option <?php echo ($year == DATE('Y'))?'selected':'';?> value="<?php echo $year?>" data-function="create_progress_pie_chart">
                        <?php echo $year?>
                    </option>
                <?php endfor;?>
                </select>
            </div>
        </div>
        <!--bar chart-->
        <div class="clear"></div>
        <div style="width: 100%;display:none" class="div-chart">
            <div class="content-widgets">
                <div>
                    <div class="widget-header-block">
                        <h4 class="widget-header">Biểu đồ Tiếp nhận - Đã trả</h4>
                    </div>
                    <div>
                        <div id=
                             "combine-chart">
                            <div id="legendcontainer26" class="legend-block">
                            </div>

                            <div id="combine-chartContainer" style=
                                 "width: 100%;height:300px; text-align: center; margin:0 auto;">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="clear" style="10px">&nbsp;</div>
                <div style="width: 100%;text-align: right">
                    <strong>Năm:</strong>&nbsp;&nbsp;
                    <select onchange="sel_year_onclick(this);">
                    <?php
                        $min_year = $arr_year['C_MIN_YEAR'];
                        $max_year = $arr_year['C_MAX_YEAR'];
                        $year = $min_year;

                        $v_loop = $max_year - $min_year;
                        for($i = 0; $i <= $v_loop;$i++):
                            $year = $year + $i;
                    ?>
                        <option <?php echo ($year == DATE('Y'))?'selected':'';?> value="<?php echo $year?>" data-function="create_recevice_respond_bar_chart">
                            <?php echo $year?>
                        </option>
                    <?php endfor;?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <!--menu chart-->
    <div class="span2" id="chart-type">
        <!--button pie chart-->
        <button class="btn" type="button" onclick="show_chart_onclick(this)">
            <i class="icon-bar-chart"></i>
            Tiến độ xử lý hồ sơ
        </button>
        <div class="clear" style="height: 5px;"></div>
        <!--button bar chart-->
        <button class="btn" type="button" onclick="show_chart_onclick(this)">
            <i class="icon-bar-chart"></i>
            Tiếp nhận-Trả kết quả
        </button>
    </div>
</div>
<?php $this->template->display('dsp_footer_pop_win.php');