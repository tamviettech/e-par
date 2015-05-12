<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

$rindex = 0;
?>
<html>
    <head>
        <title>Lịch làm việc ngày <?php echo Date('d/m/Y')?></title>
        <style type="text/css">
            body
            {
                margin:0px;
                padding: 0px;
                font-family: Times;
                font-size: 22px;
            }
            #tbl-event
            {
                width:100%;
                border-collapse: collapse;
            }
            #tbl-event TH
            {
                font-weight: bold;
                text-align: center;
                font-size: 22px;
                padding-top:5px;
                padding-bottom:5px;
                background-color: #7FDCF8;
            }
            #tbl-event TD
            {
                font-size: 22px;
                padding: 15px 4px 15px 4px;
            }
            #tbl-event TR.row0
            {
                background-color: #FFFFFF;
            }
            #tbl-event TR.row1
            {
                background-color: #F7F7F7;
            }

            #date
            {
                padding-bottom: 5px;
                color: #FFF;
            }

            #header
            {
                background-image: url(<?php echo SITE_ROOT?>public/images/public-schedule-header-bg.jpg);
                background-repeat: repeat;
                width: 100%;
            }
        </style>
       <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        <script type="text/javascript">
        function get_data()
        {
        	$.get('<?php echo $this->get_controller_url()?>arp_today_event', function(data) {
    		  $('#content').html(data);
    		});
        }
        get_data();
        setInterval(get_data, 60000);
        </script>
    </head>
    <body>
        <div id="header">
            <img src="<?php echo SITE_ROOT;?>public/images/public-schedule-banner1.jpg" border="0" /> 

            <!--<div id="date">
                Hôm nay, <?php echo jwDate::vn_day_of_week();?>, ngày <?php echo Date('d');?> tháng <?php echo Date('m');?> năm <?php echo Date('Y');?>
            </div>-->
        </div>

        <div id="content"></div>
    </body>
</html>
<?php $this->template->display('dsp_footer_pop_win.php');