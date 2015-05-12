<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

//View data
$arr_all_record_type   = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code    = $VIEW_DATA['record_type_code'];
$arr_all_record        = $VIEW_DATA['arr_all_record'];
//$MY_TASK                = $VIEW_DATA['MY_TASK'];
//header
$this->template->title = 'Tra cứu hồ sơ';
$this->template->display('dsp_header.php');

$v_receive_date_from = get_post_var('txt_receive_date_from','');
$v_receive_date_to = get_post_var('txt_receive_date_to','');

$v_return_date_from = get_post_var('txt_return_date_from','');
$v_return_date_to = get_post_var('txt_return_date_from','');

$v_record_no = isset($_POST['txt_record_no']) ? replace_bad_char($_POST['txt_record_no']) : '';

$v_free_text = get_post_var('txt_free_text');

$sel_spec_selected = get_post_var('sel_spec','');

?>

<form name="frmMain" id="frmMain" action="" method="POST" style="margin: 0">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', '0');
    echo $this->hidden('hdn_item_id_list', '');

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');
    echo $this->hidden('hdn_undelete_method', 'undelete_record');
    echo $this->hidden('hdn_handover_method', 'do_handover_record');
    echo $this->hidden('hdn_rollback_method', 'dsp_rollback');
    echo $this->hidden('hdn_search_mode', get_post_var('hdn_search_mode', 1));
    echo $this->hidden('hdn_is_admin', intval(Session::get('is_admin')));

    echo $this->hidden('record_type_code', $v_record_type_code);
    ?>
    <!-- filter -->
    <div class="group" >
        <div class="widget-head blue">
            <h3>
                <a  class="widget-head" href="javascript:;" id="change_search" onclick="toggle_filter()" >
                    Tìm kiếm
                    &nbsp;<i class="icon-caret-down"></i>
                </a>
            </h3>
        </div>
        <?php
        $show_search = $sel_spec_selected || $v_record_type_code || $v_record_no || $v_free_text
                || $v_receive_date_from || $v_receive_date_to || $v_return_date_from || $v_return_date_to;
        $show_search = $show_search ? '' : 'display:none;';
        ?>
        <div class="widget-container" style="min-height: 90px;border: 1px solid #3498DB;<?php echo $show_search ?>padding: 10px;" id="div_filter" >
