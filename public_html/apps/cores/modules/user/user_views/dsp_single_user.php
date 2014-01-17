<?php
/**


This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//display header
$this->template->title = __('update user');

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
//------------------------------------------------------------------------------

/*
$arr_single_user            = $VIEW_DATA['arr_single_user'];
$arr_parent_ou_path         = $VIEW_DATA['arr_parent_ou_path'];
$arr_all_group_by_user      = $VIEW_DATA['arr_all_group_by_user'];
*/

if (isset($arr_single_user['PK_USER']))
{
    $v_user_id     = $arr_single_user['PK_USER'];
    $v_ou_id       = $arr_single_user['FK_OU'];
    $v_name        = $arr_single_user['C_NAME'];
    $v_login_name  = $arr_single_user['C_LOGIN_NAME'];
    $v_order       = $arr_single_user['C_ORDER'];
    $v_status      = $arr_single_user['C_STATUS'];
    $v_xml_data    = $arr_single_user['C_XML_DATA'];
    $v_job_title   = $arr_single_user['C_JOB_TITLE'];
}
else
{
    $v_user_id     = 0;
    $v_ou_id       = 0;
    $v_code        = '';
    $v_name        = '';
    $v_order       = $arr_single_user['C_ORDER'];
    $v_status      = 1;
    $v_xml_data    = '';
    $v_job_title    = '';
}
?>
<form name="frmMain" method="post" id="frmMain" action="#"><?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_user');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_user');
    echo $this->hidden('hdn_update_method', 'update_user');
    echo $this->hidden('hdn_delete_method', 'delete_user');

    echo $this->hidden('hdn_item_id', $v_user_id);
    echo $this->hidden('XmlData', $v_xml_data);

    echo $this->hidden('pop_win', $v_pop_win);

    echo $this->hidden('hdn_deleted_doc_file_id_list', '');

    echo $this->hidden('hdn_group_id_list', '');

    echo $this->hidden('hdn_grant_function', '');
    ?>
    <!-- Toolbar -->
    <!-- <h2 class="module_title">Cập nhật NSD</h2>-->
    <!-- /Toolbar -->

    <script>
        $(function() {
            $( "#tabs_user" ).tabs();
        });
    </script>

    <div id="tabs_user">
        <ul>
            <li><a href="#user_info">Thông tin cá nhân NSD</a></li>
            <li><a href="#user_group">Thuộc các nhóm</a></li>
            <li><a href="#user_permit">Phân quyền cho NSD</a></li>
        </ul>
        <div id="user_info">
            <!-- Cot tuong minh -->
            <table width="100%" class="main_table" cellpadding="0" cellspacing="0">
                <colgroup>
                    <col width="25%" />
                    <col width="75%" />
                </colgroup>
                <tr>
                    <td>
                        Tên đăng nhập <label class="required">(*)</label>
                    </td>
                    <td>
                        <?php if ($v_user_id < 1): ?>
                            <input type="text" name="txt_login_name" id="txt_login_name" value=""
                                class="inputbox" maxlength="50" style="width:50%"
                                onKeyDown="return handleEnter(this, event);"
                                data-allownull="no" data-validate="loginname"
                                data-name="Tên đăng nhập"
                                data-xml="no" data-doc="no"
                                autofocus="autofocus"
                            />
                        <?php else: ?>
                            <strong><?php echo $v_login_name;?></strong>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Tên người sử dụng <label class="required">(*)</label></td>
                    <td>
                        <input type="text" name="txt_name" id="txt_name" value="<?php echo $v_name;?>"
                            class="inputbox" maxlength="50" style="width:50%"
                            onKeyDown="return handleEnter(this, event);"
                            data-allownull="no" data-validate="username"
                            data-name="Tên người sử dụng"
                            data-xml="no" data-doc="no"
                        />
                    </td>
                </tr>
                <?php if ($v_user_id < 1): ?>
                    <tr>
                        <td>Mật khẩu <label class="required">(*)</label></td>
                        <td>
                            <input type="password" name="txt_password" id="txt_password" value=""
                                class="inputbox" maxlength="50" style="width:50%"
                                onKeyDown="return handleEnter(this, event);"
                                data-allownull="no" data-validate="text"
                                data-name="Mật khẩu"
                                data-xml="no" data-doc="no"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td>Xác nhận mật khẩu <label class="required">(*)</label></td>
                        <td>
                            <input type="password" name="txt_confirm_password" id="txt_confirm_password" value=""
                                class="inputbox" maxlength="50" style="width:50%"
                                onKeyDown="return handleEnter(this, event);"
                                data-allownull="no" data-validate="text"
                                data-name="Xác nhận mật khẩu"
                                data-xml="no" data-doc="no"
                            />
                        </td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td>Mật khẩu mới</td>
                        <td>
                            <input type="password" name="txt_password" id="txt_password" value=""
                                class="inputbox" maxlength="50" style="width:50%"
                                onKeyDown="return handleEnter(this, event);"
                                data-allownull="yes" data-validate="text"
                                data-name="Mật khẩu mới"
                                data-xml="no" data-doc="no"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td>Xác nhận mật khẩu mới</td>
                        <td>
                            <input type="password" name="txt_confirm_password" id="txt_confirm_password" value=""
                                class="inputbox" maxlength="50" style="width:50%"
                                onKeyDown="return handleEnter(this, event);"
                                data-allownull="yes" data-validate="text"
                                data-name="Xác nhận mật khẩu mới"
                                data-xml="no" data-doc="no"
                            />
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>Thuộc đơn vị</td>
                    <td>
                        <?php foreach ($arr_parent_ou_path as $id => $name): ?>
                        <label>/<?php echo $name;?></label>
                        <?php endforeach; ?>
                        <?php echo $this->hidden('hdn_parent_ou_id', $id);?>
                        <br/>
                        <input type ="text" id="txt_ou_patch" name="txt_ou_patch" value="<?php echo $v_ou_patch;?>" style="width:50%" disabled/>
                        <input type="button" class="ButtonAddOu" onclick="dsp_all_ou_to_add()">
                    </td>
                </tr>
                <tr>
                    <td>
                        Chức danh:
                    </td>
                    <td>
                        <input type="text" name="txt_job_title" value="<?php echo $v_job_title; ?>" id="txt_job_title"
                                class="inputbox" maxlength="200" style="width:50%"
                                onKeyDown="return handleEnter(this, event);"
                                data-allownull="yes" data-validate="text"
                                data-name="Chức danh"
                                data-xml="no" data-doc="no"
                        />
                    </td>
                </tr>
                <tr>
                    <td><?php echo __('order'); ?><label class="required">(*)</label></td>
                    <td>
                        <input type="text" name="txt_order" value="<?php echo $v_order; ?>" id="txt_order"
                        class="inputbox" size="4" maxlength="3"
                        data-allownull="no" data-validate="number"
                        data-name="<?php echo __('order'); ?>"
                        data-xml="no" data-doc="no"
                        />
                    </td>
                </tr>
                <tr>
                    <td><?php echo __('status'); ?></td>
                    <td>
                         <input type="checkbox" name="chk_status" value="1"
                            <?php echo ($v_status > 0) ? ' checked' : ''; ?>
                            id="chk_status"
                        /><label for="chk_status"><?php echo __('active status'); ?></label><br/>
                    </td>
                </tr>
                <?php if (defined('CONST_IDC_INTERGRATED') && CONST_IDC_INTERGRATED > 0): ?>
                    <tr>
                        <td>IDC ID</td>
                        <td>
                             <input type="text" name="txt_idc_id" value="<?php echo $v_idc_id; ?>" id="txt_idc_id"
                                    class="inputbox" size="6" maxlength="6"
                                    data-allownull="no" data-validate="number"
                                    data-name="IDC ID"
                                    data-xml="no" data-doc="no"
                            />
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
        <div id="user_group">
            <div id="group_of_user" class="edit-box">
                <table width="100%" class="adminlist" cellspacing="0" border="1" id="tbl_user_in_group">
                    <colgroup>
                        <col width="5%" />
                        <col width="95%" />
                    </colgroup>
                    <tr>
                        <th>#</th>
                        <th>Tên nhóm</th>
                    </tr>
                    <?php foreach ($arr_all_group_by_user as $v_group_id => $v_group_name): ?>
                        <tr id="tr_<?php echo $v_group_id;?>">
                            <td class="center">
                                <input type="checkbox" name="chk_group" value="<?php echo $v_group_id;?>" id="chk_group_<?php echo $v_group_id;?>" />
                            </td>
                            <td>
                                <img src="<?php echo $this->template_directory . 'images/user-group16.png' ;?>" border="0" align="absmiddle" />
                                <label for="chk_group_<?php echo $v_group_id;?>"><?php echo $v_group_name;?></label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div id="group_of_users_action">
                <input type="button" name="btn_add_group" value="Thêm nhóm" class="button add_group" onclick="dsp_all_group_to_add()"/><br/>
                <input type="button" name="btn_remove_group" value="Bỏ nhóm" class="button delete_group" onclick="remove_group_from_user()"/>
            </div>
            <div class="clear">&nbsp;</div>
        </div>
        <div id="user_permit">
            <label>Chọn ứng dụng</label>
            <select name="sel_application" onchange="get_application_permit(this.value)">
                <option value="-1">&nbsp;</option>
                <?php echo $this->generate_select_option($VIEW_DATA['arr_all_application_option']);?>
            </select>
            <div id="application_permit"></div>
        </div>
    </div>
    <!-- XML data -->
    <?php
    //$v_xml_file_name = 'xml_user_edit.xml';
    //$this->load_xml($v_xml_file_name);
   // echo $this->render_form_display_single();
    ?>

    <!-- Button -->
    <div class="button-area">
        <input type="button" name="btn_update_user" class="button save" value="<?php echo __('update'); ?>" onclick="btn_update_user_onclick(); "/>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('cancel'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function() {
        //Fill data
        var formHelper = new DynamicFormHelper('','',document.frmMain);
        formHelper.BindXmlData();
    });

    function dsp_all_group_to_add()
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_all_group_to_add/&pop_win=1';

        showPopWin(url, 450, 350, add_group);
    }
    function add_group(returnVal)
    {
        json_data = JSON.stringify(returnVal);

        for (i=0; i<returnVal.length; i++)
        {
            v_group_id = returnVal[i].group_id;
            v_group_name = returnVal[i].group_name;

            //Neu user chua co  thi them vao
            q = '#group_' + v_group_id;
            if( $(q).length < 1 )
            {
                html = '<tr id="tr_' + v_group_id + '">';
                html += '<td class="center">';
                html +=     '<input type="checkbox" name="chk_group" value="' + v_group_id + '" id="chk_group_' + v_group_id + '" />';
                html += '</td>';
                html += '<td>';
                html += '<img src="<?php echo $this->template_directory;?>images/user-group16.png" border="0" align="absmiddle" />';
                html += '<label for="chk_group_' + v_group_id + '">' + v_group_name + '</label>';
                html += '</td></tr>';
                $('#tbl_user_in_group').append(html);
            }
        }
    }
    function remove_group_from_user()
    {
        var q = "input[name='chk_group']";
        $(q).each(function(index) {
            if ($(this).is(':checked'))
            {
                s = '#tr_' + $(this).val();
                $(s).remove();
            }
        });
    }

    function btn_update_user_onclick()
    {
        <?php if ($v_user_id < 1): ?>
            //Kiem tra xac nhan mat khau
            if ($("#txt_password").val() != $("#txt_confirm_password").val())
            {
                alert('Mật khẩu xác nhận chưa đúng!');
                return;
            }
        <?php endif; ?>
        //Lay danh sach nhom
        var arr_group_id = new Array();
        var q = "input[name='chk_group']";
        $(q).each(function(index) {
            arr_group_id.push($(this).val());
        });

        document.frmMain.hdn_group_id_list.value = arr_group_id.join();

        //Lay danh sach ma function da danh dau
        var q = "#application_permit input[type='checkbox']";
        var arr_checked_function = new Array();
        $(q).each(function(index) {
            if ($(this).is(':checked') && parseBoolean($(this).attr('data-xml')))
            {
                arr_checked_function.push($(this).attr('id'));
            }
        });

        $('#hdn_grant_function').val(arr_checked_function.join());
        

        btn_update_onclick();
    }


    function get_application_permit(app_id)
    {
        $.ajax({url:"<?php echo SITE_ROOT;?>cores/application/dsp_application_permit/" + app_id, success:function(result){
                $("#application_permit").html(result);

                //Danh dau cac quyen da duoc phan
                v_url =  "<?php echo $this->get_controller_url();?>arp_user_permit_on_application/?app_id=" + app_id + '&user_id=' + $('#hdn_item_id').val();
                $.getJSON(v_url, function(current_permit) {
                    for (i=0; i<current_permit.length; i++)
                    {
                        q = '#' + current_permit[i];
                        $(q).attr('checked', true);
                    }
                });
            }
        });
    }
    function dsp_all_ou_to_add()
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_all_ou_to_add/&pop_win=1';
        
        showPopWin(url, 450, 350, add_ou);
    }
    function add_ou(returnVal)
    {
        var ou_id=returnVal[0].ou_id;
        var ou_patch = returnVal[0].ou_patch;
       
        $('#txt_ou_patch').attr('value',ou_patch);
        $('#hdn_parent_ou_id').val(ou_id);
       
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');