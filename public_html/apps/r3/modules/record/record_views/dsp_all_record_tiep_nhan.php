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
<div class="container-fluid">
    <div id="overDiv" style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></div>
    <form name="frmMain" id="frmMain" action="" method="POST" class="">
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
        <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type);?>


        <div id="solid-button" >
            <!--buton them moi-->
            <button type="button" name="addnew" class="btn btn-primary" onclick="btn_addnew_onclick();" accesskey="2">
                <i class="icon-plus"></i>
                <?php echo __('add new');?>
            </button>
            <!--button xoa-->
            <button type="button" name="trash" class="btn" onclick="btn_delete_onclick();">
                <i class="icon-trash"></i>
                <?php echo __('delete');?>
            </button>
            <!--button in-->
            <button type="button" name="trash" class="btn" onclick="btn_print_guide_for_citizen_onclick();">
                <i class="icon-print"></i>
                <?php echo __('guide print');?>
            </button>
        </div>
        <div class="clear" style="height: 10px;"></div>

        <div id="procedure"><?php
            if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'list')))
            {
                echo $this->render_form_display_all_record($arr_all_record,1);
            }?>
        </div>
        <div>
            <?php
                $v_page = isset($_POST['sel_goto_page']) ? ($_POST['sel_goto_page']) : 1;
                $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? ($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
                $total_record = (isset($arr_all_record[0]['TOTAL_RECORD'])) ? $arr_all_record[0]['TOTAL_RECORD'] : $v_rows_per_page;
                
                echo $this->paging($v_page, $v_rows_per_page, $total_record);
            ?>
        </div>
        <!--
        <div class="button-area">
            <input type="button" name="addnew" class="button add" value="<?php echo __('add new');?> (Alt+2)" onclick="btn_addnew_onclick();" accesskey="2" />
            <input type="button" name="trash" class="button delete" value="<?php echo __('delete');?>" onclick="btn_delete_onclick();"/>
        </div>-->

        <!-- Context menu -->
        <ul id="myMenu" class="contextMenu">
            <li class="print">
                <a href="#print">In phiếu biên nhận</a>
            </li>
            <li class="delete">
                <a href="#delete">Xoá bỏ</a>
            </li>
        </ul>
    </form>
</div>
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

                case 'delete':
                    quick_delete_item(v_record_id);
                    break;
            }
        });
        <?php if (strtoupper($this->active_role) == _CONST_TIEP_NHAN_ROLE): ?>
            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id =   $(this).attr('data-item_id');

                html = '';

                //Print
                html += '<a href="javascript:void(0)" onclick="print_record_ho_for_citizen(\'' + v_item_id + '\')" class="quick_action" title="In phiếu biên nhận cho công dân">';
                html += '<i class="icon-print"></i></a>';

                //Delete
                html += '<a href="javascript:void(0)" onclick="quick_delete_item(\'' + v_item_id + '\')" class="quick_action" title="Xoá bỏ">';
                html += '<i class="icon-trash"></i></a>';

                $(this).html(html);
            });

        <?php endif;?>
    });

    function print_record_ho_for_citizen(p_record_id)
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_print_ho_for_citizen/' + p_record_id;

        showPopWin(url, 800, 400, null, true);
    }
    function btn_print_guide_for_citizen_onclick()
    {
        v_record_type_code = $("#sel_record_type").val();
        v_record_type_name = $("#sel_record_type>option:selected").attr('title');
        
        if (v_record_type_code != '')
        {
            var url = '<?php echo $this->get_controller_url();?>dsp_print_guide_for_citizen/';
            url += QS + 'record_type_code=' + v_record_type_code;
            url += '&record_type_name=' + encodeURI(v_record_type_name);
            
            showPopWin(url, 800, 400, null, true);
        }
        else
        {
            alert('Bạn chưa chọn Mã loại hồ sơ!');
            return;
        }
    }
</script>
<?php $this->template->display('dsp_footer.php');