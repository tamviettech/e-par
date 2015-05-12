
<?php
defined('SERVER_ROOT') or die('No direct script');

$v_active_tab = get_post_var('hdn_active_tab',0);
?>
<h2 class="module_title">
    Chuyên mục
</h2>
<div if="right_content">
    <div id="tabs">
        <ul>
            <?php if (check_permission('XEM_DANH_SACH_CHUYEN_MUC',$this->app_name)): ?>
                <li>
                    <a href="<?php echo $this->get_controller_url() . 'dsp_all_category/' ?>" >
                        Danh sách chuyên mục
                    </a>
                </li>
            <?php endif; ?>
            <?php if (check_permission('XEM_DANH_SACH_CHUYEN_MUC_NOI_BAT',$this->app_name)): ?>
<!--                <li>
                    <a href="<?php echo $this->get_controller_url() . 'dsp_featured_category/' ?>" >
                       Chuyên mục nổi bật
                    </a>
                </li>-->
            <?php endif; ?>
        </ul>
    </div>
</div>
<script>
    $(function(){
        $('#tabs').tabs(
                    { selected: <?php echo $v_active_tab; ?>}
                    ,{select: function(){
                                        $('#tabs .ui-tabs-panel').html('<center><img src="<?php echo SITE_ROOT ?>public/images/loading.gif"/></center>');
                                        }
                    }
                );
        $('#tabs .ui-tabs-panel').css('min-height', '368px');
    });
    function reload_current_tab()
    {
      
        var tabindex = $("#tabs").tabs('option', 'selected');
        $("#tabs").tabs('load',tabindex);
    }
    
</script>
