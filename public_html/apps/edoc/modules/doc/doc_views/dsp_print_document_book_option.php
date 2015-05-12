<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}
//display header
$this->template->title = 'In So';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_direction    = $_REQUEST['direction'];
$v_type         = $_REQUEST['type'];
?>
<label>Từ ngày: &nbsp;</label>
<input type="text" id="txt_begin_date" readonly="readonly"/>
<br/>
<label>Đến ngày:</label>
<input type="text" id="txt_end_date" readonly="readonly"/>
<br/>
<input type="button" id="btn_print" value="In sổ văn bản" class="button" onclick="btn_print_onclick()"/>
<script>
    $('#txt_begin_date').datepicker({
        changeYear: true,
        changeMonth: true,
        dateFormat: 'dd/mm/yy'
    });
    $('#txt_end_date').datepicker({
        changeYear: true,
        changeMonth: true,
        dateFormat: 'dd/mm/yy'
    });
    function btn_print_onclick()
    {

        if ($('#txt_begin_date').val() == '')
        {
            alert('Xin chọn ngày bắt đầu!');
            $('#txt_begin_date').focus();
            return false;
        }
        if ($('#txt_end_date').val() == '')
        {
            alert('Xin chọn ngày kết thúc!');
            $('#txt_end_date').focus();
            return false;
        }
        
        //
        b = Date.parse($('#txt_begin_date').val());
        e = Date.parse($('#txt_end_date').val());


        window.parent.hidePopWin();
        var url =  '<?php echo $this->get_controller_url() . strtolower($v_direction);?>/dsp_print_document_book/?pop_win=1&direction=<?php echo $v_direction;?>&type=<?php echo $v_type;?>';
        url += '&begin_date=' + $('#txt_begin_date').val();
        url += '&end_date=' + $('#txt_end_date').val();
        window.parent.showPopWin(url ,900, 600, null);
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');