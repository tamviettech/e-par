<?php
defined('DS') or die('no direct access');
?>

<form name="frmVersion" id="frmVersion">
    <table class="adminlist" cellspacing="0" border="1" width="100%">
        <colgroup>
            <col width="10%">
            <col width="20%">
            <col width="15%">
            <col width="25%">
            <col width="15%">
            <col width="15%">
        </colgroup>
        <tr>
            <th><?php echo __('version') ?></th>
            <th><?php echo __('date') ?></th>
            <th><?php echo __('action') ?></th>
            <th><?php echo __('user') ?></th>
            <th><?php echo __('status') ?></th>
            <th><?php echo __('details') ?></th>
        </tr>
        <?php
        $n = count($arr_all_version);
        $i = 0;
        ?>
        <?php for ($i = $n - 1; $i >= 0; $i--): ?>
            <?php
            $item         = $arr_all_version[$i];
            $v_version_id = $item['id'];
            $v_date       = new DateTime($item['date']);
            $v_user_name  = $item['user_name'];
            $arr_status   = array(
                0              => __('Hủy đăng tin bài'),
                1              => __('Đăng tin bài'),
            );
            $v_status      = $arr_status[$item['status']];
            $v_action      = __($item['action']);
            $v_has_content = $item['has_content'];
            ?>
            <tr class="row<?php echo $i % 2; ?>">
                <td class="Center"><?php echo $v_version_id ?></td>
                <td class="Center"><?php echo $v_date->format('d/m/Y H:i') ?></td>
                <td class="Center"><?php echo $v_action ?></td>
                <td><?php echo $v_user_name ?></td>
                <td class="Center"><?php echo $v_status ?></td>
                <td class="Center">
                    <?php if ($v_has_content): ?>
                        <a href="javascript:;" onClick="version_onclick(<?php echo $v_version_id ?>);">
                            <?php echo __('details') ?>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endfor; ?>
        <?php $n = get_request_var('sel_rows_per_page', CONST_DEFAULT_ROWS_PER_PAGE); ?>
        <?php for ($i; $i < $n; $i++): ?>
            <tr class="row<?php echo $i % 2 ?>">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php endfor; ?>
    </table>
</form>
<div class="button-area">
    <input type="button" class="ButtonBack" value="<?php echo __('goback to list'); ?>"
           onClick="btn_back_onclick();"/>
</div>

<script>
    function version_onclick($version_id)
    {
        $url = "<?php echo $this->get_controller_url() . 'dsp_single_version/' . "&article=$v_id" ?>";
        $url += "&version=" +$version_id;
        showPopWin($url, 800, 600, null);
    }
</script>