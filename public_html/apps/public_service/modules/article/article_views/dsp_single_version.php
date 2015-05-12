<?php
defined('DS') or die('no direct access');

$v_date                = new DateTime(strval($dom_single_version->date));
$v_date                = $v_date->format('d/m/Y H:i');
$this->template->title = $v_date;
$this->template->display('dsp_header_pop_win.php');
?>
<div class="article-view" style="height: 500px;overflow-y: scroll;">
    <h3><?php echo strval($dom_single_version->title); ?></h3>
    <div class="summary-container"><?php echo $dom_single_version->summary ?></div>
    <?php echo $dom_single_version->content ?>
</div>
<div class="button-area">
    <input 
        type="button" class="ButtonAccept" value="<?php echo __('restore') ?>" 
        onclick="buttonAccept_onClick();"
        />
    <input 
        type="button" class="ButtonBack" value="<?php echo __('goback') ?>" 
        onclick="javascript:window.parent.hidePopWin(false);"
        />
</div>


<script>
    function buttonAccept_onClick()
    {
        if(confirm("<?php echo __('restore') . '?' ?>"))
        {
            var $url = "<?php echo $this->get_controller_url() . 'restore_version/' ?>";       
            $.ajax({
                type: 'post',
                url: $url,
                data: {
                    article_id: <?php echo $article_id ?>
                    ,version_id: <?php echo $version_id ?>
                },
                success: function(json){
                    window.parent.location.reload();
                }
            }); 
        }
    }

</script>
<?php $this->template->display('dsp_footer_pop_win.php'); ?>