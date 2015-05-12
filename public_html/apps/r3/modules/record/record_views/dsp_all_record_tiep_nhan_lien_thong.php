<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_all_record         = $VIEW_DATA['arr_all_record'];

//header
$this->template->title = 'Tiếp nhận hồ sơ liên thông';
$this->template->display('dsp_header.php');
?>
<div class="container-fluid">
    <div id="overDiv" style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></div>
    <form name="frmMain" id="frmMain" action="" method="POST" class="form-horizontal">
        <?php
            echo $this->hidden('controller',$this->get_controller_url());
            echo $this->hidden('hdn_item_id','0');
            echo $this->hidden('hdn_item_id_list','');

            echo $this->hidden('hdn_dsp_single_method','dsp_single_exchange_record');
            echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record_tiep_nhan_lien_thong');
            echo $this->hidden('hdn_delete_method','delete_record');

            echo $this->hidden('hdn_role', _CONST_BAN_GIAO_ROLE);
        ?>
        <!-- filter -->
        <?php $this->dsp_div_filter($v_record_type_code, $arr_all_record_type);?>


        <div id="solid-button" >
            <!--button xoa-->
            <button type="button" name="trash" class="btn" onclick="btn_delete_onclick();">
                <i class="icon-trash"></i>
                <?php echo __('delete');?>
            </button>
        </div>
        <div class="clear" style="height: 10px;"></div>

        <div id="procedure">
            <table width="100%" class="adminlist table table-bordered table-striped">
                <colgroup>
                    <col width="2%">
                    <col width="*">
                    <col width="19%">
                    <col width="19%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr>
                        <th><input type="checkbox" name="chk_check_all" onclick="toggle_check_all(this,this.form.chk);"></th>
                        <th>Tên hồ sơ</th>
                        <th>Người đăng ký</th>
                        <th>Địa chỉ</th>
                        <th>Đơn vị gửi LT</th>
                    </tr>
                </thead>
                <?php foreach($arr_all_record as $arr_record):
                        $v_exchange_data      = $arr_record['C_EXCHANGE_DATA'];
                        $v_xml_data           = $arr_record['C_XML_DATA'];
                        $v_exchange_record_id = $arr_record['PK_EXCHANGE_RECORD'];
                        
                        $dom_xml_data      = new stdClass();
                        $dom_exchange_data = new stdClass();
                        
                        if(!empty($v_xml_data))
                        {
                            $dom_xml_data = simplexml_load_string($v_xml_data);
                        }
                        
                        if(!empty($v_xml_data))
                        {
                            $dom_exchange_data = simplexml_load_string($v_exchange_data);
                        }
                ?>
                <tr>
                    <td><input type="checkbox" name="chk" value="<?php echo $v_exchange_record_id?>" onclick="if (!this.checked) this.form.chk_check_all.checked=false;"></td>
                    <td>
                        <a href="javascript:void(0)" onclick="row_onclick('<?php echo $v_exchange_record_id?>')">
                            <?php echo get_xml_value($dom_exchange_data, '//record_type_name')?>
                        </a>
                    </td>
                    <td><?php echo get_xml_value($dom_xml_data, '//item[@id="txtName"]/value')?></td>
                    <td><?php echo get_xml_value($dom_xml_data, '//item[@id="txtDiaChi"]/value')?></td>
                    <td><?php echo get_xml_value($dom_exchange_data, '//unit_name')?></td>
                </tr>
                <?php endforeach;?>
                <?php
                    $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? ($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
                    if($v_rows_per_page > count($arr_all_record))
                    {
                        $v_loop = $v_rows_per_page - count($arr_all_record);
                        for($i=0;$i<$v_loop;$i++)
                        {
                            echo '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>';
                        }
                    }
                ?>
            </table>
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
        v_record_type_name = $("#sel_record_type>option:selected").text();
        
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