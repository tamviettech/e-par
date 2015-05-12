<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
//display header
$this->template->title = 'Xử lý ban đầu';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

//------------------------------------------------------------------------------
$v_doc_id          = $VIEW_DATA['doc_id'];
$arr_all_ou_option = $VIEW_DATA['arr_all_ou_option'];
//$arr_all_monitor_user       = $VIEW_DATA['arr_all_monitor_user'];
$arr_all_exec_user = $VIEW_DATA['arr_all_exec_user'];
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url(); ?>do_pre_processing_doc">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_doc');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_doc');
    echo $this->hidden('hdn_update_method', 'update_doc');
    echo $this->hidden('hdn_delete_method', 'delete_doc');

    echo $this->hidden('hdn_item_id', $v_doc_id);
    echo $this->hidden('pop_win', $v_pop_win);

    echo $this->hidden('hdn_allot_by', '1');
    echo $this->hidden('hdn_direct_exec_ou_id', '-1');
    echo $this->hidden('hdn_direct_exec_ou_name', '');
    echo $this->hidden('hdn_co_exec_ou_id_list', '');

    echo $this->hidden('hdn_direct_exec_user_id', '-1');
    echo $this->hidden('hdn_direct_exec_user_name', '');
    echo $this->hidden('hdn_co_exec_user_id_list', '');

    echo $this->hidden('hdn_monitor_user_id', '-1');
    echo $this->hidden('hdn_monitor_user_name', '');

    echo $this->hidden('hdn_allot_user_name', '');
    ?>
    <!-- Toolbar -->
    <h2 class="module_title">Xử lý ban đầu văn bản đến</h2>
    <!-- /Toolbar -->

    <script>
        $(function() {
            $("#tabs_allot").tabs();
        });
    </script>
    <div id="tabs_allot">
        <ul>
            <li><a href="#to_leader" onclick="allot_by(1)">Trình lãnh đạo</a>
            </li>
            <li><a href="#to_ou" onclick="allot_by(2)">Chuyển cho phòng ban</a>
            </li>
            <li><a href="#to_person" onclick="allot_by(3)">Chuyển cho cán bộ</a>
            </li>
        </ul>
        <div id="to_leader">
            <table class="none-border-table">
                <tr>
                    <td>
                        Trình lên cho: <span class="required">(*)</span>
                    </td>
                    <td>
                        <select name="sel_allot_user" id="sel_allot_user"
                                onchange="this.form.hdn_allot_user_name.value = this.options[this.selectedIndex].text">
                            <option value="-1">--Chọn từ danh sách--</option>
                            <?php echo $this->generate_select_option($VIEW_DATA['arr_all_allot_user'], -1); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Nội dung trình:</td>
                    <td><textarea rows="1" cols="90" name="txt_submit_message"></textarea></td>
                </tr>
            </table>
        </div>
        <div id="to_ou">
            <table class="none-border-table">
                <tr>
                    <td>
                        Đến:
                    </td>
                    <td>
                        <select name="sel_direct_exec_ou" id="sel_direct_exec_ou"
                                onchange="sel_direct_exec_ou_onchange(this)">
                            <option value="-1">--Chọn phòng tiếp nhận--</option>
                            <?php echo $this->generate_select_option($arr_all_ou_option, -1); ?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <div id="to_person">
            <table class="none-border-table">
                <tr>
                    <td>
                        Đến:
                    </td>
                    <td>
                        <select name="sel_direct_exec_user" id="sel_direct_exec_user"
                                onchange="sel_direct_exec_user_onchange(this)">
                            <option value="-1">--Chọn cán bộ tiếp nhận--</option>
                            <?php echo $this->generate_select_option($arr_all_exec_user, -1); ?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!-- id="tabs_allot" -->

    <!-- Button -->
    <div class="button-area">
        <a href="#" class="easyui-linkbutton" iconCls="icon-save" onclick="btn_allot_onlick();">
            Thực hiện
        </a>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="<?php echo $v_back_action; ?>">
            <?php echo _LANG_CLOSE_WINDOW_BUTTON; ?>
        </a>
    </div>
</form>
<script>
        function date_which_diff_day(nDay)
        {
            $.ajax({url: "<?php echo SITE_ROOT; ?>/cores/calendar/date_which_diff_day/" + nDay
                        , success: function(result) {
                    $("#txt_deadline_exec_date").val(result);
                }
            });
        }

        function sel_direct_exec_ou_onchange(obj)
        {
            var f = document.frmMain;
            f.hdn_direct_exec_ou_id.value = obj.options[obj.selectedIndex].value;
            f.hdn_direct_exec_ou_name.value = obj.options[obj.selectedIndex].text;
        }

        function sel_direct_exec_user_onchange(obj)
        {
            var f = document.frmMain;
            f.hdn_direct_exec_user_id.value = obj.options[obj.selectedIndex].value;
            f.hdn_direct_exec_user_name.value = obj.options[obj.selectedIndex].text;
        }

        function sel_monitor_user_onchange(obj)
        {
            var f = document.frmMain;
            f.hdn_monitor_user_id.value = obj.options[obj.selectedIndex].value;
            f.hdn_monitor_user_name.value = obj.options[obj.selectedIndex].text;
        }

        function btn_allot_onlick()
        {
            var f = document.frmMain;

            if (f.hdn_allot_by.value == '1')
            {
                if (f.sel_allot_user.value == "-1")
                {
                    alert('Phải xác lãnh đạo để trình!');
                    f.sel_allot_user.focus();
                    return false;
                }
            }
            else if (f.hdn_allot_by.value == '2')
            {
                if (f.hdn_direct_exec_ou_id.value == "-1")
                {
                    alert('Phải xác định phòng thụ lý chính!');
                    f.sel_direct_exec_ou.focus();
                    return false;
                }
            }
            else if (f.hdn_allot_by.value == '3')
            {
                //CB thu ly chinh
                if (f.hdn_direct_exec_user_id.value == "-1")
                {
                    alert('Phải xác định cán bộ thụ lý chính!');
                    f.sel_direct_exec_user.focus();
                    return false;
                }
            }

            f.submit();
        }

        function allot_by(by)
        {
            document.frmMain.hdn_allot_by.value = by;
        }
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');