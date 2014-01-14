<?php
/**
Copyright (C) 2012 Tam Viet Tech.

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
$this->template->title = 'Chọn đơn vị';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$arr_all_ou_to_add = $VIEW_DATA['arr_all_ou'];
?>
<form name="frmMain" method="post" id="frmMain" action="#">
    <table width="100%" class="adminlist" cellspacing="0" border="1">
                <colgroup>
                <col width="95%" />
            </colgroup>
            <tr>
                <th>Tên đơn vị</th>
            </tr>
    </table>
    <div style="height:200px;overflow: scroll">
        <table width="100%" class="adminlist" cellspacing="0" border="1">
                <colgroup>
                <col width="100%" />
            </colgroup>

            <?php for ($i=0; $i<count($arr_all_ou_to_add); $i++): ?>
                <?php
                    $v_ou_id        = $arr_all_ou_to_add[$i]['PK_OU'];
                    $v_ou_name      = $arr_all_ou_to_add[$i]['C_NAME'];
                    $v_ou_level     = strlen($arr_all_ou_to_add[$i]['C_INTERNAL_ORDER'])/3-1;
                    $v_ou_patch     = $v_ou_name;
                    $v_ou_parent    = $arr_all_ou_to_add[$i]['FK_OU'];
                    for($j=0;$j<$v_ou_level;$j++)
                    {
                        for($n=0;$n<count($arr_all_ou_to_add);$n++)
                        {
                            if($v_ou_parent == $arr_all_ou_to_add[$n]['PK_OU'])
                            {
                                $v_ou_parent = $arr_all_ou_to_add[$n]['FK_OU'];
                                $v_ou_patch="/".$arr_all_ou_to_add[$n]['C_NAME'].'/'.$v_ou_patch;
                                break;
                            }
                        }
                    }
                ?>
                <tr class="<?php echo $v_class;?>">
                    <td>
                        
                        <?php 
                            for($j=0;$j<$v_ou_level;$j++)
                            {
                                echo " -- ";
                            }
                        ?>
                        <a href="javascript:void(0)" data-ou_patch="<?php echo $v_ou_patch;?>"
                           data-ou_id="<?php echo $v_ou_id;?>" 
                           onclick="get_selected_ou(this)">
                            <?php echo $v_ou_name;?>
                        </a>
                    </td>
                </tr>
            <?php endfor; ?>
        </table>
    </div>
    <!-- Button -->
    <div class="button-area">
        <input type="button" name="update" class="ButtonAccept" value="<?php echo __('update'); ?>" onclick="get_selected_group();"/>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="ButtonCancel" value="<?php echo __('cancel'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script>
    function get_selected_ou(ou)
    {
        var jsonObj = []; //declare array
        var ou_id=$(ou).attr('data-ou_id');
        var ou_patch=$(ou).attr('data-ou_patch');
        //alert(ou_patch);return;
        jsonObj.push({'ou_id': ou_id, 'ou_patch': ou_patch});

        returnVal = jsonObj;
        window.parent.hidePopWin(true);
    }
</script>

<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');