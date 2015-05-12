<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

//display header
$this->template->title = 'Chọn Văn bản';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$arr_all_doc_to_add = $VIEW_DATA['arr_all_doc_to_add'];

$v_filter = $VIEW_DATA['txt_filter'];
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url(); ?>dsp_all_doc_to_add">
    <?php
    echo $this->hidden('pop_win', 1);
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_doc_to_add');
    ?>

    <h4>Chọn Văn bản</h4>

    <!-- filter -->
    <div id="div_filter">
        <label>Số ký hiệu, hoặc trích yếu</label>
        <input type="text" name="txt_filter"
               value="<?php echo $v_filter; ?>"
               class="inputbox" size="30" autofocus="autofocus"
               onkeypress="txt_filter_onkeypress(this.form.btn_filter, event);"
               />
        <a href="javascript:;" class="easyui-linkbutton" iconCls="icon-search" onclick="btn_filter_onclick();">
            <?php echo _LANG_FILTER_BUTTON; ?>
        </a>
    </div>
    <table width="100%" class="adminlist" cellspacing="0" border="1">
        <colgroup>
            <col width="5%" />
            <col width="20%" />
            <col width="50%" />
            <col width="25%" />
        </colgroup>
        <tr>
            <th>#</th>
            <th>Số và ký hiệu</th>
            <th>Trích yêu</th>
            <th>Ngày văn bản</th>
        </tr>
    </table>
    <div style="height:400px;overflow: scroll">
        <!-- List item -->
        <table width="100%" class="adminList" cellspacing="0" border="1">
            <colgroup>
                <col width="5%" />
                <col width="20%" />
                <col width="50%" />
                <col width="25%" />
            </colgroup>
            <?php
            if (sizeof($arr_all_doc_to_add) > 0)
            {
                for ($i = 0; $i < count($arr_all_doc_to_add); $i++)
                {
                    $v_doc_id          = $arr_all_doc_to_add[$i]['PK_DOC'];
                    $v_doc_code        = $arr_all_doc_to_add[$i]['DOC_SO_KY_HIEU'];
                    $v_doc_description = $arr_all_doc_to_add[$i]['DOC_TRICH_YEU'];
                    $v_doc_date        = $arr_all_doc_to_add[$i]['DOC_NGAY_VAN_BAN'];
                    ?>
                    <tr class="ou_name">
                        <td class="center">
                            <input type="checkbox" name="chk_doc"
                                   value="<?php echo $v_doc_id; ?>"
                                   id="doc_<?php echo $v_doc_id; ?>"
                                   data-doc_code="<?php echo $v_doc_code; ?>"
                                   data-description="<?php echo $v_doc_description; ?>"
                                   data-doc_date="<?php echo $v_doc_date; ?>"
                                   />
                        </td>
                        <td>
                            <label for="doc_<?php echo $v_doc_id; ?>"><?php echo $v_doc_code; ?></label>
                        </td>
                        <td>
                            <label for="doc_<?php echo $v_doc_id; ?>"><?php echo $v_doc_description; ?></label>
                        </td>
                        <td>
                            <label for="doc_<?php echo $v_doc_id; ?>"><?php echo $v_doc_date; ?></label>
                        </td>
                    </tr>
                    <?php
                }
            }
            //Add empty rows
            echo $this->add_empty_rows($i + 1, _CONST_DEFAULT_ROWS_PER_PAGE, 4);
            ?>
        </table>
        <!-- /List item -->

    </div>
    <!-- Button -->
    <div class="button-area">
        <a href="javascript:;" class="easyui-linkbutton" iconCls="icon-save" onclick="get_selected_doc();" >
            <?php echo _LANG_UPDATE_BUTTON; ?>
        </a>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="<?php echo $v_back_action; ?>">
            <?php echo _LANG_CANCEL_BUTTON; ?>
        </a>
    </div>
</form>
<script>
                   function get_selected_doc()
                   {
                       var jsonObj = []; //declare array

                       q = "input[name='chk_doc']";
                       $(q).each(function(index) {
                           if ($(this).is(':checked'))
                           {
                               v_doc_id = $(this).val();
                               v_doc_code = $(this).attr('data-doc_code');
                               v_doc_description = $(this).attr('data-description');
                               v_doc_date = $(this).attr('data-doc_date');

                               jsonObj.push({'doc_id': v_doc_id, 'doc_code': v_doc_code, 'doc_description': v_doc_description, 'doc_date': v_doc_date});
                           }
                       });

                       returnVal = jsonObj;
                       window.parent.hidePopWin(true);
                   }
</script>

<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');