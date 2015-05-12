<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

$day = $VIEW_DATA['day'];
$month = $VIEW_DATA['month'];
$year = $VIEW_DATA['year'];

$arr_date_of_week = $VIEW_DATA['arr_date_of_week'];

//header
$this->template->title = 'Lịch công tác';
$this->template->display('dsp_header.php');


$v_date_col_width = "80";
$v_noon_col_width = "70";
$v_subject_col_width = "406";
$v_owner_col_width = "150";
$v_unit_col_width = "150";
$v_location_col_width = "150";

$v_begin_of_next_week = jwDate::beginOfNextWeek($day, $month, $year);
$v_begin_of_prev_week = jwDate::beginOfPrevWeek($day, $month, $year);

$v_next_week_link = $this->get_controller_url() . '&day=' . jwDate::cutDay($v_begin_of_next_week)
                        . '&month=' . jwDate::cutMonth($v_begin_of_next_week)
                        . '&year=' . jwDate::cutYear($v_begin_of_next_week);

$v_prev_week_link = $this->get_controller_url() . '&day=' . jwDate::cutDay($v_begin_of_prev_week)
                        . '&month=' . jwDate::cutMonth($v_begin_of_prev_week)
                        . '&year=' . jwDate::cutYear($v_begin_of_prev_week);
?>
<div class="page-title">
    Lịch làm việc<br/>
    Tuần từ <?php echo $arr_date_of_week[0]['C_DATE_DDMMYYYY'];?> đến <?php echo $arr_date_of_week[6]['C_DATE_DDMMYYYY'];?>
</div>
<form name="frmMain" id="frmMain" action="#" method="POST">
    <table width="100%" border="0" cellpading="0" cellspacing="0" class="week-navigator none-border-table">
        <tr>
            <td class="left middle bold">
                <a href="<?php echo $v_prev_week_link;?>"><< Tuần trước</a>
            </td>
            <td class="center middle bold">
                <a href="<?php echo $this->get_controller_url();?>">Tuần hiện tại</a>
            </td>
            <td class="right middle bold">
                <a href="<?php echo $v_next_week_link;?>">Tuần kế tiếp >></a>
            </td>
        </tr>
    </table>
    <table width="100%" class="tbl_week_view" cellpading="0" cellspacing="0" border="1">
        <tr>
            <th rowspan="2" width="<?php echo $v_date_col_width;?>">Ngày</th>
            <th rowspan="2" width="<?php echo $v_noon_col_width;?>">Buổi</th>
            <th rowspan="2" width="<?php echo $v_subject_col_width - 16;?>">Nội dung họp, làm việc</th>
            <th colspan="2">Người chủ trì, chỉ đạo hoặc dự</th>
            <th rowspan="2" width="<?php echo $v_location_col_width;?>">Địa điểm</th>
        </tr>
        <tr>
            <th width="<?php echo $v_owner_col_width;?>">Lãnh đạo, chủ trì</th>
            <th width="<?php echo $v_unit_col_width;?>">Tham dự</th>
        </tr>
        <script>var arr_date_of_week = new Array();</script>
        <?php for ($i=0; $i<=6; $i++):
            $v_date_ddmmyyyy    = $arr_date_of_week[$i]['C_DATE_DDMMYYYY'];
            $v_date_yyyymmdd    = $arr_date_of_week[$i]['C_DATE_YYYYMMDD'];
            $v_date_off         = $arr_date_of_week[$i]['C_OFF'];

            $v_date_ddmmyyyy = str_replace('/', '-', $v_date_ddmmyyyy);
            $v_date_yyyymmdd = str_replace('/', '-', $v_date_yyyymmdd);

            $v_day_of_week = jwDate::dayOfWeek(jwDate::cutDay($v_date_ddmmyyyy), jwDate::cutMonth($v_date_ddmmyyyy), jwDate::cutYear($v_date_ddmmyyyy));

            $v_tr_class = ($v_date_off == 1) ? 'class="off"' : '';
            ?>
            <script>arr_date_of_week.push('<?php echo $v_date_yyyymmdd;?>')</script>
            <tr <?php echo $v_tr_class;?>>
                <td rowspan="2" class="center middle">
                    <?php echo $v_date_ddmmyyyy;?><br/>
                    <?php echo jwDate::vn_day_of_week($v_day_of_week);?><br/>
                </td>
                <td class="center middle">
                    <label>Sáng</label>&nbsp;
                    <input type="button" value="+" onclick="dsp_add_event('<?php echo $v_date_yyyymmdd;?>','am')"/>
                </td>
                <td colspan="4">
                    <table id="am_<?php echo $v_date_yyyymmdd;?>" class="adminlist event"></table>
                </td>
            </tr>
            <tr <?php echo $v_tr_class;?>>
                <td class="center middle">
                    <label>Chiều</label>&nbsp;
                    <input type="button" value="+" onclick="dsp_add_event('<?php echo $v_date_yyyymmdd;?>','pm')"/>
                </td>
                <td colspan="4">
                        <table id="pm_<?php echo $v_date_yyyymmdd;?>" class="adminlist event"></table>
                </td>
            </tr>
        <?php endfor; ?>
    </table>
