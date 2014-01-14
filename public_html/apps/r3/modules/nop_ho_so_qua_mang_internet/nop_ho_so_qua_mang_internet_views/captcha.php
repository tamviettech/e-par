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

<?php
defined('SERVER_ROOT') or die();
?>
<form name="frmMain" id="frmMain" action="" method="POST">
    <div class="panel_color">Mã xác nhận</div>
    <div>
        <?php echo recaptcha_get_html(_CONST_RECAPCHA_PUBLIC_KEY); ?>
    </div>
    <div class="button-area" id="btnContinue">
        <input type="button" class="button document_next" value="Tiếp tục" onclick="this.form.submit()">
    </div>
</form>