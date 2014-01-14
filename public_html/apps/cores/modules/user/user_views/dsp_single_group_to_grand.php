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
$this->template->title = 'Phân quyền';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
//--------------------------------------------------------------

$v_group_id =  $VIEW_DATA['group_id'];
$v_group_name =  $VIEW_DATA['group_name'];

?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>dsp_single_user_to_grand"><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_item_id', $v_group_id);
    echo $this->hidden('XmlData', '"');

    echo $this->hidden('hdn_update_method', 'update_group_permit');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_single_group_to_grand');

    echo $this->hidden('pop_win', '1');
    echo $this->hidden('hdn_item_name', $v_user_name);

    echo $this->hidden('hdn_grant_function', '');

    ?>
    <!-- Toolbar -->
    <h2 class="module_title">Phân quyền cho Nhóm NSD</h2>
    <!-- /Toolbar -->

    <div class="Row">
        <div class="left-Col">Tên nhóm</div>
        <div class="right-Col"><?php echo $v_group_name;?> </div>
    </div>
    <div class="Row">
        <div class="left-Col">Ứng dụng</div>
        <div class="right-Col">
            <select name="sel_application" id="sel_application" onchange="get_application_permit(this.value)">
                <option value="-1">-- Chọn ứng dụng --</option>
                <?php echo $this->generate_select_option($VIEW_DATA['arr_all_application_option'], -1);?>
            </select>
        </div>
    </div>
    <div class="clear">&nbsp</div>
    <div id="application_permit">

    </div>
    <div class="button-area">
        <input type="button" name="btn_update_group_permit" class="button save" value="<?php echo __('update'); ?>" onclick="btn_update_group_permit_onclick(); "/>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo _LANG_CANCEL_BUTTON; ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script type="text/javascript">
    function get_application_permit(app_id)
    {
        $.ajax({url:"<?php echo SITE_ROOT;?>application/dsp_application_permit/" + app_id, success:function(result){
                $("#application_permit").html(result);

                //Danh dau cac quyen da duoc phan
                v_url =  "<?php echo SITE_ROOT;?>ou/arp_group_permit_on_application/?app_id=" + app_id + '&group_id=' + $('#hdn_item_id').val();
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

    function btn_update_group_permit_onclick()
    {
        //Lay danh sach ma function da danh dau
        var q = "input[type='checkbox']";
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

</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');