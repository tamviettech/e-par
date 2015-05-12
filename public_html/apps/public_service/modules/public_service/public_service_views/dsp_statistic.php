<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>

<script src="<?php echo FULL_SITE_ROOT; ?>/public/themes/bootstrap/js/jquery.flot.js"></script>
<script src="<?php echo FULL_SITE_ROOT; ?>public/themes/bootstrap/js/jquery.flot.pie.js"></script>
<script src="https://www.google.com/jsapi"></script>
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
            url: "<?php echo $this->get_controller_url() . 'arp_get_record_process/'; ?>" + year,
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
            ['',        'Tiếp nhận',                                  'Đã trả'],
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
    
    function show_chart_onclick(chart_type)
    {   
        start = false;
        var type = $(chart_type).attr('data');
        var year = $('#year').val();
        var count_pie = $('.div-chart').filter('#chartContainer-pie').length;
       
        if(type == 'pie')
        {
            if(count_pie == 0)
            {   
                 $('#pie-chart h3').html('Biểu đồ sử lý tiến độ hồ sơ');
                $('#year option').attr('data-function','create_process_pie_chart');
                $('.div-chart').attr('id','chartContainer-pie');
                create_process_pie_chart(year);
            }
        }
        if(type == 'bar')
        {
            if(count_pie > 0)
            {
                $('#pie-chart h3').html('Biểu đồ thông tin tiếp nhận - trả hồ sơ');
                $('#year option').attr('data-function','create_process_bar_chart');
                $('.div-chart').attr('id','chartContainer-bar');
                create_process_bar_chart(year);
            }
        }
    }
</script>

<div class="clear" ></div>
<div  class="group-option" id="page-all-statistics">
    <div class="tab-widget">
        <ul class="nav nav-tabs" id="myTab1">
            <li class="active">
                <a href="#tab-chart">
                    <i class="icon-bar-chart"></i>&nbsp;Biểu đồ tiến độ xử lý hồ sơ</a></li>
            <li><a href="#tab-liveboard"><i class=""></i>Bảng theo dõi trực tuyến</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab-chart">

                <div class="span12" >
                    <div id="pie-chart" class="span12">
                        <div class="content-widgets light-gray">
                            <div class="widget-head blue">
                                <h3>Biểu đồ sử lý tiến độ hồ sơ</h3>
                            </div>
                            <!--End .widget-head blue-->
                            <div id="box-chart" class="widget-container" >
                                <div class="span10" style="overflow: hidden"> 
                                    <div id="chartContainer-pie" data="chart" class="case-container div-chart" style="width: 100%; height: 400px; "></div>
                                    <div id="chartContainer-bar" data="chart" class="case-container" style="width: 100%; height: 400px;display: none "></div>
                                    <div style="width: 100%;text-align: right">
                                        <strong>Năm:</strong>&nbsp;&nbsp;
                                        <select id="year" name="year" onchange="sel_year_onclick(this);">
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
                                <!--End #box-chart-->
                                <div class="span2" id="chart-type">
                                    <!--button pie chart-->
                                    <button class="btn" type="button"  data="pie" onclick="show_chart_onclick(this)">
                                        <i class="icon-bar-chart"></i>
                                        Tiến độ xử lý hồ sơ
                                    </button>
                                    <div class="clear" style="height: 5px;"></div>
                                    <!--button bar chart-->
                                    <button class="btn" type="button" data="bar"  onclick="show_chart_onclick(this)">
                                        <i class="icon-bar-chart"></i>
                                        Tiếp nhận-Trả kết quả
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>            
                </div>
                <!--End .span12-->
            </div>
            <!--End #tab-chart-->
            <div class="tab-pane" id="tab-liveboard">
                <iframe src="<?php echo FULL_SITE_ROOT?>r3/liveboard/" style="width:100%;height:500px;min-height: 500px" ></iframe>
            </div>
            <!--End tab-liveboard-->
        </div>
    </div>

</div>

