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

$arr_all_user_to_grand      = $VIEW_DATA['arr_all_user_to_grand'];
$arr_all_group_to_grand     = $VIEW_DATA['arr_all_group_to_grand'];

$v_filter                   = $VIEW_DATA['filter'];


$v_row = 0;

?>
<form name="frmMain" id="frmMain" action="#" method="POST">
    <?php echo $this->hidden('controller', $this->get_controller_url());?>
    <!-- Toolbar -->
    <h2 class="module_title">Danh sách NSD, Nhóm NSD</h2>
    <!-- /Toolbar -->

     <!-- filter -->
    <div id="div_filter">
        <label>Lọc theo tên </label>
        <input type="text" name="txt_filter" id="txt_filter"
            value="<?php echo $v_filter;?>"
            class="inputbox" size="30" autofocus="autofocus"
            onkeypress="txt_filter_onkeypress(this.form.btn_filter,event);"
        />
        <input type="button" class="filter_button" id="btn_filter" onclick="filter_user_and_group()"
                name="btn_filter" value="<?php echo _LANG_FILTER_BUTTON;?>"
        />
    </div>

    <table width="100%" class="adminlist" cellspacing="0" border="1">
        <colgroup>
            <col width="40%" />
            <col width="30%" />
            <col width="20%" />
            <col width="10%" />
        </colgroup>
        <tr>
            <th>Tên </th>
            <th>Thuộc phòng ban</th>
            <th>Loai</th>
            <th>#</th>
        </tr>
        <!-- NSD -->
        <?php for ($i=0; $i<count($arr_all_user_to_grand); $i++): ?>
            <?php
            $v_user_id      = $arr_all_user_to_grand[$i]['PK_USER'];
            $v_user_name    = $arr_all_user_to_grand[$i]['C_NAME'];
            $v_status       = $arr_all_user_to_grand[$i]['C_STATUS'];

            $v_class = 'row' . ($v_row % 2);
            $v_row++;

            $v_url = $this->get_controller_url() . 'dsp_single_user_to_grand/' . $v_user_id
                    . '/?hdn_item_id=' . $v_user_id . '&pop_win=1&type=user&hdn_item_name=' . $v_user_name;

            $v_icon_file_name = ($v_status > 0) ? 'icon-16-user.png' : 'icon-16-user-inactive.png';
            ?>
            <tr class="<?php echo $v_class;?>">
                <td>
                    <img src="<?php echo SITE_ROOT . 'public/images/' . $v_icon_file_name ;?>" border="0" align="absmiddle"/>
                    <?php echo $v_user_name;?>
                </td>
                <td></td>
                <td>Người sử dụng</td>
                <td>
                    <a href="javascript:void(0)" onclick="grand('<?php echo $v_url;?>')">Phân quyền</a>
                </td>
            </tr>
        <?php endfor; ?>

        <!-- Group -->
        <?php for ($i=0; $i<count($arr_all_group_to_grand); $i++): ?>
            <?php
            $v_group_id         = $arr_all_group_to_grand[$i]['PK_GROUP'];
            $v_name             = $arr_all_group_to_grand[$i]['C_NAME'];

            $v_class = 'row' . ($v_row % 2);
            $v_row++;
            $v_url = $this->get_controller_url() . 'dsp_single_group_to_grand/' . $v_group_id
                    . '/?hdn_item_id=' . $v_group_id . '&pop_win=1&type=group&hdn_item_name=' . $v_name;
            ?>
            <tr class="<?php echo $v_class;?>">
                <td>
                    <img src="<?php echo SITE_ROOT . 'public/images/user-group16.png' ;?>" border="0" align="absmiddle"/>
                    <?php echo $v_name;?>
                </td>
                <td></td>
                <td>Nhóm người sử dụng</td>
                <td>
                    <a href="javascript:void(0)" onclick="grand('<?php echo $v_url;?>')">Phân quyền</a>
                </td>
            </tr>

        <?php endfor; ?>
        <?php echo $this->add_empty_rows($v_row, _CONST_DEFAULT_ROWS_PER_PAGE, 4); ?>
    </table>
</form>
<script>
    function user_pop_win(url)
    {
        showPopWin(url ,800,500, null);
    }

    function filter_user_and_group()
    {
            m = SITE_ROOT + 'ou/dsp_all_user_and_group_to_grand/';

            $.post(m, { txt_filter: $('#txt_filter').val() }, function(result) {
                $("#content_right").html(result);
            });

    }

    function grand(url)
    {
        user_pop_win(url);
    }
</script>