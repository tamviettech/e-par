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
$this->template->title = 'Chọn NSD';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$arr_all_user_to_add = $VIEW_DATA['arr_all_user_to_add'];
?>
<form name="frmMain" method="post" id="frmMain" action="#">
    <table width="100%" class="adminlist" cellspacing="0" border="1">
        <colgroup>
            <col width="5%" />
            <col width="95%" />
        </colgroup>
        <tr>
            <th>#</th>
            <th>Tên NSD</th>
        </tr>
    </table>
    <div style="height:200px;overflow: scroll">
        <table width="100%" class="adminlist" cellspacing="0" border="1">
                <colgroup>
                <col width="5%" />
                <col width="95%" />
            </colgroup>

            <?php for ($i=0; $i<count($arr_all_user_to_add); $i++): ?>
                <?php
                $v_user_id     = $arr_all_user_to_add[$i]['PK_USER'];
                $v_user_name   = $arr_all_user_to_add[$i]['C_NAME'];
                $v_status      = $arr_all_user_to_add[$i]['C_STATUS'];
                $v_job_title   = $arr_all_user_to_add[$i]['C_JOB_TITLE'];

                $v_icon_file_name = ($v_status > 0) ? 'icon-16-user.png' : 'icon-16-user-inactive.png';
                $v_class = 'row' . strval($i % 2);
                ?>
                <tr class="<?php echo $v_class;?>">
                    <td class="center">
                        <input type="checkbox" name="chk_user"
                               value="<?php echo $v_user_id;?>"
                               id="user_<?php echo $v_user_id;?>"
                               data-user_name="<?php echo $v_user_name;?>"
                               data-user_status="<?php echo $v_status;?>"
                        />
                    </td>
                    <td>
                        <img src="<?php echo $this->template_directory . 'images/' . $v_icon_file_name ;?>" border="0" align="absmiddle" />
                        <label for="user_<?php echo $v_user_id;?>"><?php echo $v_user_name;?> (<?php echo $v_job_title;?>)</label>
                    </td>
                </tr>
            <?php endfor; ?>
            <?php //echo $this->add_empty_rows($i+1, _CONST_DEFAULT_ROWS_PER_PAGE, 2); ?>
        </table>
    </div>
    <!-- Button -->
    <div class="button-area">
        <input type="button" name="update" class="button add" value="<?php echo __('update'); ?>" onclick="get_selected_user();"/>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo _LANG_CANCEL_BUTTON; ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script>
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

                jsonObj.push({'user_id': v_user_id, 'user_name': v_user_name, 'user_status': v_user_status});
            }
        });

        returnVal = jsonObj;
        window.parent.hidePopWin(true);
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');