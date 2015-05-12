<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

$v_event_id                     = $VIEW_DATA['event_id'];
$date                           = $VIEW_DATA['date'];
$arr_single_event               = $VIEW_DATA['arr_single_event'];
$arr_all_event_owner            = $VIEW_DATA['arr_all_event_owner'];
$arr_all_event_attender         = $VIEW_DATA['arr_all_event_attender'];
$noon                           = $VIEW_DATA['noon'];

if ($v_event_id > 0)
{
    $v_event_id             = $arr_single_event['event_id'];
    $v_subject              = $arr_single_event['subject'];
    $v_description          = $arr_single_event['description'];
    $v_creator_user_code    = $arr_single_event['creator_user_code'];
    $v_location             = $arr_single_event['location'];

    $v_begin_date           = $arr_single_event['begin_date'];
    $v_begin_hour           = $arr_single_event['begin_hour'];
    $v_begin_minute         = $arr_single_event['begin_minute'];

    $v_end_date             = $arr_single_event['end_date'];
    $v_end_hour             = $arr_single_event['end_hour'];
    $v_end_minute           = $arr_single_event['begin_minute'];
}
else
{
    $v_event_id             = 0;
    $v_subject              = '';
    $v_description          = '';
    //$v_begin_time           = date('d/m/Y');
    //$v_end_time             = date('d/m/Y');
    $v_creator_user_code      = Session::get('user_code');

    $v_location             = '';


    $v_begin_date           = jwDate::yyyymmdd_to_ddmmyyyy($date);
    $v_begin_hour           = date('H');
    $v_begin_minute         = 0;

    $v_end_date             = jwDate::yyyymmdd_to_ddmmyyyy($date);
    $v_end_hour             = date('H');
    $v_end_minute           = 0;
}

//Kiem tra quyen phan cong cong viec
//1. LA Lanh dao don vi
//$v_is_bod = in_array(_CONST_BOD_GROUP_CODE, Session::get('arr_group_code'));
//$v_is_leader = in_array(_CONST_TEAM_LEADER_GROUP_CODE, Session::get('arr_group_code'));

$v_is_bod = TRUE;
$v_is_leader = TRUE;
$v_is_staff = TRUE;
//$v_is_staff  = !$v_is_leader && !$v_is_bod;

