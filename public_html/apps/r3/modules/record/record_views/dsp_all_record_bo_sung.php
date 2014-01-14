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

<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

//header
$this->template->title = 'Bổ sung hồ sơ';
$this->template->display('dsp_header.php');
?>

<!--Tab -->
<div id="tabs_statistics">
    <ul>
        <li><a href="#supplement_0">Chưa thông báo cho công dân <span class="count" id="supplement_count_0">(0)</span></a></li>
        <li><a href="#supplement_1">Đã thông báo cho công dân, chưa nhận bổ sung <span class="count" id="supplement_count_1">(0)</span></a></li>
        <li><a href="#supplement_2">Bàn giao hồ sơ bổ sung <span class="count" id="supplement_count_2">(0)</span></a></li>
    </ul>
    <div id="supplement_0" class="supplement-div">
        <iframe name="ifr_supplement_0" id="ifr_supplement_0"
                src="<?php echo $this->get_controller_url(); ?>dsp_supplement_record_0"
                width="100%" style="min-height:700px;overflow-y: scroll;border:0;background-color: #FFF"></iframe>
    </div>
    <div id="supplement_1">
        <iframe name="ifr_supplement_1" id="ifr_supplement_1"
                src="<?php echo $this->get_controller_url(); ?>dsp_supplement_record_1"
                width="100%" style="min-height:700px;overflow-y: scroll;border:0;background-color: #FFF""></iframe>
    </div>
    <div id="supplement_2">
        <iframe name="ifr_supplement_2" id="ifr_supplement_2"
                src="<?php echo $this->get_controller_url(); ?>dsp_supplement_record_2"
                width="100%" style="min-height:700px;overflow-y: scroll;border:0;background-color: #FFF""></iframe>
    </div>
</div>
<div class="clear">&nbsp;</div>
<script>
    $(document).ready(function() {
        $("#tabs_statistics").tabs();
    });

    function loadframe(status)
    {
        eval('var obj = document.getElementById("ifr_supplement_' + status + '");');
        if (obj.src == 'about:blank')
        {
            obj.src = '<?php echo $this->get_controller_url(); ?>dsp_supplement_record_' + status;
        }
    }
</script>
<?php
$this->template->display('dsp_footer.php');