</form>
<div id="text"></div>
<script>
    $(document).ready(function() {
        for (i=0; i < arr_date_of_week.length; i++)
        {
            reload_event(Array('am', arr_date_of_week[i]));
            reload_event(Array('pm', arr_date_of_week[i]));
        }
    });
    function reload_event(returnVal)
    { 
        noon = returnVal[0];
        date_yyyymmdd = returnVal[1];

        old_noon = returnVal[2];
        old_date_yyyymmdd = returnVal[3];

      
       
        //ajax get all event of [date], which begintime by [noon]
        var v_url = SITE_ROOT + 'schedule/event/get_date_event_' + noon + '/' + date_yyyymmdd;
        var q = '#' + noon + '_' + date_yyyymmdd;
        var q_old_row = '#' + old_noon + '_' + old_date_yyyymmdd;
        $.ajax({url:v_url
            ,success:function(result){
                var json = jQuery.parseJSON(result);
                $(q_old_row + ' tr').remove();
                for (i=0; i<json.length; i++)
                {
                    v_event_id              = json[i].event_id;
                    v_subject               = json[i].subject;
                    v_description           = json[i].description;
                    v_location              = json[i].location;
                    v_create_user_id        = json[i].create_user_id;
                    v_owner_user_id         = json[i].owner_user_id;
                    v_owner_user_name       = json[i].owner_user_name;
                    v_owner_unit_name       = json[i].owner_unit_name;

                    v_begin_date            = json[i].begin_date;
                    v_begin_hour            = json[i].begin_hour;
                    v_begin_minute          = json[i].begin_minute;
                    v_begin_hour = parseInt(v_begin_hour) < 10 ? '0' + v_begin_hour : v_begin_hour;
                    v_begin_minute = parseInt(v_begin_minute) < 10 ? '0' + v_begin_minute : v_begin_minute;

                    v_end_date              = json[i].end_date;
                    v_end_hour              = json[i].end_hour;
                    v_end_minute            = json[i].end_minute;
                    v_end_hour = parseInt(v_end_hour) < 10 ? '0' + v_end_hour : v_end_hour;
                    v_end_minute = parseInt(v_end_minute) < 10 ? '0' + v_end_minute : v_end_minute;

                    v_subject = '<label class="schedule begin_time">' + v_begin_hour + ':' + v_begin_minute + '</label> - '
                                    + '<label class="schedule end_time">' + v_end_hour + ':' + v_end_minute + '</label>'
                                    + '<label class="schedule subject">' + v_subject + '</label>';
                    v_tooltip = '';
                    if (trim(v_description) != '')
                    {
                        v_tooltip = ' onMouseOver="return overlib(\'' + v_description + '\',BELOW, RIGHT);" onMouseOut="return nd();"';
                    }

                    //Thuong truc
                    arr_owner_user = new Array();
                    //Tham du
                    arr_attender_user = new Array();
                    xml = $.parseXML(json[i].event_user);
                    $(xml).find( "row" ).each(
                        function(){
                            if ($(this).attr('user_role') == 'owner')
                            {
                                arr_owner_user.push($(this).attr('user_name'));
                            }
                            else
                            {
                                arr_attender_user.push($(this).attr('user_name'));
                            }
                        }
                    );

                    html = '<tr id="event_' + v_event_id + '">';
                    html += '<td width="<?php echo $v_subject_col_width;?>"' + v_tooltip + '>'
                            + '<a href="javascript:void(0)" onclick="edit_event(' + v_event_id + ')" style="cursor:pointer">'
                            + v_subject + '</a>'
                            + '</td>';

                    html += '<td width="<?php echo $v_owner_col_width;?>">' + arr_owner_user.join('; ') + '</td>';
                    html += '<td width="<?php echo $v_unit_col_width;?>">' + arr_attender_user.join('; ') + '</td>';
                    html += '<td width="<?php echo $v_location_col_width;?>">' + v_location + '</td>';
                    html += '</tr>';
					$('#event_' + v_event_id).remove();
                    $(q).append(html);
                }
            }
        });
    }

    function dsp_add_event(date_yyyymmdd, noon)
    {
        var url =  '<?php echo $this->get_controller_url();?>/dsp_single_event/0/&hdn_item_id=0&pop_win=1&date=' + date_yyyymmdd + '&noon='+noon;
        showPopWin(url , 920,620, reload_event);
    }

    function edit_event(id)
    {
        var url =  '<?php echo $this->get_controller_url();?>/dsp_single_event/' + id
                    + '/&hdn_item_id=' + id + '&pop_win=1';
        showPopWin(url ,920,620, reload_event);
    }
</script>
<?php $this->template->display('dsp_footer.php');