<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//display header
$this->template->title = $this->title = __('update user');

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header' . $v_pop_win . '.php');
//------------------------------------------------------------------------------
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
    $v_idc_id      = $arr_single_user['C_IDC_ID'];
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
    $v_idc_id    = '';
}
?>
<div class="container-fluid">
    <form name="frmMain" method="post" id="frmMain" action="#" class="form-horizontal"><?php
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
        <script>
            $(function() {
                $( "#tabs_user" ).tabs();
            });
        </script>

        <div class="tab-widget">
            <ul class="nav nav-tabs" id="myTab1">
                <li class="active"><a href="#user_info"><i class="icon-user"></i>Thông tin cá nhân NSD</a></li>
                <li><a href="#user_group"><i class="icon-group "></i>Thuộc các nhóm</a></li>
                <li><a href="#user_permit"><i class="icon-unlock"></i>Phân quyền cho NSD</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="user_info">
                    <!-- Cot tuong minh -->
                    <table width="100%" class="table" cellpadding="0" cellspacing="0">
                        <colgroup>
                            <col width="30%" />
                            <col width="70%" />
                        </colgroup>
                        <tr>
                            <td>
                                Tên đăng nhập <span class="required">(*)</span>
                            </td>
                            <td>
                                <?php if ($v_user_id < 1): ?>
                                    <input type="text" name="txt_login_name" id="txt_login_name" value=""
                                        class="input" maxlength="50" style="width:50%"
                                        onKeyDown="return handleEnter(this, event);"
                                        data-allownull="no" data-validate="text"
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
                            <td>Tên người sử dụng <span class="required">(*)</span></td>
                            <td>
                                <input type="text" name="txt_name" id="txt_name" value="<?php echo $v_name;?>"
                                    class="input" maxlength="50" style="width:50%"
                                    onKeyDown="return handleEnter(this, event);"
                                    data-allownull="no" data-validate="text"
                                    data-name="Tên người sử dụng"
                                    data-xml="no" data-doc="no"
                                />
                            </td>
                        </tr>
                        <?php if ($v_user_id < 1): ?>
                            <tr>
                                <td>Mật khẩu <span class="required">(*)</span></td>
                                <td>
                                    <input type="password" name="txt_password" id="txt_password" value="" pattern=".{5,20}"  
                                        class="input" maxlength="50" style="width:50%"
                                        onKeyDown="return handleEnter(this, event);"
                                        data-allownull="no" data-validate="text"
                                        data-name="Mật khẩu"
                                        data-xml="no" data-doc="no"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>Xác nhận mật khẩu <span class="required">(*)</span></td>
                                <td>
                                    <input type="password" name="txt_confirm_password" id="txt_confirm_password" value="" pattern=".{5,20}"  
                                        class="input" maxlength="50" style="width:50%"
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
                                        class="input" maxlength="50" style="width:50%"
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
                                        class="input" maxlength="50" style="width:50%"
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
                                <span>/<?php echo $name;?></span>
                                <?php endforeach; ?>
                                <?php echo $this->hidden('hdn_parent_ou_id', $id);?>
                                <br/>
                                <div class="input-append">
                                    <input type="text" id="txt_ou_patch" name="txt_ou_patch" value="<?php echo $v_ou_patch;?>"  disabled class="uneditable-input span7"/>
                                    <button type="button" onclick="dsp_all_ou_to_add()" class="btn btn-file" title="Chọn đơn vị">
                                        <i class="icon-folder-open"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Chức danh:
                            </td>
                            <td>
                                <input type="text" name="txt_job_title" value="<?php echo $v_job_title; ?>" id="txt_job_title"
                                        class="input" maxlength="200" style="width:50%"
                                        onKeyDown="return handleEnter(this, event);"
                                        data-allownull="yes" data-validate="text"
                                        data-name="Chức danh"
                                        data-xml="no" data-doc="no"
                                />
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo __('order'); ?><span class="required">(*)</span></td>
                            <td>
                                <input type="text" name="txt_order" value="<?php echo $v_order; ?>" id="txt_order"
                                class="input" size="4" maxlength="3"
                                data-allownull="no" data-validate="number"
                                data-name="<?php echo __('order'); ?>"
                                data-xml="no" data-doc="no"
                                />
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo __('status'); ?></td>
                            <td>
                                <label for="chk_status">
                                 <input type="checkbox" name="chk_status" value="1"
                                    <?php echo ($v_status > 0) ? ' checked' : ''; ?>
                                    id="chk_status"
                                /><?php echo __('active status'); ?>
                                </label>
                            </td>
                        </tr>
                        <?php 
                        if ( ! defined('CONST_IDC_INTERGRATED'))
                        {
                            define('CONST_IDC_INTERGRATED', 0);
                        }
                        if (CONST_IDC_INTERGRATED > 0): ?>
                            <tr>
                                <td>IDC ID </span></td>
                                <td>
                                     <input type="text" name="txt_idc_id" value="<?php echo $v_idc_id; ?>" id="txt_idc_id"
                                            class="input" size="6" maxlength="6"
                                            data-allownull="yes" data-validate="number"
                                            data-name="IDC ID"
                                            data-xml="no" data-doc="no"
                                    />
                                </td>
                            </tr>
                        <?php endif; ?> 
                    </table>
                </div>
                <div class="tab-pane" id="user_group">
                    <div id="group_of_user" class="span8">
                        <table width="100%" class="table" cellspacing="0" border="1" id="tbl_user_in_group">
                            <colgroup>
                                <col width="5%" />
                                <col width="95%" />
                            </colgroup>
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên nhóm</th>
                            </tr></thead>
                            <?php foreach ($arr_all_group_by_user as $v_group_id => $v_group_name): ?>
                                <tr id="tr_<?php echo $v_group_id;?>">
                                    <td class="center">
                                        <input type="checkbox" name="chk_group" value="<?php echo $v_group_id;?>" id="chk_group_<?php echo $v_group_id;?>" />
                                    </td>
                                    <td>
                                        <label for="chk_group_<?php echo $v_group_id;?>">
                                            <img src="<?php echo $this->template_directory . 'images/user-group16.png' ;?>" border="0" align="absmiddle" />
                                            <?php echo $v_group_name;?>
                                        </label>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <div id="group_of_users_action" class="span4 form-actions">
                        <button type="button" name="btn_add_group" class="btn btn-primary" onclick="dsp_all_group_to_add();"><i class="icon-plus icon-group"></i>Thêm nhóm</button>
                        <button type="button" name="btn_remove_group" class="btn" onclick="remove_group_from_user();"><i class="icon-trash"></i>Bỏ nhóm</button>
                    </div>
                    <div class="clear">&nbsp;</div>
                </div>
                <div class="tab-pane" id="user_permit">
                        Chọn ứng dụng
                        <select name="sel_application" onchange="get_application_permit(this.value)">
                            <option value="-1">&nbsp;</option>
                            <?php echo $this->generate_select_option($VIEW_DATA['arr_all_application_option']);?>
                        </select>
                    <div id="application_permit"></div>
                </div>
            </div>
        </div><!-- /.tab-widget-->
        
        <!-- Button -->
        <div class="form-actions">
            <button type="button" name="btn_update_user" class="btn btn-primary" onclick="btn_update_user_onclick();"><i class="icon-save"></i><?php echo __('update');?></button>
            <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
            <button type="button" class="btn" onclick="<?php echo $v_back_action;?>"><i class="icon-reply"></i><?php echo __('cancel');?></button>
        </div>
    </form>
