<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}
//display header
$this->template->title = 'Trình văn bản lên lãnh đạo';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

//------------------------------------------------------------------------------
$v_doc_id                   = $VIEW_DATA['doc_id'];
$arr_all_ou_option          = $VIEW_DATA['arr_all_ou_option'];
$arr_all_monitor_user       = $VIEW_DATA['arr_all_monitor_user'];
$arr_all_exec_user          = $VIEW_DATA['arr_all_exec_user'];
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>do_allot_doc"><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_doc');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_doc');
    echo $this->hidden('hdn_update_method', 'update_doc');
    echo $this->hidden('hdn_delete_method', 'delete_doc');

    echo $this->hidden('hdn_item_id', $v_doc_id);
    echo $this->hidden('pop_win', $v_pop_win);

    echo $this->hidden('hdn_allot_by', 'p');
    echo $this->hidden('hdn_direct_exec_ou_id', '-1');
    echo $this->hidden('hdn_direct_exec_ou_name', '');
    echo $this->hidden('hdn_co_exec_ou_id_list', '');

    echo $this->hidden('hdn_direct_exec_user_id', '-1');
    echo $this->hidden('hdn_direct_exec_user_name', '');
    echo $this->hidden('hdn_co_exec_user_id_list', '');

    echo $this->hidden('hdn_monitor_user_id', '-1');
    echo $this->hidden('hdn_monitor_user_name', '');

    ?>
    <!-- Toolbar -->
    <h2 class="module_title">Phân công thụ lý văn bản đến</h2>
    <!-- /Toolbar -->

    <script>
        $(function() {
            $( "#tabs_allot" ).tabs();
        });
    </script>
    <div id="tabs_allot">
        <ul>
            <li><a href="#by_ou" onclick="allot_by('p')">Phân cho phòng</a></li>
            <li><a href="#by_user" onclick="allot_by('c')">Phân cho cá nhân</a></li>
        </ul>
        <div id="by_ou">
            <div class="Row">
                <div class="left-Col">Phòng thụ lý chính <span class="required">(*)</span> </div>
                <div class="right-Col">
                    <select name="sel_direct_exec_ou" id="sel_direct_exec_ou" onchange="sel_direct_exec_ou_onchange(this)">
                        <option value="-1">--Chọn phòng thụ lý chính--</option>
                        <?php echo $this->generate_select_option($arr_all_ou_option, -1);?>
                    </select>
                </div>
            </div>
            <div class="Row">
                <div class="left-Col">Phối hợp</div>
                <div class="right-Col">
                    <div id="div_co_exec_user" style="height:160px; overflow: auto;border:1px solid;">
                        <?php foreach ($arr_all_ou_option as $code => $name) :?>
                            <input type="checkbox" name="chk_co_exec_ou" id="chk_co_exec_ou<?php echo $code;?>" value="<?php echo $code;?>" />
                            <label for="chk_co_exec_ou<?php echo $code;?>"><?php echo $name;?></label><br/>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="by_user">
            <div class="Row">
                <div class="left-Col">Cán bộ thụ lý chính <span class="required">(*)</span> </div>
                <div class="right-Col">
                    <select name="sel_direct_exec_user" id="sel_direct_exec_user" onchange="sel_direct_exec_user_onchange(this)">
                        <option value="-1">--Chọn cán bộ thụ lý chính--</option>
                        <?php echo $this->generate_select_option($arr_all_exec_user, -1);?>
                    </select>
                </div>
            </div>
            <div class="Row">
                <div class="left-Col">Phối hợp</div>
                <div class="right-Col">
                    <div id="div_co_exec_ou" style="height:160px; overflow: auto;border:1px solid;">
                        <?php foreach ($arr_all_exec_user as $code => $name) :?>
                            <input type="checkbox" name="chk_co_exec_user" id="chk_co_exec_user<?php echo $code;?>" value="<?php echo $code;?>" />
                            <label for="chk_co_exec_user<?php echo $code;?>"><?php echo $name;?></label><br/>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="Row">
                <div class="left-Col">Lãnh đạo phụ trách</div>
                <div class="right-Col">
                    <select name="sel_monitor_user" id="sel_monitor_user" onchange="sel_monitor_user_onchange(this)">
                        <option value="-1">--Chọn lãnh đạo phụ trách--</option>
                        <?php echo $this->generate_select_option($arr_all_monitor_user, -1);?>
                    </select>
                </div>
            </div>
            <div class="Row">
                <div class="left-Col">Ý kiến chỉ đạo</div>
                <div class="right-Col">
                    <textarea name="txt_allot_message" rows="3" cols="106"></textarea>
                </div>
            </div>
            <div class="Row">
                <div class="left-Col">Thời hạn xử lý</div>
                <div class="right-Col">
                    Trong: <input type="text" name="txt_exec_day" size="4" onkeyup="inputIntPlus(this)"
                                    onchange="date_which_diff_day(parseInt(this.value))" maxlength="2"/> ngày làm việc.
                    (Tức đến ngày: <input type="textbox" name="txt_deadline_exec_date" id="txt_deadline_exec_date" size="15"
                                          class=" text  valid" value="Ngày/Tháng/Năm" readonly="readonly"
                                />
                     )
                </div>
            </div>
            <div class="Row">
                <div class="left-Col">&nbsp;</div>
                <div class="right-Col">
                    <input type="checkbox" name="chk_open_doc_profile" id="chk_open_doc_profile" />
                    <label for="chk_open_doc_profile">Yêu cầu mở hồ sơ công việc</label>
                </div>
            </div>
    </div> <!-- id="tabs_allot" -->

    <!-- Button -->
    <div class="button-area">
        <a href="#" class="easyui-linkbutton" iconCls="icon-add" onclick="btn_allot_onlick();" >
            Phân công
        </a>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="<?php echo $v_back_action;?>" >
            <?php echo _LANG_CLOSE_WINDOW_BUTTON; ?>
        </a>
    </div>
</form>
<script>

    function date_which_diff_day(nDay)
    {
        /*
        var date = new Date();
        date.setDate(date.getDate() + nDay);
        futMonth = date.getMonth() + 1;
        return date.getDate() + "/" + futMonth + "/" + date.getFullYear();
        */

        $.ajax({url:"<?php echo SITE_ROOT;?>/cores/calendar/date_which_diff_day/" + nDay
            ,success:function(result){
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

        if (f.hdn_allot_by.value == 'p')
        {
            if (f.hdn_direct_exec_ou_id.value == "-1")
            {
                alert('Phải xác định phòng thụ lý chính!');
                f.sel_direct_exec_ou.focus();
                return false;
            }

            //Lay danh sach dong ban phoi hop thu ly
            f.hdn_co_exec_ou_id_list.value = get_all_checked_checkbox(f.chk_co_exec_ou, ",");
        }

        else
        {
            //CB thu ly chinh
            if (f.hdn_direct_exec_user_id.value == "-1")
            {
                alert('Phải xác định cán bộ thụ lý chính!');
                f.sel_direct_exec_user.focus();
                return false;
            }

            //Lay danh sach CB phoi hop thu ly
            f.hdn_co_exec_user_id_list.value = get_all_checked_checkbox(f.chk_co_exec_user ,",");
        }

        f.submit();
    }

    function allot_by(by)
    {
        document.frmMain.hdn_allot_by.value = by;
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');