<?php
defined('DS') or die('no direct access');

$v_begin_date    = get_request_var('txt_begin_date', '');
$v_end_date      = get_request_var('txt_end_date', '');
$v_status        = intval(get_request_var('sel_status', -1));
$v_category_name = empty($category_name) ? ' -- ' . __('all') . ' -- ' : $category_name;
$v_init_user     = intval(get_request_var('sel_init_user', 0));
$v_category_id   = intval(get_request_var('hdn_category', ''));

$v_keyword       = get_request_var('txt_keyword', '');
$v_title         = get_request_var('txt_title');

$v_advanced_search = 'false';
if ($v_begin_date or $v_end_date or ($v_status != -1) or $v_keyword or $v_init_user)
{
    $v_advanced_search = 'true';
}

// Số tin bài hiển thị phụ
$count_article  = count($arr_all_article) ;
?>
<?php

function show_insert_delete_button($app_name)
{
    $html = '<div class="button-area">';
    if (check_permission('THEM_MOI_TIN_BAI',$app_name ))
    {
        $html .= '<input type="button" class="ButtonAdd" onClick="row_onclick(0);" value="' . __('add new') . '"></input>';
    }
    if (check_permission('XOA_TIN_BAI',$app_name))
    {
        $html .= '<input type="button" class="ButtonDelete" onClick="btn_delete_onclick(\'hdn_id_list\');" value="' . __('delete') . '"></input>';
    }
    $html .= '</div>';

    echo $html;
}
?>
<h2 class="module_title"><?php echo __('article number'); ?>: <font style="color:royalblue"><?php echo $count_article ?></font></h2>
<?php
echo $this->hidden('hdn_dsp_single_method', 'dsp_single_article');
echo $this->hidden('controller', $this->get_controller_url());
echo $this->hidden('hdn_dsp_all_method', 'dsp_all_article');
echo $this->hidden('hdn_update_method', 'update_article');
echo $this->hidden('hdn_delete_method', 'delete_article');
echo $this->hidden('hdn_item_id', '');
echo $this->hidden('XmlData', '');
?>

