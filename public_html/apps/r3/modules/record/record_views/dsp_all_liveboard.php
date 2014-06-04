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
/* @var $this \View */
//header
$this->template->title = 'Theo dõi trực tuyến';
$this->template->display('dsp_header.php');
?>
<style>
    body > div.layout > div.main-wrapper > div.row-fluid > div > div{display: none}
</style>
<div class="tab-widget" >
    <ul class="nav nav-tabs" id="myTab1" >
        <li class="active"><a href="#supplement_0">Biểu đồ tiến độ xử lý hồ sơ</a></li>
        <li><a href="#supplement_1">Bảng theo dõi trực tuyến </a></li>
      
    </ul>
    <div class="tab-content">
        <div id="supplement_0" class="tab-pane active">
            <iframe name="ifr_supplement_0" id="ifr_supplement_0"
                    src="<?php echo SITE_ROOT?>r3/chart"
                    width="100%" style="min-height:700px;overflow-y: scroll;border:0;background-color: white"></iframe>
        </div>
        <div id="supplement_1" class="tab-pane">
            <form name="frmMain" id="frmMain" action="" method="POST">
                <iframe src="<?php echo SITE_ROOT?>r3/liveboard" style="width:100%;height:500px; border:0; background-color: white;overflow: scroll;"></iframe>
                <a href="<?php echo SITE_ROOT?>r3/liveboard" target="blank"><label>Xem bảng tổng hợp đầy đủ</label></a>
            </form>
        </div>
        
    </div>
</div>
<div class="clear">&nbsp;</div>

<?php
$this->template->display('dsp_footer.php');