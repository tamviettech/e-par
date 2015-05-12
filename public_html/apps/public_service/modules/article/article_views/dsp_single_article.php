
<?php
defined('DS') or die('no direct access');

$this->title        = __('article');
$this->dsp_side_bar = false;
?>
<script src="<?php echo SITE_ROOT; ?>public/tinymce/script/tiny_mce.js"></script>
<!--jwplayer-->
<script type="text/javascript" src="<?php echo SITE_ROOT ?>public/jwplayer/jwplayer.js" ></script>
<script type="text/javascript">jwplayer.key="Qa3rOiiEqCKn+SICIA6EkRGZKHyS0etl1/ioTQ==";</script>

<h2 class="module_title"><?php echo __('article') ?></h2>
<div id="tabs">
    <ul>
        <li>
            <a href="<?php echo $this->get_controller_url() . 'dsp_general_info/' . $article_id; ?>"
               id="dsp_general_info"
            >
                   <?php echo __('general info') ?>
            </a>
        </li>
        <?php if ($article_id): ?>
            <li>
                <a href="<?php echo $this->get_controller_url() . 'dsp_preview/' . $article_id; ?>">
                    <?php echo __('preview') ?>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->get_controller_url() . 'dsp_edit_article/' . $article_id; ?>">
                    <?php echo __('edit article'); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->get_controller_url() . 'dsp_all_version/' . $article_id ?>">
                    <?php echo __('version'); ?>
                </a>
            </li>

        <?php endif; ?>
    </ul>
</div>
<div id="msg-box" style="">

</div>

<form name="frm_filter" id="frm_filter" action="" method="post">
    <?php
    foreach ($_POST as $key => $val)
    {
        echo $this->hidden($key, $val);
    }
    ?>
</form>

<script>
    SITE_ROOT = "<?php echo SITE_ROOT ?>";
    tinyMCE_init(); 
   
    $('#tabs').tabs({
        load: function(){
            tinyMCE.execCommand('mceAddControl', false, 'txt_content');
            tinyMCE.execCommand('mceAddControl', false, 'txt_summary');
            is_changed = false;
        },
        select: function(){
            tinyMCE.execCommand('mceRemoveControl', false, 'txt_content');
            tinyMCE.execCommand('mceRemoveControl', false, 'txt_summary');
            $('#tabs .ui-tabs-panel').html('<center><img src="<?php echo SITE_ROOT ?>public/images/loading.gif"/></center>');
        }
    });
    $('#tabs .ui-tabs-panel').css('min-height', '368px');
    
    $(document).ready(function(){
        reset_div_msg();

        $('.TopMenuAdmin *').attr('onclick','');
        $('.TopMenuAdmin *').addClass('disabled');
    });
    
    function reset_div_msg(){
        $html = '<p>';
        $html += '<img height="16" width="16" src="' + '<?php echo SITE_ROOT ?>public/images/loading.gif' + '"/>';
        $html += '<?php echo __('processing') ?>';
        $html += '</p>';
        $('#msg-box').html($html);
        $('#msg-box').removeAttr('class');
    }

    function add_btnOK_to_div_msg()
    {
        $html = '</br><input type="button" onClick="div_msg_hide();" value="OK" style="width:30px;"/>';
        $('#msg-box').append($html);
    }
    
    function div_msg_hide()
    {
        $('#msg-box').fadeOut('fast', reset_div_msg);
    }
    window.btn_back_onclick = function()
    {
        $('#frm_filter').attr('action', "<?php echo $this->get_controller_url(); ?>")
        $('#frm_filter').submit();
    }

</script>
