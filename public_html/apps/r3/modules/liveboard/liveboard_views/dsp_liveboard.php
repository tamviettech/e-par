<?php 
/**
// File name   : 
// Version     : 1.0.0.1
// Begin       : 2012-12-01
// Last Update : 2010-12-25
// Author      : TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
//Copyright (C) 2012-2013  TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn

// E-PAR is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// E-PAR is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// See LICENSE.TXT file for more information.
*/

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//header
$this->template->title = 'Bảng theo dõi tình hình giải quyết hồ sơ thủ tục hành chính';
$this->template->display('dsp_header_pop_win.php');
?>
<script type="text/javascript" src="<?php echo SITE_ROOT?>public/js/wz_tooltip.js"></script>
<script type="text/javascript">
    function build_col_width(){
        var html=[];
        var arr_cw=new Array();
        var browser=navigator.appName;
        if (browser=="Microsoft Internet Explorer"){
            for (var i=0; i<=100; i++)
            {
                arr_cw[i] = i + '%';
            }
        } else {
            var v_screen_width = screen.width;
            for (var i=0; i<=100; i++)
            {
                arr_cw[i] = (Math.round(i * v_screen_width / 100)) + 'px';
            }
        }
    
        i=-1;
        html[++i]	= '<col width="' + arr_cw[3] + '"/>';
        html[++i]	= '<col width="' + arr_cw[9] + '"/>';
        html[++i]	= '<col width="' + arr_cw[9] + '"/>';
        html[++i]	= '<col width="' + arr_cw[9] + '"/>';
        html[++i]	= '<col width="' + arr_cw[9] + '"/>';
        html[++i]	= '<col width="' + arr_cw[9] + '"/>';
        html[++i]	= '<col width="' + arr_cw[9] + '"/>';
        html[++i]	= '<col width="' + arr_cw[9] + '"/>';
        html[++i]	= '<col width="' + arr_cw[9] + '"/>';
        html[++i]	= '<col width="' + arr_cw[10] + '"/>';
        html[++i]	= '<col width="' + arr_cw[9] + '"/>';
        html[++i]	= '<col width="' + arr_cw[7] + '"/>';
        
        return html.join('');
    }

    function build_header_text()
    {
    	//return '<tr><th rowspan="2">STT</th><th rowspan="2">Mã TTHC</th><th colspan="2">Tiếp nhận hồ sơ</th><th colspan="5">Tình hình giải quyết hồ sơ</th><th colspan="2">Trả kết quả</th></tr><tr><th>Mới tiếp nhận</th><th>Đã bàn giao</th><th>Đang thụ lý</th><th>Đúng tiến độ</th><th>Chậm tiến độ</th><th>Quá hạn</th><th>Phải bổ sung</th><th>Chờ trả</th><th>Đã trả</th></tr>';
    	v_html_header = '<tr><th rowspan="2">STT</th><th rowspan="2">Mã TTHC</th><th colspan="2">Tiếp nhận hồ sơ</th><th colspan="6">Tình hình giải quyết hồ sơ</th><th colspan="2">Trả kết quả</th></tr><tr><th>Mới tiếp nhận</th><th>Đã bàn giao</th><th>Đang thụ lý</th><th>Đúng tiến độ</th><th>Chậm tiến độ</th><th>Quá hạn</th><th>Phải bổ sung</th><th>Đang tạm dừng<br/>(Chờ bổ sung/thuế)</th><th>Chờ trả</th><th>Đã trả</th></tr>';

    	return v_html_header;
    }
</script>
<div id="liveboard">
	<div class="center">
	    <div id="quoc-huy">
	        <img src="<?php echo SITE_ROOT;?>public/images/12033_quoc_huy.png" />
	    </div>
		<label id="unit_full_name"><?php echo get_xml_value(simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml'), '//full_name')?>	</label>
        <h5>
            Bảng theo dõi tình hình giải quyết hồ sơ thủ tục hành chính ngày <?php echo date('d-m-Y');?>
        </h5>
	</div>
	<script type="text/javascript">
        document.write('<table width="100%" class="adminlist liveboard">');
        document.write(build_col_width());
        document.write(build_header_text());
        document.write('</table>');
    </script>
	<div id="detail_table">
	    <script type="text/javascript">
            document.write('<table width="100%" class="adminlist liveboard">');
            document.write(build_col_width());
            document.write('<tbody id="row_detail"></tbody>');
            document.write('</table>');
        </script>
	</div>
	<label><input type="checkbox" name="chk_scroll" id="chk_scroll" onclick="chk_scroll_onclick(this)" />Cuộn</label> (<u>Chú ý</u>: Di chuột vào mã thủ tục để xem tên thủ tục)
</div>
<style>
    #container{padding:0px;margin:0px}
    body { overflow-x: hidden;}
</style>
<script>
    function update_liveboard()
    {
        var url = '<?php echo $this->get_controller_url();?>arp_liveboard';
        $.get(url, function(data) {
            $("#row_detail").html(data);
        });
    }
    update_liveboard();
    setInterval("update_liveboard()", 1000 * 60 * 30); //5mins


    //Scroll
	var scrollingbox=document.getElementById('detail_table'); 
	var sct=0, limit=0, step=1;
	scrollingbox.scrollTop = 0;
	var refreshIntervalId;
	
	function scroll(){
		if (limit > sct + (5 * step)) {
			sct=0;
			limit=0;
			scrollingbox.scrollTop = 0;
		} else {
			sct = scrollingbox.scrollTop;
			limit += step;
		}
		scrollingbox.scrollTop = sct + step;
	}

	function stop_scroll()
	{
		scrollingbox.scrollTop = 0;
		clearInterval(refreshIntervalId);
		scrollingbox.style.overflow = "";
	}
	
	function do_srcoll(){
		//reset
		sct=0; limit=0; step=1;
		
		scrollingbox.style.overflow = "hidden";
		scrollingbox.scrollTop = 0;
		scroll();
		refreshIntervalId = setInterval('scroll()',100);
	}

	function chk_scroll_onclick(obj)
	{
		if (obj.checked == true)
		{
		    do_srcoll();
		}
		else
		{
			stop_scroll();
		}
	}

	$(document).ready(function() {
		$("body").css("overflow-x", "hidden");
		$("body").css("background-color", "#FCFCFC");
		
		$("#chk_scroll").attr('checked','true');
		do_srcoll();
	});
	
</script>
<?php $this->template->display('dsp_footer_pop_win.php');?>