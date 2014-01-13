<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

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

<?php
defined('SERVER_ROOT') or die();
$this->template->display('dsp_header_pop_win.php');

$arr_tables = array(
    'dsp_liveboard'             => 'Bảng theo dõi tình hình giải quyết thủ tục hành chính',
    'dsp_liveboard_huyen'       => 'Bảng theo dõi tình hình giải quyết thủ tục hành chính tiếp nhận tại huyện',
    'dsp_liveboard_xa'          => 'Bảng theo dõi tình hình giải quyết thủ tục hành chính tiếp nhận tại xã (tổng hợp)',
    'dsp_tthc'                  => 'Danh mục thủ tục hành chính'
);
?>
<h3 class="page-title">Danh sách bảng theo dõi trực tuyến</h3>
<div style="width:100%;margin:0 auto">
    <table class="adminlist" width="100%">
        <colgroup>
            <col width="10%">
            <col width="90%">
        </colgroup>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên bảng</th>
            </tr>
        </thead>
        <tbody>
            <?php $index      = 0; ?>
            <?php foreach ($arr_tables as $action => $label): ?>
                <tr>
                    <td class="center"><?php echo ++$index ?></td>
                    <td>
                        <a href="<?php echo $this->get_controller_url() . $action ?>" target="_blank">
                            <?php echo $label ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            
            <tr>
                <td colspan="2"><b>Tại các xã</b></td>
            </tr>
            <?php for ($i=0; $i<sizeof($arr_all_village); $i++): ?>
                <tr>
                    <td class="center"><?php echo $i+1;?></td>
                    <td>
                        <a href="<?php echo $this->get_controller_url();?>dsp_liveboard_xa/<?php echo $arr_all_village[$i]['PK_OU'];?>/?v=<?php echo $arr_all_village[$i]['C_NAME']?>" target="_blank">
                            <?php echo $arr_all_village[$i]['C_NAME']?>
                        </a>
                    </td>
                </tr>
            <?php endfor;?>
        </tbody>
    </table>
</div>

<?php
$this->template->display('dsp_footer_pop_win.php');
