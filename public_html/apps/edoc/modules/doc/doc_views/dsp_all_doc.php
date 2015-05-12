<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}
//header

$this->template->title = $VIEW_DATA['direction_text'];
$this->template->display('dsp_header.php');

$v_processing_filter =  isset($VIEW_DATA['processing']) ? $VIEW_DATA['processing'] : -1;
$arr_all_ou_option          = $VIEW_DATA['arr_all_ou_option'];
?>
        <form name="frmMain" id="frmMain" action="#" method="POST">
            <?php
            $v_direction = $VIEW_DATA['direction'];
            $v_direction_text = $VIEW_DATA['direction_text'];
            $v_type = $VIEW_DATA['type'];
            $v_type_text = $VIEW_DATA['type_text'];
            $arr_all_doc = $VIEW_DATA['arr_all_doc'];

            $v_filter = $VIEW_DATA['filter'];
            $v_begin_date = $VIEW_DATA['v_begin_date'];
            $v_end_date = $VIEW_DATA['v_end_date'];


            echo $this->hidden('direction', $v_direction);
            echo $this->hidden('direction_text', $v_direction_text);
            echo $this->hidden('type', $v_type);
            echo $this->hidden('type_text', $v_type_text);

            echo $this->hidden('controller', $this->get_controller_url());
            echo $this->hidden('hdn_item_id', '0');
            echo $this->hidden('hdn_item_id_list', '');

            echo $this->hidden('hdn_dsp_single_method', 'dsp_single_doc');
            echo $this->hidden('hdn_dsp_all_method', strtolower($v_direction) . '/' . strtolower($v_type));
            echo $this->hidden('hdn_update_method', 'update_doc');
            echo $this->hidden('hdn_delete_method', 'delete_doc');

            echo $this->hidden('hdn_folder_id', '');
            ?>
            <!-- Toolbar -->
            <h2 class="module_title"><?php echo $v_direction_text; ?> > <?php echo $v_type_text; ?></h2>
            <!-- /Toolbar -->

            <!-- filter -->
            <div id="div_filter">
                <?php if ($v_direction == 'VBDEN'): ?>
                    <label>Đã phân cho:</label>
                    <select name="sel_direct_ou" onchange="this.form.btn_filter.click()">
                        <option value="-1">-- Tất cả --</option>
                        <?php echo $this->generate_select_option($arr_all_ou_option, $VIEW_DATA['direct_ou_filter']); ?>
                    </select>

                <?php endif; ?>
                <?php if ($v_direction == 'VBDEN'): ?>
                    <label>Loại văn bản:</label>
                    <select name="type_filter" onchange="this.form.btn_filter.click()">
                        <option value="">-- Tất cả --</option>
                        <?php echo $this->generate_select_option($arr_all_list_type, $v_type_filter); ?>
                    </select>

                    <label>Trạng thái:</label>
                    <select name="sel_processing">
                        <option value="-1">-- Tất cả --</option>
                        <option value="1" <?php echo ($v_processing_filter == 1) ? 'selected' : '' ?>>Đang xử lý - Chưa đến hạn</option>
                        <option value="2" <?php echo ($v_processing_filter == 2) ? 'selected' : '' ?>>Đang xử lý - Đã quá hạn</option>
                        <option value="3" <?php echo ($v_processing_filter == 3) ? 'selected' : '' ?>>Đã hoàn thành - Đúng hạn</option>
                        <option value="4" <?php echo ($v_processing_filter == 4) ? 'selected' : '' ?>>Đã hoàn thành - Quá hạn</option>
                    </select>
                    <br/>
                <?php endif; ?>


                <label>Từ ngày: &nbsp;</label>
                <input type="text" name="txt_begin_date" value = "<?php echo $v_begin_date; ?>" id="txt_begin_date" />
                <label>Đến ngày:</label>
                <input type="text" name="txt_end_date" value = "<?php echo $v_end_date; ?>" id="txt_end_date" />

                <input type="text" name="txt_filter"
                       value="<?php echo $v_filter; ?>"
                       class="inputbox" size="25" autofocus="autofocus"
                       onkeypress="txt_filter_onkeypress(this.form.btn_filter,event);"
                       />
                <a onclick="btn_filter_onclick();" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search"> <?php echo _LANG_FILTER_BUTTON; ?> </a>
<!--                <input type="button" class="filter_button" onclick="btn_filter_onclick();"
                       name="btn_filter" value="<?php echo _LANG_FILTER_BUTTON; ?>"
                       />-->
                <?php if ($v_direction != 'VBNOIBO'): ?>
                <a onclick="btn_print_document_book_onclick();" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print"> In sổ </a>