</div>
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
                html += '<td><label for="chk_group_' + v_group_id + '">';
                html += '<img src="<?php echo $this->template_directory;?>images/user-group16.png" border="0" align="absmiddle" />';
                html += v_group_name + '</label>';
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
        //var 
        if((document.frmMain.txt_password.value).length < 5 && (document.frmMain.txt_password.value).length != 0)
        {
            alert('Độ dài mật khẩu phải ít nhất 5 ký tự!');
            document.frmMain.txt_password.focus();
            return false;
        }   
        
        //kiem tra xac nhan mat khau
        if( document.frmMain.txt_password.value != document.frmMain.txt_confirm_password.value)
        {
            alert('Xác nhận mật khẩu không đúng!');
            document.frmMain.txt_password.focus();
            return false;
        }
        
        //ten dang nhap khong chua ky tu dac biet
        if (document.frmMain.hdn_item_id.value == '0')
        {
            //var v_login_name = $("#txt_login_name").val();
            //var patt = '/^([a-z]+)([a-z0-9.@]{4,30})$/g';
            if (!(login_name_validate($("#txt_login_name").val())))
            {
                alert('Tên đăng nhập không hợp lệ!');
                document.frmMain.txt_login_name.focus();
                return false;
            }
        }
            
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
<?php require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer' . $v_pop_win . '.php');