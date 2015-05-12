<?php
/**
 * @copyright	Copyright (C) 2012 Tam Viet Tech. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Ngo Duc Lien <liennd@gmail.com>
 * @author		Luong Thanh Binh <ltbinh@gmail.com>
 */
?>
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//display header
$this->template->title = 'Phân quyền';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
//--------------------------------------------------------------

$v_user_id =  $VIEW_DATA['user_id'];
$v_user_name =  $VIEW_DATA['user_name'];

?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>dsp_single_user_to_grand"><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_item_id', $v_user_id);
    echo $this->hidden('XmlData', '"');

    echo $this->hidden('hdn_update_method', 'update_user_permit');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_single_user_to_grand');

    echo $this->hidden('pop_win', '1');
    echo $this->hidden('hdn_item_name', $v_user_name);

    echo $this->hidden('hdn_grant_function', '');

    ?>
    <!-- Toolbar -->
    <h2 class="module_title">Phân quyền cho NSD</h2>
    <!-- /Toolbar -->

    <div class="Row">
        <div class="left-Col">Người sử dụng</div>
        <div class="right-Col"><?php echo $v_user_name;?> </div>
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
    <div id="aaa"></div>
    <div id="application_permit">

    </div>
    <div class="button-area">
        <input type="button" name="btn_update_user_permit" class="button save" value="<?php echo __('update'); ?>" onclick="btn_update_user_permit_onclick(); "/>
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
                v_url =  "<?php echo SITE_ROOT;?>ou/arp_user_permit_on_application/?app_id=" + app_id + '&user_id=' + $('#hdn_item_id').val();
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

    function btn_update_user_permit_onclick()
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