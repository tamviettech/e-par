<?php
defined('SERVER_ROOT') or die;

/* @var $this \edoc_View */
$this->template->title = 'Nhật ký xử lý';
$this->template->display('dsp_header_pop_win.php');
$dom_processing        = simplexml_load_string($arr_single_doc['C_XML_PROCESSING']);
if (!$dom_processing)
{
    die('Không có hồ sơ này hoặc nhật ký bị lỗi');
}
?>
<table class="adminlist" style="width: 100%">
    <colgroup>
        <col width="5%">
        <col width="20%">
        <col width="55%">
        <col width="20%">
    </colgroup>
    <tr>
        <th>STT</th>
        <th>Ngày thực hiện</th>
        <th>Công việc</th>
        <th>Cán bộ thực hiện</th> 
    </tr>
    <?php $i = 0; ?>
    <?php foreach ($dom_processing->xpath('//step') as $step): $i++ ?>
        <tr class="row<?php echo $i % 2 ?>">
            <td><?php echo $i ?></td>
            <Td><?php echo date_create($step->datetime)->format('d-m-Y H:i') ?></td>
            <td><?php echo $step->action_text ?></td>
            <td>
                <?php
                foreach ($step->children() as $child):
                    if (strpos($child->getName(), 'user_name') !== false):
                        echo $child;
                        break;
                    endif;
                endforeach;
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php for ($i; $i < 10; $i++): ?>
        <tr class="row<?php echo $i % 2 ?>">
            <?php echo str_repeat('<td>&nbsp;</td>', 4) ?>
        </tr>
    <?php endfor; ?>
</table>
<div class="button-area">
    <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="window.parent.hidePopWin(false)">
        Đóng cửa sổ
    </a>
</div>
<?php
$this->template->display('dsp_footer_pop_win.php');