<form name="frmMain" id="frmMain" method="post">
    <input 
        type="hidden" name="sel_rows_per_page" id="page_size" 
        value="<?php echo get_request_var('sel_rows_per_page', CONST_DEFAULT_ROWS_PER_PAGE) ?>"
        />
    <input 
        type="hidden" name="sel_goto_page" id="page_no" 
        value="<?php echo get_request_var('sel_goto_page', 1) ?>"
        />

    <input
        type="hidden" name="hdn_category" id="hdn_category" 
        value="<?php echo $v_category_id; ?>"
        />
    <input 
        type="button" class="ButtonSearch" id="btn-advanced-search" 
        value="<?php echo __('advanced search') ?>" style="font-weight: bold;"
        />
    <table class="advanced-search">
        <colgroup>
            <col width="15%" />
            <col width="35%" />
            <col width="15%" />
            <col width="35%" />
        </colgroup>
        <tr>
            <td><?php echo __('publish time range') ?></td>
            <td>
                <?php echo __('begin date') ?>
                <input type="text" name="txt_begin_date" id="txt_begin_date" value="<?php echo $v_begin_date; ?>" 
                       class="inputbox" maxlength="255" style="width:50%" 
                       data-allownull="yes" data-validate="date" data-name="<?php echo __('begin date'); ?>" 
                       data-xml="no" data-doc="no" onclick="DoCal('txt_begin_date');"
                       />
                <img 
                    height="16" width="16" src="<?php echo SITE_ROOT ?>apps/public_service/images/calendar.png"
                    onClick="DoCal('txt_begin_date')"
                    />
            </td>
            <td><?php echo __('end date'); ?></td>
            <td>
                <input type="text" name="txt_end_date" id="txt_end_date" value="<?php echo $v_end_date; ?>" 
                       class="inputbox" maxlength="255" style="width:50%" 
                       data-allownull="yes" data-validate="date" data-name="<?php echo __('end date'); ?>" 
                       data-xml="no" data-doc="no"  onClick="DoCal('txt_end_date');"
                       /> 
                <img 
                    height="16" width="16" src="<?php echo SITE_ROOT ?>apps/public_service/images/calendar.png"
                    onClick="DoCal('txt_end_date');"
                    />
            </td>
        </tr>
        <tr>
            <td><?php echo __('status') ?></td>
            <td>
                <select name="sel_status" id="sel_status" onChange="form_auto_submit();">
                    <option value="-1"> -- <?php echo __('all') ?> -- </option>
                    <option value="0"><?php echo __('Không hiển thị') ?></option>
                    <option value="1"><?php echo __('Hiển thị') ?></option>
                </select>
                <script>$('#sel_status option[value=<?php echo $v_status ?>]').attr('selected', '1');</script>
            </td>
            <td><label for="txt_category"><?php echo __('category') ?></label></td>
            <td>
                <label for="txt_category" onClick="show_category_svc();">
                    <input type="text" name="txt_category" id="txt_category" value="<?php echo $v_category_name; ?>" 
                           class="inputbox" maxlength="255" style="width:50%" 
                           data-allownull="yes" data-validate="text" data-name="<?php echo __('category'); ?>" 
                           data-xml="no" data-doc="no" 
                           disabled
                           /> 
                    <img 
                        height="16" width="16" 
                        src="<?php echo SITE_ROOT ?>apps/public_service/images/folder.png"
                        />
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('init user') ?>
            </td>
            <td colspan="3">
                <select name="sel_init_user" id="sel_init_user" onChange="form_auto_submit();">
                    <option value="0"> -- <?php echo __('all') ?> -- </option>
                    <?php foreach ($arr_all_user as $item): ?>
                        <option value="<?php echo $item['PK_USER']; ?>">
                            <?php echo $item['C_NAME'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <script>$('#sel_init_user').val(<?php echo $v_init_user ?>);</script>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo __('keywords'); ?>
            </td>
            <td colspan="3">
                <input type="text" name="txt_keyword" id="txt_keyword" value="<?php echo $v_keyword; ?>" 
                       class="inputbox" maxlength="255" style="width:50%" 
                       data-allownull="yes" data-validate="text" data-name="<?php echo __('keywords'); ?>" 
                       data-xml="no" data-doc="no" 
                       /> 
                <input type="submit" class="ButtonSearch" value="<?php echo __('search') ?>"/>
            </td>
        </tr>
    </table>
    <div class="clear"></div>
    <div style="float:right" class="search">

        <label for="txt_category_2" onClick="show_category_svc();">
            <?php echo __('category'); ?>
            <input 
                type="text" name="txt_category_2" id="txt_category_2" value="<?php echo $v_category_name; ?>" 
                class="inputbox" maxlength="255" size="30"
                data-allownull="yes" data-validate="text" data-name="<?php echo __('category'); ?>" 
                data-xml="no" data-doc="no" 
                disabled
                /> &nbsp;
            <img 
                height="16" width="16" 
                src="<?php echo SITE_ROOT ?>apps/public_service/images/folder.png"
                />
        </label>
        <label><?php echo __('title') ?></label>
        <input 
            type="text" name="txt_title" id="txt_title"
            value="<?php echo $v_title ?>" size="50"
            />
        <input type="submit" class="ButtonSearch" value="<?php echo __('search') ?>"/>

    </div>
    <div class="clear"></div>
    <?php show_insert_delete_button($this->app_name); ?>
    
    
    
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', '0');
    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_article');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_article');
    echo $this->hidden('hdn_update_method', 'update_article');
    echo $this->hidden('hdn_delete_method', 'delete_article');
    echo $this->hidden('hdn_delete_method', 'delete_article');
    ?>
    <?php
    $n          = count($arr_all_article);
    $i          = 0;
    ?>
    <?php
    $arr_status = array(
        0 => __('Không hiển thị'),
        1 => __('Hiển thị'),
    );
    ?>
    <table width="100%" class="adminlist" cellspacing="0" border="1">
        <colgroup>
            <col width="5%">
            <col width="34%">
            <col width="18%">
            <col width="18%">
            <col width="15%">
        </colgroup>
        <tr>
            <th>
                <input type="checkbox" name="chk_check_all" id="chk_all"/>
            </th>
            <th><?php echo __('title') ?></th>
            <th><?php echo __('init user') ?></th>
            <th><?php echo __('begin date') ?></th>
            <th><?php echo __('status') ?></th>
        </tr>
        <?php for ($i = 0; $i < $n; $i++): ?>
            <?php
            $item = $arr_all_article[$i];
            if(sizeof($item) > 0):
            ?>
            <tr class="row<?php echo $i % 2 ?>">
                <td class="Center">
                    <input 
                        type="checkbox" name="chk[]" class="chk" id="item_<?php echo $i ?>"
                        value="<?php echo $item['PK_ARTICLE'] ?>"
                        />
                </td>
                <td>
                    <?php if (check_permission('SUA_TIN_BAI',$this->app_name)): ?>
                        <a href="javascript:;" onClick="row_onclick(<?php echo $item['PK_ARTICLE'] ?>)">
                            <?php echo $item['C_TITLE']; ?>
                        </a>
                    <?php else: ?>
                        <?php echo $item['C_TITLE'] ?>
                    <?php endif; ?>
                </td>
                <td class="Center"><?php echo $item['C_INIT_USER_NAME'] ?></td>
                <td class="Center"><?php echo $item['C_BEGIN_DATE'] ?></td>
                <td class="Center status_<?php echo $item['C_STATUS'] ?>"><b><?php  echo $arr_status[$item['C_STATUS']] ?></b></td>
            </tr>
            <?php endif; ?>
        <?php endfor; ?>
        <?php $n = get_request_var('sel_rows_per_page', CONST_DEFAULT_ROWS_PER_PAGE); ?>
        <?php for ($i; $i < $n; $i++): ?>
            <tr class="row<?php echo $i % 2 ?>">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php endfor; ?>
    </table>
    <div class="button-area" style="float:right;padding-top: 0;">
        <?php echo $this->paging2($arr_all_article); ?>
    </div>
