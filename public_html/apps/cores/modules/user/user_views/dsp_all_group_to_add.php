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
$this->template->title = 'Chọn nhóm';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$arr_all_group_to_add = $VIEW_DATA['arr_all_group_to_add'];
?>
<form name="frmMain" method="post" id="frmMain" action="#">
    <table width="100%" class="adminlist" cellspacing="0" border="1">
                <colgroup>
                <col width="5%" />
                <col width="95%" />
            </colgroup>

            <tr>
                <th>#</th>
                <th>Tên nhóm</th>
            </tr>
    </table>
    <div style="height:200px;overflow: scroll">
        <table width="100%" class="adminlist" cellspacing="0" border="1">
                <colgroup>
                <col width="5%" />
                <col width="95%" />
            </colgroup>

            <?php for ($i=0; $i<count($arr_all_group_to_add); $i++): ?>
                <?php
                $v_group_id     = $arr_all_group_to_add[$i]['PK_GROUP'];
                $v_group_name   = $arr_all_group_to_add[$i]['C_NAME'];

                $v_class = 'row' . strval($i % 2);
                ?>
                <tr class="<?php echo $v_class;?>">
                    <td class="center">
                        <input type="checkbox" name="chk_group"
                               value="<?php echo $v_group_id;?>"
                               id="group_<?php echo $v_group_id;?>"
                               data-group_name="<?php echo $v_group_name;?>"
                        />
                    </td>
                    <td>
                        <img src="<?php echo $this->template_directory;?>images/user-group16.png" border="0" align="absmiddle" />
                        <label for="group_<?php echo $v_group_id;?>"><?php echo $v_group_name;?></label>
                    </td>
                </tr>
            <?php endfor; ?>
            <?php //echo $this->add_empty_rows($i+1, _CONST_DEFAULT_ROWS_PER_PAGE, 2); ?>
        </table>
    </div>
    <!-- Button -->
    <div class="button-area">
        <input type="button" name="update" class="button add" value="<?php echo __('update'); ?>" onclick="get_selected_group();"/>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('cancel'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script>
    function get_selected_group()
    {
        var jsonObj = []; //declare array

        q = "input[name='chk_group']";
        $(q).each(function(index) {
            if ($(this).is(':checked'))
            {
                v_group_id = $(this).val();
                v_group_name = $(this).attr('data-group_name');

                jsonObj.push({'group_id': v_group_id, 'group_name': v_group_name});
            }
        });

        returnVal = jsonObj;
        window.parent.hidePopWin(true);
    }
</script>

<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');