<!--                 <input type="button" name="btn_print_document_book" class="filter_button"
                        onclick="btn_print_document_book_onclick()"
                        value="In sổ"
                        />-->
                <?php endif; ?>
            </div>
            
        
            <?php
            $xml_file = strtolower('xml_' . $v_type . '_' . $v_direction . '_list.xml');
            if ($this->load_xml($xml_file)) {
                echo $this->render_form_display_all_by_doc_type($arr_all_doc);
            } else {
                echo 'Missing: ' . $v_direction_text . ' -> ' . $v_type_text;
                echo '<br>Need: ' . $xml_file;
            }
            ?>
            <div>
                <?php
                    //Phan trang
                    $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? $_POST['sel_rows_per_page'] : _CONST_DEFAULT_ROWS_PER_PAGE;
                    if (isset($arr_all_doc[1]['TOTAL_RECORD'])) {
                        $v_page = isset($_POST['sel_goto_page']) ? $_POST['sel_goto_page'] : 1;
                        $v_total_record = $arr_all_doc[1]['TOTAL_RECORD'];
                    } else {
                        $v_page = 1;
                        $v_total_record = $v_rows_per_page;
                    }
                    echo $this->paging($v_page, $v_rows_per_page, $v_total_record);
                ?>
            </div>
            <div class="button-area">
            <?php
            $v_is_granted_update = FALSE;
            $v_is_graned_delete = FALSE;
            if (strtoupper($v_direction) === 'VBDEN') {
                $v_is_granted_update = $this->check_permission('VAO_SO_VAN_BAN_DEN');
                $v_is_graned_delete = $this->check_permission('XOA_VAN_BAN_DEN');
            } elseif (strtoupper($v_direction) === 'VBDI') {
                $v_is_granted_update = $this->check_permission('SOAN_THAO_VAN_BAN_DI') OR $this->check_permission('VAO_SO_VAN_BAN_DI');
                $v_is_graned_delete = $this->check_permission('XOA_VAN_BAN_DI');
            } elseif (strtoupper($v_direction) === 'VBTRACUU') {
                $v_is_granted_update = $this->check_permission('NHAP_VAN_BAN_TRA_CUU');
                $v_is_graned_delete = $this->check_permission('XOA_VAN_BAN_DI');
            } elseif (strtoupper($v_direction) === 'VBNOIBO') {
                $v_is_granted_update = $this->check_permission('SOAN_THAO_VAN_BAN_NOI_BO');
                $v_is_graned_delete = $this->check_permission('XOA_VAN_BAN_NOI_BO');
            }
            ?>
                <?php if ($v_is_granted_update): ?>
                    <a href="javascript:void(0)" ></a>
                    <a href="#" class="easyui-linkbutton" onclick="pop_up_add_new();" iconCls="icon-add">Thêm mới</a>
                    <!--<input type="button" name="btn_addnew" class="button add" value="<?php echo _LANG_ADDNEW_BUTTON; ?>" onclick="pop_up_add_new();"/>-->
                <?php endif; ?>

                <?php /*
                  <?php if ($v_is_granted_update And (0 > 1)): ?>
                  <input type="button" name="btn_delete" class="button delete" value="<?php echo _LANG_DELETE_BUTTON;?>" onclick="btn_delete_onclick();"/>
                  <?php endif; ?>
                 */ ?>
            </div>
</form>
<script>
    $(document).ready( function() {
        <?php if (strtoupper($v_direction) === 'VBNOIBO'):?>
            <?php if ($this->check_permission('PHAN_PHOI_VAN_BAN_NOI_BO')): ?>
                $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                    v_item_id =   $(this).attr('data-item_id');

                    var v_url = '<?php echo $this->get_controller_url();?>dsp_doc_user/' + v_item_id;
                    v_url += '&hdn_item_id=' + v_item_id
                                + '&pop_win=1'
                                + '&direction=VBNOIBO'
                                + '&type=' + $(this).attr('data-doc_type');
                    html = '<a href="javascript:void(0);" onclick="row_onclick_pop_win(\'' + v_url + '\')">';
                    html += '<img src="' + SITE_ROOT + 'public/images/icon-32-user.png" /></a>';

                    $(this).append(html);
                });
            <?php endif;?>
        <?php endif;?>

        <?php if (strtoupper($v_type) == 'VBNB_TIEP_DAN_DK'):?>
            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id =   $(this).attr('data-item_id');

                var v_url = '<?php echo $this->get_controller_url();?>dsp_print_tiep_dan_dinh_ky/' + v_item_id;
                v_url += '&hdn_item_id=' + v_item_id
                            + '&pop_win=1'
                            + '&direction=VBDEN'
                            + '&type=' + $(this).attr('data-doc_types');
                html = '<a href="javascript:void(0);" onclick="row_onclick_pop_win(\'' + v_url + '\')">';
                html += '<img src="' + SITE_ROOT + 'public/images/icon-32-print.png" /></a>';

                $(this).append(html);
            });
        <?php endif;?>
        <?php if (strtoupper($v_type) == 'VBNB_TIEP_DAN_TX'):?>
            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id =   $(this).attr('data-item_id');

                var v_url = '<?php echo $this->get_controller_url();?>dsp_print_tiep_dan_thuong_xuyen/' + v_item_id;
                v_url += '&hdn_item_id=' + v_item_id
                            + '&pop_win=1'
                            + '&direction=VBDEN'
                            + '&type=' + $(this).attr('data-doc_types');
                html = '<a href="javascript:void(0);" onclick="row_onclick_pop_win(\'' + v_url + '\')">';
                html += '<img src="' + SITE_ROOT + 'public/images/icon-32-print.png" /></a>';

                $(this).append(html);
            });
        <?php endif;?>
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
    });

    function pop_up_add_new(){
        var url =  '<?php echo $this->get_controller_url() . strtolower($v_direction);?>/dsp_single_doc/&doc_id=0&hdn_item_id=0&pop_win=1&direction=<?php echo $v_direction;?>&type=<?php echo $v_type;?>';
        showPopWin(url ,920,550, null);
    }

    function btn_print_document_book_onclick()
    {
        var url =  '<?php echo $this->get_controller_url() . strtolower($v_direction);?>/dsp_print_document_book_option/?pop_win=1&direction=<?php echo $v_direction;?>&type=<?php echo $v_type;?>';
        showPopWin(url ,400, 250, null);
    }
 
   
</script>
<?php $this->template->display('dsp_footer.php');
