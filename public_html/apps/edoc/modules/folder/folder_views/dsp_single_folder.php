<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}
?>
<?php
//display header
$this->template->title = 'Cập nhật Hồ sơ lưu';
$this->template->display('dsp_header.php');

$arr_single_folder      = $VIEW_DATA['arr_single_folder'];
$arr_all_doc_by_folder  = $VIEW_DATA['arr_all_doc_by_folder'];
$arr_all_shared_user    = $VIEW_DATA['arr_all_shared_user'];
$arr_all_shared_ou      = $VIEW_DATA['arr_all_shared_ou'];

if (isset($arr_single_folder['PK_FOLDER'])) {
    $v_folder_id = $arr_single_folder['PK_FOLDER'];
    $v_code = $arr_single_folder['C_CODE'];
    $v_name = $arr_single_folder['C_NAME'];
    $v_description = $arr_single_folder['C_DESCRIPTION'];
    $v_status = $arr_single_folder['C_STATUS'];
    $v_is_closed = $arr_single_folder['C_IS_CLOSED'];
    $v_xml_data = $arr_single_folder['C_XML_DATA'];
    $v_create_user_id = $arr_single_folder['FK_USER'];
} else {
    $v_folder_id = 0;
    $v_code = '';
    $v_name = '';
    $v_xml_data = '';
    $v_description = '';
    $v_status  = 1;
    $v_is_closed = 0;
    $v_create_user_id = session::get('user_id');
}
?>
<form name="frmMain" method="post" id="frmMain" action=""><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_folder');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_folder');
    echo $this->hidden('hdn_update_method', 'update_folder');
    echo $this->hidden('hdn_delete_method', 'delete_folder');

    echo $this->hidden('hdn_item_id', $v_folder_id);
    echo $this->hidden('XmlData', $v_xml_data);

    // Luu dieu kien loc
    $v_filter = isset($_POST['txt_filter']) ? $_POST['txt_filter'] : '';
    $v_page = isset($_POST['sel_goto_page']) ? Model::replace_bad_char($_POST['sel_goto_page']) : 1;
    $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? Model::replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;

    echo $this->hidden('txt_filter', $v_filter);
    echo $this->hidden('sel_goto_page', $v_page);
    echo $this->hidden('sel_rows_per_page', $v_rows_per_page);


    echo $this->hidden('hdn_doc_id_list', '');
    echo $this->hidden('hdn_shared_user_id_list', '');
    echo $this->hidden('hdn_shared_ou_id_list', '');
    ?>
    <!-- Toolbar -->
    <h2 class="module_title">Cập nhật hồ sơ lưu</h2>
    <!-- /Toolbar -->
    <fieldset>
        <legend>Thông tin hồ sơ</legend>
        <!-- Cot tuong minh -->
        <div class="Row">
            <div class="left-Col">Mã hồ sơ</div>
            <div class="right-Col">
                <?php echo $this->textbox('txt_code', $v_code, 'text', 'Mã hồ sơ', 0);?>
            </div>
        </div>
        <div class="Row">
            <div class="left-Col">Tiêu đề</div>
            <div class="right-Col">
                <?php echo $this->textbox('txt_name', $v_name, 'text', 'Tiêu đề', 0); ?>
            </div>
        </div>
        <div class="Row">
            <div class="left-Col">Chú giải</div>
            <div class="right-Col">
                <textarea name="txt_description" id="txt_description"
                    class="inputbox" style="width:40%"
                    onKeyDown="return handleEnter(this, event);"
                    data-allownull="yes" data-validate="text"
                    data-name="Thông tin mô tả"
                    data-xml="no" data-doc="no"
                    rows="4"
                    ><?php echo $v_description; ?></textarea>
            </div>
        </div>
        <div class="Row">
            <div class="left-Col">
                <?php echo _LANG_STATUS_LABEL; ?>
            </div>
            <div class="right-Col">
                <input type="checkbox" name="chk_status" value="1"
                    id="chk_status"
                    <?php echo ($v_status > 0) ? ' checked' : ''; ?>
                /><label for="chk_status"><?php echo _LANG_ACTIVE_STATUS_LABEL; ?></label><br/>
                <input type="checkbox" name="chk_is_closed" value="1"
                    id="chk_is_closed"
                    <?php echo ($v_is_closed > 0) ? ' checked' : ''; ?>
                /><label for="chk_is_closed">Đóng hồ sơ</label>
            </div>
        </div>
        <!-- Button -->
        <div class="button-area">
            <a onclick="btn_update_folder_onclick();" class="easyui-linkbutton" iconCls="icon-save"><?php echo _LANG_UPDATE_BUTTON; ?></a>
            <a onclick="btn_back_onclick();" class="easyui-linkbutton" iconCls="icon-cancel"><?php echo _LANG_GO_BACK_BUTTON; ?></a>
        </div>
    </fieldset>

    <div id="doc_list">
        <fieldset>
            <legend>Danh sách văn bản thuộc hồ sơ</legend>
            <div>
            <div id="doc_of_folder" class="edit-box">
                <table width="100%" class="adminlist" cellspacing="0" border="1" id="tbl_doc_in_folder">
                    <colgroup>
                        <col width="5%" />
                        <col width="15%" />
                        <col width="70%" />
                        <col width="10%" />
                    </colgroup>
                    <tr>
                        <th>#</th>
                        <th>Số và ký hiệu</th>
                        <th>Trích yêu</th>
                        <th>Ngày văn bản</th>
                    </tr>
                    <tr><th></th><th></th><th></th><th></th></tr>
                        <?php for ($i=0; $i < sizeof($arr_all_doc_by_folder); $i++):
                            $v_doc_id = $arr_all_doc_by_folder[$i]['PK_DOC'];
                            $v_doc_code = $arr_all_doc_by_folder[$i]['DOC_SO_KY_HIEU'];
                            $v_doc_description = $arr_all_doc_by_folder[$i]['DOC_TRICH_YEU'];
                            $v_create_by_user_id = $arr_all_doc_by_folder[$i]['FK_USER'];
                            $v_create_by_user_name = $arr_all_doc_by_folder[$i]['C_USER_NAME'];
                            $v_doc_date = $arr_all_doc_by_folder[$i]['DOC_NGAY_VAN_BAN'];

                            $v_xml_attach_file_list = $arr_all_doc_by_folder[$i]['DOC_ATTACH_FILE_LIST'];


                            $v_str_disabled = ($v_create_by_user_id != session::get('user_id')) ? ' disabled="disabled"' : '';
                            ?>
                            <tr id="tr_doc_<?php echo $v_doc_id;?>" class="row<?php echo ($i%2);?>">
                                <td class="center">
                                    <input type="checkbox" name="chk_doc" value="<?php echo $v_doc_id;?>" id="doc_<?php echo $v_doc_id;?>" <?php echo $v_str_disabled;?>/>
                                </td>
                                <td>
                                    <label for="doc_<?php echo $v_doc_id;?>"><?php echo $v_doc_code;?></label>
                                </td>
                                <td>
                                    <label for="doc_<?php echo $v_doc_id;?>"><?php echo $v_doc_description;?></label>
                                    <?php if ($v_xml_attach_file_list != NULL)
                                    {
                                        $f_dom = simplexml_load_string('<root>' . $v_xml_attach_file_list . '</root>');
                                        $rows = $f_dom->xpath('//row');

                                        $html = '<br/><img src="' . SITE_ROOT . 'public/images/attach.png" /><u>File đính kèm:</u>';

                                        foreach ($rows as $row)
                                        {
                                            $v_attach_file_name = $row->attributes()->C_FILE_NAME;
                                            $v_file_path    = CONST_SITE_DOC_FILE_UPLOAD_DIR . $v_attach_file_name;
                                            $v_file_extension = array_pop(explode('.', $v_attach_file_name));
                                            $html .= '<br/>&nbsp;&nbsp;<img src="' . SITE_ROOT . 'public/images/' . $v_file_extension . '-icon.png" width="16px" height="16px"/>';
                                            $html .=  '<a href="' . $v_file_path .'" target="_blank">' .$v_attach_file_name . '</a>&nbsp;';
                                        }
                                        echo $html;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <label for="doc_<?php echo $v_doc_id;?>"><?php echo $v_doc_date;?></label>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </div>
                <div id="doc_of_folder_action">
                        <a onclick="dsp_all_doc_to_add()" class="easyui-linkbutton" iconCls="icon-add">Thêm văn bản</a><br/>
                        <a onclick="remove_doc_from_folder()" class="easyui-linkbutton" iconCls="icon-remove">Bỏ văn bản</a>
                </div>
            </div>
            <div class="clear">&nbsp;</div>
        </fieldset>
    </div>

    <?php if ( ($v_create_user_id == session::get('user_id')) ): ?>
        <div id="user_list">
            <fieldset>
                <legend>Danh sách NSD được chia sẻ</legend>
                <div>
                <div id="user_of_folder" class="edit-box">
                    <table width="100%" class="adminlist" cellspacing="0" border="1" id="tbl_shared_user">
                        <colgroup>
                            <col width="5%" />
                            <col width="95%" />
                        </colgroup>
                        <tr>
                            <th>#</th>
                            <th>Tên NSD</th>
                        </tr>
                            <tr><th></th><th></th></tr>
                            <?php for ($i=0; $i < sizeof($arr_all_shared_user); $i++):
                                $v_user_id      = $arr_all_shared_user[$i]['PK_USER'];
                                $v_user_name    = $arr_all_shared_user[$i]['C_NAME'];
                                ?>
                                <tr id="tr_user_<?php echo $v_user_id;?>" class="row<?php echo ($i%2);?>">
                                    <td class="center">
                                        <input type="checkbox" name="chk_user" value="<?php echo $v_user_id;?>" id="user_<?php echo $v_user_id;?>" />
                                    </td>
                                    <td>
                                    	<img src="<?php echo SITE_ROOT; ?>public/images/icon-16-user.png" border="0" align="absmiddle" />
                                        <label for="user_<?php echo $v_user_id;?>"><?php echo $v_user_name;?></label>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </table>
                    </div>
                    <div id="user_of_folder_action">
                        <a onclick="dsp_all_user_to_add()" class="easyui-linkbutton" iconCls="icon-add">Thêm NSD</a><br/>
                        <a onclick="remove_shared_user()" class="easyui-linkbutton" iconCls="icon-remove">Bỏ NSD</a>
                    </div>
                </div>
                <div class="clear">&nbsp;</div>
            </fieldset>
        </div>

        <div id="ou_list">
            <fieldset>
                <legend>Danh sách phòng ban được chia sẻ</legend>
                <div>
                <div id="doc_of_folder" class="edit-box">
                    <table width="100%" class="adminlist" cellspacing="0" border="1" id="tbl_shared_ou">
                        <colgroup>
                            <col width="5%" />
                            <col width="95%" />
                        </colgroup>
                        <tr>
                            <th>#</th>
                            <th>Tên phòng ban</th>
                        </tr>
                            <tr><th></th><th></th></tr>
                            <?php for ($i=0; $i < sizeof($arr_all_shared_ou); $i++):
                                $v_ou_id    = $arr_all_shared_ou[$i]['PK_OU'];
                                $v_ou_name  = $arr_all_shared_ou[$i]['C_NAME'];
                               
                                ?>
                                <tr id="tr_ou_<?php echo $v_ou_id;?>" class="row<?php echo ($i%2);?>">
                                    <td class="center">
                                        <input type="checkbox" name="chk_ou" value="<?php echo $v_ou_id;?>" id="ou_<?php echo $v_ou_id;?>" />
                                    </td>
                                    <td>
                                    	<img src="<?php echo SITE_ROOT;?>public/images/unit16.png" border="0" align="absmiddle" />
                                        <label for="ou_<?php echo $v_ou_id;?>"><?php echo $v_ou_name;?></label>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </table>
                    </div>
                    <div id="doc_of_folder_action">
                        <a onclick="dsp_all_ou_to_add()" class="easyui-linkbutton" iconCls="icon-add">Thêm phòng ban</a><br/>
                        <a onclick="remove_shared_ou()" class="easyui-linkbutton" iconCls="icon-remove">Bỏ phòng ban</a>
                    </div>
                </div>
                <div class="clear">&nbsp;</div>
            </fieldset>
        </div>
     <?php endif; ?>
</form>
<script>
    $(document).ready(function() {
        //Fill data
        var formHelper = new DynamicFormHelper('','',document.frmMain);
        formHelper.BindXmlData();

        <?php if ($v_folder_id < 1): ?>
            $('#txt_code').focus();
        <?php endif; ?>
    });

    function dsp_all_user_to_add()
    {
        var url = SITE_ROOT + 'cores/user/dsp_all_user_to_add/&pop_win=1';

        showPopWin(url, 450, 350, add_user);
    }
    function add_user(returnVal) {
        for (i=0; i<returnVal.length; i++)
        {
            v_user_id = returnVal[i].user_id;
            v_user_name = returnVal[i].user_name;

            //Neu user chua co trong group thi them vao
            q = '#user_' + v_user_id;
            if( $(q).length < 1 )
            {
                html = '<tr id="tr_user_' + v_user_id + '">';
                html += '<td class="center">';
                html +=     '<input type="checkbox" name="chk_user" value="' + v_user_id + '" id="user_' + v_user_id + '" />';
                html += '</td>';
                html += '<td>';
                html += '<img src="' + SITE_ROOT + 'public/images/icon-16-user.png" border="0" align="absmiddle" />';
                html += '<label for="user_' + v_user_id + '">' + v_user_name + '</label>';
                html += '</td></tr>';
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

    //OU
    function dsp_all_ou_to_add()
    {
        var url = SITE_ROOT + 'cores/user/dsp_all_ou_to_add/&pop_win=1';

        showPopWin(url, 450, 350, add_ou);
    }

    function add_ou(returnVal)
    {
        for (i=0; i<returnVal.length; i++)
        {
            v_ou_id   = returnVal[i].ou_id;
            v_ou_name = returnVal[i].ou_name;

            //Neu user chua co  thi them vao
            q = '#ou_' + v_ou_id;
            if( $(q).length < 1 )
            {
                html = '<tr id="tr_ou_' + v_ou_id + '">';
                html += '<td class="center">';
                html +=     '<input type="checkbox" name="chk_ou" value="' + v_ou_id + '" id="ou_' + v_ou_id + '" />';
                html += '</td>';
                html += '<td>';
                html += '<img src="' + SITE_ROOT + 'public/images/unit16.png" border="0" align="absmiddle" />';
                html += '<label for="ou_' + v_ou_id + '">' + v_ou_name + '</label>';
                html += '</td></tr>';
                $('#tbl_shared_ou').append(html);
            }
        }
    }

    function remove_shared_ou()
    {
        var q = "input[name='chk_ou']";
        $(q).each(function(index) {
            if ($(this).is(':checked'))
            {
                v_ou_id = $(this).val();
                s = '#tr_ou_' + v_ou_id;
                $(s).remove();
            }
        });
    }


    function btn_update_folder_onclick()
    {
        //Luu danh sach van ban
        var arr_doc_id = new Array();
        var q = "input[name='chk_doc']";
        $(q).each(function(index) {
            arr_doc_id.push($(this).val());
        });
        document.frmMain.hdn_doc_id_list.value = arr_doc_id.join();

        //Luu danh sach NSD
        var arr_user_id = new Array();
        var q = "input[name='chk_user']";
        $(q).each(function(index) {
            arr_user_id.push($(this).val());
        });
        document.frmMain.hdn_shared_user_id_list.value = arr_user_id.join();

        //Luu danh sach phong ban
        var arr_ou_id = new Array();
        var q = "input[name='chk_ou']";
        $(q).each(function(index) {
            arr_ou_id.push($(this).val());
        });
        document.frmMain.hdn_shared_ou_id_list.value = arr_ou_id.join();

        //Danh sach van ban
        var arr_doc_id = new Array();
        var q = "input[name='chk_doc']";
        $(q).each(function(index) {
            if ($(this).attr('disabled') != true)
            {
                arr_doc_id.push($(this).val());
            }
        });
        document.frmMain.hdn_doc_id_list.value = arr_doc_id.join();

        btn_update_onclick();
    }

    function dsp_all_doc_to_add()
    {
        var url = SITE_ROOT + 'edoc/doc/dsp_all_doc_to_add/&pop_win=1';
        showPopWin(url, 800, 600, add_doc);
    }

    function add_doc(returnVal)
    {
        for (i=0; i<returnVal.length; i++)
        {
            v_doc_id            = returnVal[i].doc_id;
            v_doc_code          = returnVal[i].doc_code;
            v_doc_description   = returnVal[i].doc_description;
            v_doc_date          = returnVal[i].doc_date;

            //Neu user chua co  thi them vao
            q = '#doc_' + v_doc_id;
            if( $(q).length < 1 )
            {
                html = '<tr id="tr_doc_' + v_doc_id + '">';
                html += '<td class="center">';
                html += '<input type="checkbox" name="chk_doc" value="' + v_doc_id + '" id="doc_' + v_doc_id + '" />';
                html += '</td>';
                html += '<td>';
                html += '<label for="doc_' + v_doc_id + '">' + v_doc_code + '</label>';
                html += '</td>';
                html += '<td>';
                html += '<label for="doc_' + v_doc_id + '">' + v_doc_description + '</label>';
                html += '</td>';
                html += '<td>';
                html += '<label for="doc_' + v_doc_id + '">' + v_doc_date + '</label>';
                html += '</td>';
                html += '</tr>';

                $('#tbl_doc_in_folder').append(html);
            }
        }
    }

    function remove_doc_from_folder()
    {
        var q = "input[name='chk_doc']";
        $(q).each(function(index) {
            if ($(this).is(':checked'))
            {
                s = '#tr_doc_' + $(this).val();
                $(s).remove();
            }
        });
    }

</script>
<?php $this->template->display('dsp_footer.php');
