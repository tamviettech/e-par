<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
//display header
$this->template->title = 'Chia sẻ văn bản đến NSD';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
//------------------------------------------------------------------------------

$v_doc_id         = $VIEW_DATA['doc_id'];
$arr_all_doc_user = $VIEW_DATA['arr_all_doc_user'];

$dom_log = simplexml_load_string($VIEW_DATA['xml_log']);
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url(); ?>do_update_doc_user"><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_folder');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_folder');
    echo $this->hidden('hdn_update_method', 'update_folder');
    echo $this->hidden('hdn_delete_method', 'delete_folder');

    echo $this->hidden('hdn_item_id', $v_doc_id);
    echo $this->hidden('hdn_user_id_list', '');
    echo $this->hidden('XmlData', '');
    ?>
    <div id="doc_user_list">
        <fieldset>
            <legend>Danh sách NSD được chia sẻ</legend>
            <div id="user_of_doc" class="edit-box">
                <table width="100%" class="adminlist" cellspacing="0" border="1" id="tbl_shared_user">
                    <colgroup>
                        <col width="5%" />
                        <col width="75%" />
                        <col width="20%" />
                    </colgroup>
                    <tr>
                        <th>#</th>
                        <th>Tên NSD</th>
                        <th>Trạng thái</th>
                    </tr>
                    <tr><th></th><th></th><th></th></tr>
                    <?php
                    for ($i = 0; $i < sizeof($arr_all_doc_user); $i++):
                        $v_user_id   = $arr_all_doc_user[$i]['FK_USER'];
                        $v_user_name = $arr_all_doc_user[$i]['C_NAME'];
                        $v_user_code = $arr_all_doc_user[$i]['C_LOGIN_NAME'];
                        ?>
                        <tr id="tr_user_<?php echo $v_user_id; ?>" class="row<?php echo ($i % 2); ?>">
                            <td class="center">
                                <input type="checkbox" name="chk_user" value="<?php echo $v_user_id; ?>" id="user_<?php echo $v_user_id; ?>" />
                            </td>
                            <td>
                                <img src="<?php echo SITE_ROOT; ?>public/images/icon-16-user.png" border="0" align="absmiddle" />
                                <label for="user_<?php echo $v_user_id; ?>"><?php echo $v_user_name; ?></label>
                            </td>
                            <td>
                                <!-- Đã xem <?php echo $dom_log ? count($dom_log->xpath("//action[user_code='$v_user_code'][code='view']")) : 0; ?> lần-->
                                <?php if ($dom_log && count($dom_log->xpath("//action[user_code='$v_user_code'][code='view']")) > 0): ?>
                                    Đã xem
                                <?php else: ?>
                                    <span style="color:#F00;font-weight:bold">Chưa xem</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endfor; ?>
                </table>
            </div>
            <div id="user_of_doc_action">
                <a href="javascript:;" class="easyui-linkbutton" iconCls="icon-add">
                    Thêm NSD
                </a>
                <br/>
                <a href="#" class="easyui-linkbutton" iconCls="icon-remove" onclick="remove_shared_user()">
                    Bỏ NSD
                </a>
            </div>
            <div class="clear">&nbsp;</div>
        </fieldset>
    </div>

    <div class="button-area">
        <?php if ($this->check_permission('PHAN_PHOI_VAN_BAN_NOI_BO')): ?>
            <a href="javascript:;" class="easyui-linkbutton" iconCls="icon-cancel" onclick="btn_update_doc_user_onclick();">
                <?php echo _LANG_UPDATE_BUTTON; ?>
            </a>
        <?php endif; ?>

        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        <a href="javascript:;" class="easyui-linkbutton" iconCls="icon-cancel" onclick="btn_update_doc_user_onclick();" onclick="<?php echo $v_back_action; ?>">
            <?php echo _LANG_CLOSE_WINDOW_BUTTON; ?>
        </a>   
    </div>
</form>
<script>
                    function dsp_all_user_to_add()
                    {
                        var url = SITE_ROOT + 'cores/user/dsp_all_user_to_add/&pop_win=1';

                        showPopWin(url, 450, 350, add_user);
                    }
                    function add_user(returnVal) {
                        for (i = 0; i < returnVal.length; i++)
                        {
                            v_user_id = returnVal[i].user_id;
                            v_user_name = returnVal[i].user_name;

                            //Neu user chua co trong group thi them vao
                            q = '#user_' + v_user_id;
                            if ($(q).length < 1)
                            {
                                html = '<tr id="tr_user_' + v_user_id + '">';
                                html += '<td class="center">';
                                html += '<input type="checkbox" name="chk_user" value="' + v_user_id + '" id="user_' + v_user_id + '" />';
                                html += '</td>';
                                html += '<td>';
                                html += '<img src="' + SITE_ROOT + 'public/images/icon-16-user.png" border="0" align="absmiddle" />';
                                html += '<label for="user_' + v_user_id + '">' + v_user_name + '</label>';
                                html += '</td><td></td></tr>';
                                $('#tbl_shared_user').append(html);
                            }
                        }
                    }

                    function remove_shared_user()
                    {
                        var q = "input[name='chk_user']";
                        $(q).each(function(index) {
                            if ($(this).is(':checked'))
                            {
                                v_user_id = $(this).val();
                                s = '#tr_user_' + v_user_id;
                                $(s).remove();
                            }
                        });
                    }

                    function btn_update_doc_user_onclick()
                    {
                        var v_user_id_list = '';
                        var q = "input[name='chk_user']";
                        $(q).each(function(index) {
                            v_user_id_list += (v_user_id_list != '') ? ',' + $(this).val() : $(this).val();
                        });

                        $("#hdn_user_id_list").val(v_user_id_list);
                        $("#frmMain").submit();
                    }
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');