//Header
$this->template->title = 'Cập nhật lịch làm việc';
$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
?>
<form name="frmMain" id="frmMain" action="#"
	method="POST">
	<?php
	echo $this->hidden('controller', $this->get_controller_url());

	echo $this->hidden('hdn_dsp_single_method', 'dsp_single_event');
	echo $this->hidden('hdn_dsp_all_method', 'dsp_all_event');
	echo $this->hidden('hdn_update_method', 'update_event');
	echo $this->hidden('hdn_delete_method', 'delete_event');

	echo $this->hidden('hdn_item_id', $v_event_id);
	echo $this->hidden('hdn_creator_user_code', $v_creator_user_code);

	echo $this->hidden('hdn_owner_user_code_list', '');
	echo $this->hidden('hdn_owner_group_code_list', '');
	echo $this->hidden('hdn_attender_user_code_list', '');
	echo $this->hidden('hdn_attender_group_code_list', '');

	echo $this->hidden('XmlData', '');

	?>
	<table width="100%" cellpading="0" cellspacing="0" border="0" class="adminform none-border-table">
		<col width="20%">
		<col width="80%">
		<tr>
			<td>Tiêu đề <?php echo $this->req();?>
			</td>
			<td><input type="text" name="txt_subject" class="inputbox"
				id="txt_subject" value="<?php echo $v_subject;?>" style="width: 90%"
				data-allownull="no" data-validate="text" data-name="Tiêu đề"
				data-xml="no" data-doc="no" autofocus="autofocus" />
			</td>
		</tr>
		<tr>
			<td>Nội dung</td>
			<td><textarea name="txt_description" id="txt_description"
					style="width: 90%" rows="2" data-allownull="yes"
					data-validate="text" data-name="Nội dung" data-xml="no"
					data-doc="no">
					<?php echo $v_description;?>
				</textarea>
			</td>
		</tr>
		<tr>
			<td>Thời gian</td>
			<td>
				<table width="100%" cellpadding="0" cellspacing="0" class="none-border-table">
					<tr>
						<td width="10%">Bắt đầu từ:</td>
						<td><input type="text" name="txt_begin_date" id="txt_begin_date"
							value="<?php echo $v_begin_date;?>" readonly="readonly" size="10"
							data-allownull="no" data-validate="text" data-name="Ngày bắt đầu"
							data-xml="no" data-doc="no" /> <?php if ($v_event_id > 0): ?> <img
							class="btndate"
							src="<?php echo SITE_ROOT;?>public/images/calendar.gif"
							onclick="DoCal('txt_begin_date')" /> <?php endif; ?> <select
							name="sel_begin_hour">
								<?php if ($v_event_id < 1):?>
								<?php if ($noon == 'am'): ?>
								<?php for ($i=0; $i<=11; $i++): ?>
								<option value="<?php echo $i;?>"
								<?php echo ($i == intval($v_begin_hour)) ? ' selected' : '';?>>
									<?php echo ($i <10) ? ('0' . $i) : $i;?>
								</option>
								<?php endfor; ?>
								<?php else: ?>
								<?php for ($i=12; $i<=23; $i++): ?>
								<option value="<?php echo $i;?>"
								<?php echo ($i == intval($v_begin_hour)) ? ' selected' : '';?>>
									<?php echo $i;?>
								</option>
								<?php endfor; ?>
								<?php endif; ?>
								<?php else: ?>
								<?php for ($i=0; $i<=23; $i++): ?>
								<option value="<?php echo $i;?>"
								<?php echo ($i == intval($v_begin_hour)) ? ' selected' : '';?>>
									<?php echo ($i <10) ? ('0' . $i) : $i;?>
								</option>
								<?php endfor; ?>
								<?php endif;?>
						</select>: <select name="sel_begin_minute">
								<?php for ($i=0; $i<=59; $i+=15): ?>
								<option value="<?php echo $i;?>"
								<?php echo ($i == intval($v_begin_minute)) ? ' selected' : '';?>>
									<?php echo ($i <10) ? ('0' . $i) : $i;?>
								</option>
								<?php endfor; ?>
						</select>
						</td>
					</tr>
					<tr>
						<td>Đến:</td>
						<td><input type="text" name="txt_end_date" id="txt_end_date"
							value="<?php echo $v_end_date;?>" readonly="readonly" size="10"
							data-allownull="no" data-validate="text"
							data-name="Ngày kết thúc" data-xml="no" data-doc="no" /> <?php if ($v_event_id > 0): ?>
							<img class="btndate"
							src="<?php echo SITE_ROOT;?>public/images/calendar.gif"
							onclick="DoCal('txt_end_date')" /> <?php endif;?> <select
							name="sel_end_hour">
								<?php for ($i=0; $i<=23; $i++): ?>
								<option value="<?php echo $i;?>"
								<?php echo ($i == intval($v_end_hour)) ? ' selected' : '';?>>
									<?php echo ($i <10) ? ('0' . $i) : $i;?>
								</option>
								<?php endfor; ?>
						</select>: <select name="sel_end_minute">
								<?php for ($i=0; $i<=59; $i+=15): ?>
								<option value="<?php echo $i;?>"
								<?php echo ($i == intval($v_end_minute)) ? ' selected' : '';?>>
									<?php echo ($i <10) ? ('0' . $i) : $i;?>
								</option>
								<?php endfor; ?>
						</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>Địa điểm <?php echo $this->req();?>
			</td>
			<td><input type="text" name="txt_location" id="txt_location"
				value="<?php echo $v_location;?>" style="width: 90%"
				data-allownull="no" data-validate="text" data-name="Địa điểm"
				data-xml="no" data-doc="no" />
			</td>
		</tr>
		<tr>
			<td>Thường trực <?php echo $this->req();?>
			</td>
			<td>
				<div>
					<div id="event_owner_list" class="edit-box list-user-box">
						<table width="100%" class="adminlist" cellspacing="0" border="1"
							id="tbl_owner_user">
							<colgroup>
								<col width="5%" />
								<col width="45%" />
								<col width="35%" />
								<col width="15%" />
							</colgroup>
							<tr>
								<th>#</th>
								<th>Tên</th>
								<th>Chức danh</th>
								<th>Loại</th>
							</tr>
							<tr>
								<th colspan="4">&nbsp;</th>
							</tr>
							<?php for ($i=0; $i < sizeof($arr_all_event_owner); $i++):
							$v_user_code    = $arr_all_event_owner[$i]['C_USER_CODE'];
							$v_user_name    = $arr_all_event_owner[$i]['C_USER_NAME'];
							$v_user_type    = $arr_all_event_owner[$i]['C_USER_TYPE'];
							$v_job_title    = $arr_all_event_owner[$i]['C_JOB_TITLE'];

							?>
							<tr id="tr_user_<?php echo $v_user_code;?>"
								class="row<?php echo ($i%2);?>">
								<td class="center"><input type="checkbox" name="chk_user"
									value="<?php echo $v_user_code;?>"
									id="chk_user_<?php echo $v_user_code;?>"
									data-user_type="<?php echo $v_user_type;?>" />
								</td>
								<td><label for="chk_user_<?php echo $v_user_code;?>"> <img
										src="<?php echo SITE_ROOT;?>public/images/icon-16-<?php echo $v_user_type;?>.png"
										border="0" align="absmiddle" /> <?php echo $v_user_name;?>
								</label>
								</td>
								<td><?php echo $v_job_title;?></td>
								<td><?php echo ($v_user_type == 'user') ? 'NSD' : 'Nhóm';?></td>
							</tr>
							<?php endfor; ?>

						</table>
					</div>
					<div id="event_owner_action" class="list-user-action">
						<?php if ( ($v_creator_user_code == Session::get('user_code')) OR ($this->check_permission('LAP_LICH_CHO_DON_VI'))):?>
						<?php // if ($v_is_bod OR $v_is_leader): ?>
						<input type="button" name="btn_add_user" value="Thêm NSD"
							class="button add_user"
							onclick="dsp_all_user_and_group_to_add_owner()" /><br /> <input
							type="button" name="btn_remove_user" value="Bỏ NSD"
							class="button remove_user"
							onclick="remove_user('tbl_owner_user')" />
						<?php endif; ?>
						<?php // endif; ?>
					</div>
				</div>
				<div class="clear">&nbsp;</div>
			</td>
		</tr>

		<tr>
			<td>Tham dự:</td>
			<td>
				<div>
					<div id="event_attender_list" class="edit-box list-user-box">
						<table width="100%" class="adminlist" cellspacing="0" border="1"
							id="tbl_attender_user">
							<colgroup>
								<col width="5%" />
								<col width="45%" />
								<col width="35%" />
								<col width="15%" />
							</colgroup>
							<tr>
								<th>#</th>
								<th>Tên</th>
								<th>Chức danh</th>
								<th>Loại</th>
							</tr>
							<tr>
								<th colspan="4">&nbsp;</th>
							</tr>
							<?php for ($i=0; $i < sizeof($arr_all_event_attender); $i++):
							$v_user_code    = $arr_all_event_attender[$i]['C_USER_CODE'];
							$v_user_name    = $arr_all_event_attender[$i]['C_USER_NAME'];
							$v_user_type    = $arr_all_event_attender[$i]['C_USER_TYPE'];
							$v_job_title    = $arr_all_event_attender[$i]['C_JOB_TITLE'];

							?>
							<tr id="tr_user_<?php echo $v_user_code;?>"
								class="row<?php echo ($i%2);?>">
								<td class="center"><input type="checkbox" name="chk_user"
									value="<?php echo $v_user_code;?>"
									id="chk_user_<?php echo $v_user_code;?>"
									data-user_type="<?php echo $v_user_type;?>" />
								</td>
								<td><label for="chk_user_<?php echo $v_user_code;?>"> <img
										src="<?php echo SITE_ROOT;?>public/images/icon-16-<?php echo $v_user_type;?>.png"
										border="0" align="absmiddle" /> <?php echo $v_user_name;?>
								</label>
								</td>
								<td><?php echo $v_job_title;?></td>
								<td><?php echo ($v_user_type == 'user') ? 'NSD' : 'Nhóm';?></td>
							</tr>
							<?php endfor; ?>
						</table>
					</div>
					<div id="event_attender_action" class="list-user-action">
						<?php if ( ($v_creator_user_code == Session::get('user_code')) OR ($this->check_permission('LAP_LICH_CHO_DON_VI'))):?>
						<?php //if ($v_is_bod OR $v_is_leader): ?>
						<input type="button" name="btn_add_user" value="Thêm NSD"
							class="button add_user"
							onclick="dsp_all_user_and_group_to_add_attender()" /><br /> <input
							type="button" name="btn_remove_user" value="Bỏ NSD"
							class="button remove_user"
							onclick="remove_user('tbl_attender_user')" />
						<?php endif; ?>
						<?php // endif; ?>
					</div>
				</div>
				<div class="clear">&nbsp;</div>
			</td>
		</tr>

	</table>
	<!-- Button -->
	<div class="button-area">
		<?php if ( ($v_creator_user_code == Session::get('user_code')) OR ($this->check_permission('LAP_LICH_CHO_DON_VI'))):?>
		<input type="button" name="update" class="button save"
			value="<?php echo _LANG_UPDATE_BUTTON; ?>"
			onclick="btn_update_event_onclick()" />
		<?php endif; ?>

		<?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
		<input type="button" name="cancel" class="button close"
			value="<?php echo _LANG_CLOSE_WINDOW_BUTTON; ?>"
			onclick="<?php echo $v_back_action;?>" />

		<?php if ( ($v_event_id > 0) && ($v_creator_user_code==Session::get('user_code')) ):?>
		<input type="button" name="btn_delete_event" class="button delete"
			value="<?php echo _LANG_DELETE_BUTTON;?>"
			onclick="btn_delete_event_onclick()" />
		<?php endif; ?>
	</div>