</form>
<div class="clear"></div>
<?php show_insert_delete_button($this->app_name); ?>


<script>
    var is_advanced_search = <?php echo $v_advanced_search ?>;
    toggle_checkbox('#chk_all', '.chk');
    $(document).ready(function(){
        if(!is_advanced_search)
        {
            $('.advanced-search').toggle();
            $('.advanced-search input,.advanced-search select').attr('disabled', 1);
        }
        else
        {
            $('.search').toggle();
            $('.search input,.search select').attr('disabled', 1);
        }
        $('#btn-advanced-search').click(function(){
            $('.advanced-search').toggle('slow');
            $('.search').toggle('slow');
            var disabled_advanced = $('.advanced-search input,.advanced-search select').attr('disabled');
            $('.advanced-search input,.advanced-search select').attr('disabled', !disabled_advanced);
            $('.search input,.search select').attr('disabled', disabled_advanced);
            $('[name="txt_category"],[name="txt_category_2"]').attr('disabled', 1);
        });

    });
    function show_category_svc()
    {
        $url = "<?php echo $this->get_controller_url('category', 'public_service'); ?>dsp_all_category_svc/";
        $url += '&enable_all=1&single_pick=1';
        showPopWin($url, 800, 600, function(obj){
            if(obj.length == 0)
            {
                $('#hdn_category').val(0);
                $('#frmMain').submit();
            }
            
            $('#txt_category').val(obj[0]['name']);
            $('#txt_category_2').val(obj[0]['name']);
            $('#hdn_category').val(obj[0]['id']);
            $('#frmMain').submit();
        });
    }
    function form_auto_submit()
    {
        $('#sel_go_to_page').val(1);
        $('#frmMain').submit();
    }
    
    window.row_onclick = function(article_id){
        var $controller = $('#controller').val();
        var $dsp_single = $('#hdn_dsp_single_method').val();
        var $url = $controller + $dsp_single + '/' + article_id;
        
        $('#frmMain').attr('action', $url);
        $('#frmMain').submit();
    }
    
    window.btn_delete_onclick = function(hdn_id_list){
        var $url = $('#controller').val() + $('#hdn_delete_method').val();
        if($('.chk:checked:not(:disabled)').length == 0)
        {
            alert("<?php echo __('you must choose atleast one object') ?>");
            return;
        }
        if(!confirm("<?php echo __('are you sure to delete all selected object?') ?>"))
        {
            return;
        }
        $.ajax({
            type: 'post',
            url: $url,
            data: $('#frmMain').serialize(),
            success: function(obj){
                window.location.reload();
            }
        });
    }
    
    $('select[name=sel_rows_per_page]').change(function(){
        $('#page_size').val($(this).val());
        $('#frmMain').submit();
    });
    $('select[name=sel_goto_page]').change(function(){
        $('#page_no').val($(this).val());
        $('#frmMain').submit();
    });
   
    $('#txt_title').keyup(function(e){
        if(e.keyCode == 13)
        {
            $('#txt_title').parents('form').submit();
        }
    });
    
   
    
</script>
