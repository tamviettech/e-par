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