</form>
<script>
    function build_user_list(json, tbl)
    {
        for (i=0; i<json.length; i++)
        {
            v_user_code = json[i].user_code;
            v_user_name = json[i].user_name;
            v_user_type = 'user';
            v_job_title = json[i].job_title;

            v_user_type_text = (v_user_type == 'user') ? 'NSD' : 'Nhóm';
            //Neu user chua co trong group thi them vao
            q = '#chk_user_' + v_user_code;
            if( $(q).length < 1 )
            {
                html = '<tr id="tr_user_' + v_user_code + '">';
                html += '<td class="center">';
                html +=     '<input type="checkbox" name="chk_user" value="' + v_user_code + '" id="chk_user_' + v_user_code + '"'
                            + ' data-user_type="' + v_user_type + '" />';
                html += '</td>';
                html += '<td>';
                html += '<img src="' + SITE_ROOT + 'public/images/icon-16-user.png" border="0" align="absmiddle" />';
                html += '<label for="chk_user_' + v_user_code + '">' + v_user_name + '</label>';
                html += '</td>';
                html += '<td>' + v_job_title + '</td>';
                html += '<td>' + v_user_type_text + '</td>';
                html += '</tr>';

                s = '#' + tbl;
                $(s).append(html);
            }
        }
    }
    function remove_user(tbl)
    {
        var q = "#" + tbl + " input[name='chk_user']";
        $(q).each(function(index) {
            if ($(this).is(':checked'))
            {
                v_user_id = $(this).val();
                s = '#tr_user_' + v_user_id;
                $(s).remove();
            }
        });
    }

    function dsp_all_user_and_group_to_add_owner()
    {
        <?php if ($v_is_bod): ?>
            var url = SITE_ROOT + 'cores/user/dsp_all_user_to_add/&pop_win=1&group=LANH_DAO_DON_VI&d=user';
        <?php elseif ($v_is_leader): ?>
            var url = SITE_ROOT + 'cores/user/dsp_all_user_to_add/&pop_win=1&my_dept_only=1';
        <?php else: ?>
            var url = 'about:blank';
        <?php endif;?>

        var url = SITE_ROOT + 'cores/user/dsp_all_user_to_add/&pop_win=1&d=user';

        showPopWin(url, 450, 350, do_add_owner);
    }
    function do_add_owner(returnVal)
    {
        build_user_list(returnVal, 'tbl_owner_user');
    }

    function do_add_attender(returnVal)
    {
        build_user_list(returnVal, 'tbl_attender_user');
    }

    function dsp_all_user_and_group_to_add_attender()
    {
        <?php if ($v_is_bod): ?>
            var url = SITE_ROOT + 'cores/user/dsp_all_user_and_group_to_add/&pop_win=1';
        <?php elseif ($v_is_leader): ?>
            var url = SITE_ROOT + 'cores/user/dsp_all_user_and_group_to_add/&pop_win=1&my_dept_only=1';
        <?php else: ?>
            var url = 'about:blank';
        <?php endif;?>

        var url = SITE_ROOT + 'cores/user/dsp_all_user_to_add/&pop_win=1&d=user';

        showPopWin(url, 450, 350, do_add_attender);
    }

    function btn_delete_event_onclick()
    {
        if (confirm('Bạn chắc chắn xoá ?'))
        {
            var f = document.frmMain;
            m = $("#controller").val() + f.hdn_delete_method.value;
            $("#frmMain").attr("action", m);
            f.submit();
        }
    }

    function btn_update_event_onclick()
    {
        //Lay danh sach NGUOI chu tri
        arr_owner_user_code = new Array();
        //Lay danh sach NHOM chu tri
        arr_owner_group_code = new Array();

        q = "#tbl_owner_user input[name='chk_user']";
        $(q).each(function(index) {
            if ($(this).attr('data-user_type') == 'user')
            {
                arr_owner_user_code.push($(this).val());
            }
            else if ($(this).attr('data-user_type') == 'group')
            {
                arr_owner_group_code.push($(this).val());
            }
        });

        /*
        if ( (arr_owner_user_code.length == 0) && (arr_owner_group_code == 0) )
        {
            alert('Bạn chưa chọn Người/Nhóm thường trực!');
            return;
        }
        */

        //Lay danh sach NGUOI chu tri
        arr_attender_user_code = new Array();
        //Lay danh sach NHOM chu tri
        arr_attender_group_code = new Array();

        q = "#tbl_attender_user input[name='chk_user']";
        $(q).each(function(index) {
            if ($(this).attr('data-user_type') == 'user')
            {
                arr_attender_user_code.push($(this).val());
            }
            else if ($(this).attr('data-user_type') == 'group')
            {
                arr_attender_group_code.push($(this).val());
            }
        });

        $("#hdn_owner_user_code_list").val(arr_owner_user_code.join());
        $("#hdn_owner_group_code_list").val(arr_owner_group_code.join());
        $("#hdn_attender_user_code_list").val(arr_attender_user_code.join());
        $("#hdn_attender_group_code_list").val(arr_attender_group_code.join());


        btn_update_onclick();
    }

</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');