<!--            <div class="Row">
                <div class="left-Col">
                    Lĩnh vực
                </div>
                <div class="right_col">
                    <select name="sel_spec" id="sel_spec" style="width: 77%; color: #000000;">
                            <option value="">-- Tất cả lĩnh vực --</option>
                            <?php echo $this->generate_select_option($arr_all_spec, $sel_spec_selected); ?>
                    </select>
                </div>
            </div>-->
            <div class="Row">
                <div class="left-Col">
                    <label>Mã loại hồ sơ: (Alt+1) </label>
                </div>
                <div class="right-Col">
                    <input type="text" class="input-small"
                           name="txt_record_type_code" id="txt_record_type_code"
                           value="<?php echo $v_record_type_code; ?>"
                           class="inputbox upper_text" size="10" maxlength="10"
                           onkeypress="txt_record_type_code_onkeypress(event);"
                           autofocus="autofocus" accesskey="1" />&nbsp;

                    <select name="sel_record_type" id="sel_record_type"
                            style="width: 76%; color: #000000;"
                            onchange="sel_rt_onchange(this)">
                        <option value="">-- Chọn loại hồ sơ --</option>
                        <?php foreach ($arr_all_record_type as $code => $info): ?>
                            <?php $str_selected = ($code == strval($v_record_type_code)) ? ' selected' : ''; ?>
                            <option value="<?php echo $code; ?>" <?php echo $str_selected ?> data-mapping="<?php echo $info['C_MAPPING_CODE']?>" class="<?php echo $info['C_SPEC_CODE'];?>" data-scope="<?php echo $info['C_SCOPE']; ?>">
                                <?php 
                                $type_name = get_leftmost_words($info['C_NAME'], 25);
                                if(trim($type_name) != '' && isset($type_name))
                                {
                                    echo $type_name.'...';
                                }
                                else
                                {
                                    echo $info['C_NAME'];
                                }
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="Row">
                <div class="left-Col">
                    <label> Mã hồ sơ: </label>
                </div>
                <div class="right-Col">
                    <input type="text" style="width: 250px" name="txt_record_no"
                           id="txt_record_no" maxlength="100" onkeypress="txt_filter_onkeypress(this.form.btn_filter, event)"
                           value="<?php echo $v_record_no; ?>" />
                </div>
            </div>
            <div class="Row">
                <div class="left-Col">
                    <label>
                        Thông tin khác
                    </label>
                </div>
                <div class="right-Col">
                    <input class="text" id="txt_free_text" maxlength="100" name="txt_free_text" 
                           style="width:250px" type="text" value="<?php echo $v_free_text; ?>" 
                           onkeypress="txt_filter_onkeypress(this.form.btn_filter, event)"
                    />
                    &nbsp;
                    &nbsp;
                </div>
            </div>
            
            <div class="Row filter-mode filter-2">
                <div class="left-Col">
                    <label for="NgayNhapHoSo">
                        Thời gian tiếp nhận từ ngày:
                    </label>
                </div>
                <div class="right-Col">
                    <div class="left-Col2">
                        <div class="left-item-col" style="font-weight: normal">
                        </div>
                        <div class="right-item-col">
                            <input class="text" id="txt_receive_date_from" maxlength="100" name="txt_receive_date_from" style="width:70%" type="text" value="<?php echo $v_receive_date_from; ?>" />
                            <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT ?>public/images/calendar.gif" onclick="DoCal('txt_receive_date_from')" />
                        </div>
                    </div>
                    <div class="right-Col2">
                        <div class="left-item-col" style="font-weight: normal">
                            <b>Đến ngày:</b>
                        </div>
                        <div class="right-item-col">
                            <input class="text" id="txt_receive_date_to" maxlength="100" name="txt_receive_date_to" style="width:70%" type="text" value="<?php echo $v_receive_date_to; ?>" />
                            <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT ?>public/images/calendar.gif" onclick="DoCal('txt_receive_date_to')" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="Row filter-mode filter-2">
                <div class="left-Col">
                    <label>
                        Thời gian hẹn trả từ ngày:
                    </label>
                </div>
                <div class="right-Col">
                    <div class="left-Col2">
                        <div class="left-item-col" style="font-weight: normal">
                        </div>
                        <div class="right-item-col">
                            <input class="text" id="txt_return_date_from" maxlength="100" name="txt_return_date_from" style="width:70%" type="text" value="<?php echo $v_return_date_from; ?>" />
                            <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT ?>public/images/calendar.gif" onclick="DoCal('txt_return_date_from')" />
                        </div>
                    </div>
                    <div class="right-Col2">
                        <div class="left-item-col" style="font-weight: normal">
                            <b>Đến ngày:</b>
                        </div>
                        <div class="right-item-col">
                            <input class="text" id="txt_return_date_to" maxlength="100" name="txt_return_date_to" 
                                style="width:70%" type="text" value="<?php echo $v_return_date_to; ?>" 
                            />
                            <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT ?>public/images/calendar.gif" onclick="DoCal('txt_return_date_to')" />
                        </div>
                    </div>
                </div>
            </div>
            <div id="solid-button">
                <!--button search-->
                <button type="button" name="btn_trash" class="btn btn-primary" onclick="this.form.submit();" >
                    <i class="icon-search"></i>
                    Tìm kiếm
                </button>
                <button type="button" name="btn_reset" class="btn" onclick="btn_reset_search_form_onclick();" >
                    <i class="icon-refresh"></i>
                    Xoá điều kiện lọc
                </button>
            </div>
            <div class="clear" style="height: 5px;"></div>
        </div>
    </div>

    <div class="clear">&nbsp;</div>
    <div id="procedure">
        <?php
        if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'lookup')))
        {
            echo $this->render_form_display_all_record($arr_all_record, FALSE);
        }
        ?>
    </div>
    <div><?php echo $this->paging2($arr_all_record); ?></div>

    <!-- Context menu -->
    <ul id="myMenu" class="contextMenu">
        <li class="statistics"><a href="#statistics">Xem tiến độ</a></li>
        <?php if (Session::get('is_admin')): ?>
            <li class="rollback"><a href="#rollback">Trả hồ sơ về phòng ban/bộ phận trước</a></li>
            <li class="delete"><a href="#delete">Xoá/Khôi phục hồ sơ</a></li>
        <?php endif; ?>
    </ul>
</form>
<?php
$is_tra_cuu            = strtoupper($this->active_role) == _CONST_TRA_CUU_ROLE;
$is_tra_cuu_lien_thong = strtoupper($this->active_role) == _CONST_TRA_CUU_LIEN_THONG_ROLE;
$is_tra_cuu_tai_xa     = strtoupper($this->active_role) == _CONST_TRA_CUU_TAI_XA_ROLE;
?>

