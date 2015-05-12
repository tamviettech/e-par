<?php
defined('DS') or die('no direct access');
/*captcha google
 * <?php echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY); ?>
            <input type="button" class="ButtonAccept" onClick="this.form.submit();" value="<?php echo __('confirm') ?>"/>
            <input type="button" class="ButtonCancel" onClick="window.parent.close_widget_poll_model();" value="<?php echo __('cancel') ?>"/>
 */
?>
<html>
    <head></head>
    <body>
        <style>
            /*captcha*/
            .captcha-content
            {
                margin: 0 auto;
                width: 300px;
                padding: 5px;
                background: #830300;
                border-radius: 8px;
            }
            .captcha-content-code
            {
                background-color: #FFDC73;
                border-radius: 5px;
                width: 100%;
            }
            /*end*/
        </style>
        <form action="#" method="POST">
            <?php session::init();?>
            <div style="width: 100%;">
                <div class="captcha-content">
                    <!--image show-->
                    <div class="Row">
                        <img id="siimage" width="300px" height="57px" 
                         src="<?php echo CONST_SITE_THEME_ROOT.'captcha/securimage_show.php'?>" 
                         alt="CAPTCHA Image"/>
                    </div>
                    <div class="clear" style ="height: 10px">&nbsp;</div>
                    <div class="Row captcha-content-code">
                        <!--captchacode-->
                        <input type="textbox" name="txt_captcha_code" id="txt_captcha_code" size="30" 
                              data-allownull="no" data-validate="text"
                                   data-name="<?php echo 'mã xác nhận'; ?>"/>
                        <!--button refresh-->
                        <a tabindex="-1" style="border-style: none;" href="#" title="Refresh Image" 
                                onclick="document.getElementById('siimage').src = '<?php echo CONST_SITE_THEME_ROOT?>captcha/securimage_show.php?sid=' + Math.random(); this.blur(); return false">
                            <img width="22px" height="22px" 
                                src="<?php echo CONST_SITE_THEME_ROOT?>images/refresh.png" alt="Reload Image"
                                onclick="this.blur()" align="bottom" border="0">
                        </a>
                        <!--button accept and close-->
                        <input type="button" class="ButtonAccept" onClick="this.form.submit();" value="<?php echo __('confirm') ?>"/>
                        <input type="button" class="ButtonCancel" onClick="window.parent.close_widget_poll_model();" value="<?php echo __('cancel') ?>"/>
                    </div>
                </div>
            </div>
        </form>
    </body>
</html>
