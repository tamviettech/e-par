<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_all_record         = $VIEW_DATA['arr_all_record'];
$MY_TASK                = $VIEW_DATA['MY_TASK'];

//header
$this->template->title = 'Tiếp nhận hồ sơ';
$this->template->display('dsp_header.php');

?>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id','0');
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method','update_record');
    echo $this->hidden('hdn_delete_method','delete_record');

    echo $this->hidden('hdn_role', _CONST_BAN_GIAO_ROLE);
    echo $this->hidden('MY_TASK', $MY_TASK);
    ?>
    <?php echo $this->dsp_div_notice($VIEW_DATA['active_role_text'] );?>
    <!-- filter -->
    <div id="div_filter">
        (1)&nbsp;<label>Mã loại hồ sơ</label>
        <input type="text" name="txt_record_type_code" id="txt_record_type_code"
               value="<?php echo $v_record_type_code; ?>"
               class="inputbox upper_text" size="10" maxlength="10"
               onkeypress="txt_record_type_code_onkeypress(event);"
               autofocus="autofocus"
               accesskey="1"
               />&nbsp;
        <select name="sel_record_type" class="chosen" id="sel_record_type" style="width:75%; color:#000000;"
                onchange="sel_record_type_onchange(this)">
            <option value="">-- Chọn loại hồ sơ --</option>
            <?php echo $this->generate_select_option($arr_all_record_type, $v_record_type_code); ?>
        </select>
        <input type="text" name="noname" style="visibility: hidden"/>
    </div>
    <div class="clear">&nbsp;</div>

    <!--
    <div id="toolbar-box">
        <div class="m">
            <div class="toolbar-list" id="toolbar">
                <ul>
                    <li class="button" id="toolbar-new">
                        <a href="javascript:void(0)" onclick="btn_addnew_onclick()" class="toolbar">
                        <span class="icon-32-new">
                        </span><?php echo __('add new');?></a>
                    </li>

                    <li class="button" id="toolbar-trash">
                        <a href="#" onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('articles.trash')}" class="toolbar">
                        <span class="icon-32-trash">
                        </span>
                        Xoá bỏ
                        </a>
                    </li>
                </ul>
                <div class="clr"></div>
            </div>
            <div class="pagetitle icon-48-article">
                <h2><?php echo $VIEW_DATA['active_role_text'];?></h2>
            </div>
        </div>
    </div>

    <div id="solid-button">
        <input type="button" class="solid add" value="<?php echo __('add new');?>"
               onclick="btn_addnew_onclick();" />
        <input type="button" name="addnew" class="solid delete" value="<?php echo __('delete');?>"
               onclick="btn_delete_onclick();" />
    </div>
    <div class="clear">&nbsp;</div>
-->

    <div id="procedure">
        <?php
        if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'list')))
        {
            echo $this->render_form_display_all_record($arr_all_record);
        }
        ?>
    </div>
    <div><?php echo $this->paging2($arr_all_record);?></div>
    <!--
    <div class="button-area">
        <input type="button" name="addnew" class="button add" value="(2) <?php echo __('add new');?>" onclick="btn_addnew_onclick();" accesskey="2" />
        <input type="button" name="trash" class="button delete" value="<?php echo __('delete');?>" onclick="btn_delete_onclick();"/>
    </div> -->

    <!-- Context menu -->
    <ul id="myMenu" class="contextMenu">
        <li class="print">
            <a href="#print">In phiếu biên nhận</a>
        </li>
    </ul>
</form>
<script>
    $(function() {
        //Show context on each row
        $(".adminlist tr[role='presentation']").contextMenu({
            menu: 'myMenu'
        }, function(action, el, pos) {
            v_record_id = $(el).attr('data-item_id');
            switch (action){
                case 'print':
                    print_record_ho_for_citizen(v_record_id);
                    break;
            }
        });

        $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
            v_item_id =   $(this).attr('data-item_id');

            html = '';

            //Print
            html += '<a href="javascript:void(0)" onclick="print_record_ho_for_citizen(\'' + v_item_id + '\')" title="In phiếu biên nhận cho công dân" class="quick-action">'
            html += '<i class="icon-print"></i></a>';

            $(this).html(html);
        });

    });

    function print_record_ho_for_citizen(p_record_id)
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_print_ho_for_citizen/' + p_record_id;

        showPopWin(url, 1000, 600, null, true);
    }
</script>
<?php $this->template->display('dsp_footer.php');