<script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.chained.mini.js" type="text/javascript"></script>
<script>
//    $("#sel_spec").change(function(){
//        var disabled = !$(this).val() ? true: false;
//        $('#sel_record_type').prop('disabled', disabled).trigger('chosen:updated');
//    }).trigger('change');
</script>
<script>
    $(document).ready(function() {
        //Show context on each row
        $(".adminlist tr[role='presentation']").contextMenu({
            menu: 'myMenu'
        }, function(action, el, pos) {
            v_record_id = $(el).attr('data-item_id');
            v_record_type = $(el).attr('data-item-type');
            v_deleted = $(el).attr('data-deleted');
            switch (action) {
                case 'statistics':
                    dsp_single_record_statistics(v_record_id);
                    break;
                case 'rollback':
                    dsp_rollback(v_record_id, v_record_type);
                    break;
                case 'delete':
                    if (v_deleted == '0' || v_deleted == 0) {
                        delete_record(v_record_id);
                    } else {
                        undelete_record(v_record_id);
                    }
                    break;
            }
        });

        //Quick action

        <?php if ($is_tra_cuu OR $is_tra_cuu_lien_thong OR $is_tra_cuu_tai_xa): ?>
            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id = $(this).attr('data-item_id');
                v_item_type = $(this).attr('data-item-type');
                v_deleted = $(this).attr('data-deleted');

                html = '';

                //Thong tin tien do
                html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\');" class="quick_action" title="Xem tiến độ">';
                html += '<i class="icon-bar-chart"></i></a>';
                if ($('#hdn_is_admin').val() != '0') {
                    html += '<a href="javascript:void(0)" onclick="dsp_rollback(\'' + v_item_id + '\', \'' + v_item_type + '\');" class="quick_action"  title="Trả hồ sơ về phòng ban/bộ phận trước">';
                    html += '<i class="icon-step-backward"></i></a>';
                    if (v_deleted == '0' || v_deleted == 0) {
                        html += '<a href="javascript:void(0)" onclick="delete_record(\'' + v_item_id + '\');" class="quick_action" title="Xoá hồ sơ" >';
                        html += '<i class="icon-trash"></i></a>';
                    } else {
                        html += '<a href="javascript:void(0)" onclick="undelete_record(\'' + v_item_id + '\');" class="quick_action" title="Khôi phục hồ sơ" >';
                        html += '<i class="icon-refresh"></i></a>';
                    }
                }


                $(this).html(html);
            });
        <?php endif; ?>
    });

    function dsp_rollback(record_id, record_type) {
        url = $('#controller').val() + $('#hdn_rollback_method').val()
                + '/' + record_id + '/' + QS + 'record_type_code=' + record_type;
        window.showPopWin(url, 800, 600, function() {
            $('#frmMain').submit();
        });
    }

    function delete_record(record_id) {
        if (!confirm('Bạn chắc chắn muốn xoá hồ sơ này?'))
            return;
        v_url = $('#controller').val() + $('#hdn_delete_method').val() + '/' + record_id;
        $.ajax({
            type: 'post',
            url: v_url,
            data: {hdn_item_id_list: record_id},
            success: function() {
                $('#frmMain').submit();
            }
        });
    }

    function undelete_record(record_id) {
        v_url = $('#controller').val() + $('#hdn_undelete_method').val() + '/' + record_id;
        $.ajax({
            type: 'post',
            url: v_url,
            data: {hdn_item_id_list: record_id},
            success: function() {
                $('#frmMain').submit();
            }
        });
    }

    function change_search() {
        old_mode = $('#hdn_search_mode').val();
        new_mode = old_mode == '1' ? 2 : 1;
        show_search_mode(new_mode);
    }

    function show_search_mode(new_mode) {
        //1=basic, 2=advance
        mode_info = {1: {label: 'Tìm kiếm cơ bản', title: 'Bấm vào đây để hiện tìm kiếm nâng cao'}
            , 2: {label: 'Tìm kiếm nâng cao', title: 'Bấm vào đây để hiện tìm kiếm cơ bản'}};
        old_mode = (new_mode == '1' ? 2 : 1);
        $('.filter-mode.filter-' + old_mode + '').hide().find('input').attr('disabled', 1);
        $('.filter-mode.filter-' + old_mode + '').find('select').attr('disabled', 1);
        $('.filter-mode.filter-' + new_mode).show().find('input').removeAttr('disabled');
        $('.filter-mode.filter-' + new_mode).find('select').removeAttr('disabled');
        $('#hdn_search_mode').val(new_mode);
        $('#change_search').html(mode_info[new_mode]['label']).attr('title', mode_info[new_mode]['title']);
    }
    $(document).ready(function() {
        //show_search_mode($('#hdn_search_mode').val());
    });
    function toggle_filter()
    {
        $('#div_filter').toggle();
    }
    
    function sel_rt_onchange(sel_record)
    {
        $('#txt_record_type_code').val($(sel_record).val());
    }
    
    
    function btn_reset_search_form_onclick()
    {
        var f = document.frmMain;
        
        f.sel_spec.value = '0';
        f.txt_record_type_code.value = '';
        f.sel_record_type.value = '';
        f.txt_record_no.value = '';
        f.txt_free_text.value = '';
        f.txt_receive_date_from.value = ''; //'01-01-<?php echo date('Y');?>';
        f.txt_receive_date_to.value = '';//'31-12-<?php echo date('Y');?>';
        f.txt_return_date_from.value = '';
        f.txt_return_date_to.value = '';
    }
    
</script>
<?php
$this->template->display('dsp_footer.php');