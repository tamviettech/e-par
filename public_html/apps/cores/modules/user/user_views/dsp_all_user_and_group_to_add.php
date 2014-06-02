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
$this->template->title = $this->title = 'Chọn NSD';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header' . $v_pop_win . '.php');

$arr_all_user_to_add    = $VIEW_DATA['arr_all_user_to_add'];
$arr_all_group_to_add   = $VIEW_DATA['arr_all_group_to_add'];
?>
<div class="container-fluid">
    <form name="frmMain" method="post" id="frmMain" action="#"  class="form-horizontal">
        <div class="button-area">
            <label for="rad_user_type_user" style="display: inline">
                <input type="radio" name="rad_user_type" value="user" id="rad_user_type_user" onclick="ufilter(this.value)" />
                <img src="<?php echo $this->template_directory;?>images/icon-16-user.png" border="0" align="top" />
                Người SD
            </label>

            <label for="rad_user_type_group" style="display: inline">
                <input type="radio" name="rad_user_type" value="group" id="rad_user_type_group" onclick="ufilter(this.value)" />
                <img src="<?php echo $this->template_directory;?>images/user-group16.png" border="0" align="top" />
                Nhóm SD
            </label>
            
            <label for="rad_user_type_all"  style="display: inline">
                <input type="radio" name="rad_user_type" value="all" id="rad_user_type_all" onclick="ufilter(this.value)" checked="checked"/>
                Tất cả
            </label>
        </div>
        <table class="adminlist table table-bordered table-striped" width="100%" border="1">
            <colgroup>
                    <col width="5%" />
                    <col width="45%" />
                    <col width="35%" />
                    <col width="15%" />
                </colgroup>
            <tr>
                <th><input type="checkbox" name="chk_check_all" onclick="check_all_user(this)" /></th>
                <th>Tên</th>
                <th>Chức danh</th>
                <th>Loại</th>
            </tr>
        </table>
        <div style="height:200px;overflow: scroll">
            <table class="adminlist table table-bordered table-striped" width="100%" border="1">
               <colgroup>
                    <col width="5%" />
                    <col width="45%" />
                    <col width="35%" />
                    <col width="15%" />
                </colgroup>

                <?php for ($i=0; $i<count($arr_all_user_to_add); $i++): ?>
                    <?php
                    $v_user_id     = $arr_all_user_to_add[$i]['PK_USER'];
                    $v_user_name   = $arr_all_user_to_add[$i]['C_NAME'];
                    $v_status      = $arr_all_user_to_add[$i]['C_STATUS'];
                    $v_code        = $arr_all_user_to_add[$i]['C_CODE'];
                    $v_job_title   = $arr_all_user_to_add[$i]['C_JOB_TITLE'];

                    $v_icon_file_name = ($v_status > 0) ? 'icon-16-user.png' : 'icon-16-user-inactive.png';
                    $v_class = 'row' . strval($i % 2);
                    ?>
                    <tr class="<?php echo $v_class;?> user">
                        <td class="center">
                            <input type="checkbox" name="chk_user"
                                   value="<?php echo $v_user_id;?>"
                                   id="user_<?php echo $v_user_id;?>"
                                   data-user_code="<?php echo $v_code;?>"
                                   data-user_name="<?php echo $v_user_name;?>"
                                   data-user_status="<?php echo $v_status;?>"
                                   data-user_type="user"
                                   data-job_title="<?php echo $v_job_title;?>"
                            />
                        </td>
                        <td>
                            <label for="user_<?php echo $v_user_id;?>">
                                <img src="<?php echo $this->template_directory . 'images/' . $v_icon_file_name ;?>" border="0" align="absmiddle" />
                                <?php echo $v_user_name;?>
                            </label>
                        </td>
                        <td><?php echo $v_job_title;?></td>
                        <td>NSD</td>
                    </tr>
                <?php endfor; ?>
                <?php for ($i=0; $i<count($arr_all_group_to_add); $i++): ?>
                    <?php
                    $v_group_id     = $arr_all_group_to_add[$i]['PK_GROUP'];
                    $v_group_name   = $arr_all_group_to_add[$i]['C_NAME'];
                    $v_group_code   = $arr_all_group_to_add[$i]['C_CODE'];

                    $v_class = 'row' . strval($i % 2);
                    ?>
                    <tr class="<?php echo $v_class;?> group">
                        <td class="center">
                            <input type="checkbox" name="chk_user"
                                   value="<?php echo $v_group_id;?>"
                                   id="group_<?php echo $v_group_id;?>"
                                   data-user_code="<?php echo $v_group_code;?>"
                                   data-user_name="<?php echo $v_group_name;?>"
                                   data-user_status="1"
                                   data-user_type="group"
                                   data-job_title=""
                            />
                        </td>
                        <td>
                            <label for="group_<?php echo $v_group_id;?>">
                                <img src="<?php echo $this->template_directory;?>images/user-group16.png" border="0" align="absmiddle" />
                                <?php echo $v_group_name;?>
                            </label>
                        </td>
                        <td>&nbsp;</td>
                        <td>Nhóm</td>
                    </tr>
                <?php endfor; ?>
            </table>
        </div>
        <!-- Button -->

        <div class="button-area">
            <button class="btn btn-success" onclick="get_selected_user();" name="update" type="button">
                <i class="icon-ok-sign"></i>
                Chọn
            </button>   
            <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
            <button type="button" name="cancel" class="btn btn-danger" onclick="<?php echo $v_back_action;?>" >
                <i class="icon-remove"></i>
                <?php echo __('cancel'); ?>
            </button>
        </div>
    </form>
</div>
<script>
    <?php if (isset($_REQUEST['d']) && $_REQUEST['d']=='user'):?>
    ufilter('user');
    <?php endif;?>

    function check_all_user(obj)
    {
        chk_selector = "input[name='chk_user']";
        $(chk_selector).attr('checked', false);

        if ($(obj).is(':checked'))
        {
            q = "input[name='chk_user'][data-user_type='user']";
            $(q).each(function(index) {
                $(this).attr('checked', true);
            });
        }
    }
    function hide_all()
    {
        chk_selector = "input[name='chk_user']";
        $(chk_selector).attr('checked', false);

        tr_selector = ".user, .group";
        $(tr_selector).each(function(index) {
            $(this).hide();
        });
    }
    function show_all()
    {
        q = ".user, .group";
        $(q).each(function(index) {
            $(this).show();
        });
    }
    function ufilter(type)
    {
        if (type == 'all')
        {
            show_all();
        }
        else
        {
            hide_all();
            q = '.' + type;
            $(q).each(function(index) {
                $(this).show();
            });
        }
    }
    function get_selected_user()
    {
        var jsonObj = []; //declare array

        q = "input[name='chk_user']";
        $(q).each(function(index) {
            if ($(this).is(':checked'))
            {
                v_user_id = $(this).val();
                v_user_name = $(this).attr('data-user_name');
                v_user_status = $(this).attr('data-user_status');
                v_user_type = $(this).attr('data-user_type');
                v_code = $(this).attr('data-user_code');
                v_job_title = $(this).attr('data-job_title');

                jsonObj.push({'user_id': v_user_id
                                , 'user_name': v_user_name
                                , 'user_status': v_user_status
                                , 'user_type': v_user_type
                                , 'user_code': v_code
                                , 'job_title': v_job_title
                });
            }
        });
        returnVal = jsonObj;
        window.parent.hidePopWin(true);
    }
</script>
<?php require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer' . $v_pop_win . '.php');