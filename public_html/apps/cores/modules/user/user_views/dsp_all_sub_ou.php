<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
//header
$this->active_menu =  'quan_tri_he_thong';
$this->template->title =  $this->title = $this->title = 'Quản trị NSD';
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header.php');
?>
<div class="container-fluid">
    <div class="row-fluid ">
        <ul class="breadcrumb">
            <li><a href="<?php echo SITE_ROOT;?>" class="icon-home"></a><span class="divider "><i class="icon-angle-right"></i></span></li>
            <li class="active">Quản trị hệ thống<span class="divider"><i class="icon-angle-right"></i></span></li>
            <li class="active">Người sử dụng</li>
        </ul>
    </div>
    <form name="frmMain" id="frmMain" action="" method="POST">
        <?php
        echo $this->hidden('controller',$this->get_controller_url());
        echo $this->hidden('hdn_item_id','0');
        echo $this->hidden('hdn_item_id_list','');
    
        echo $this->hidden('hdn_dsp_single_method','dsp_single_ou');
        echo $this->hidden('hdn_dsp_all_method','dsp_all_ou');
        echo $this->hidden('hdn_update_method','update_ou');
        echo $this->hidden('hdn_delete_method','delete_ou');
    
        $v_row = 0;
        ?>
        <h5 class="module_title">
            <i class="icon-folder-close"></i> Đơn vị: 
            <?php foreach ($arr_ou_path as $id => $name): ?>
                /<a href="<?php echo $this->get_controller_url() . 'dsp_all_sub_ou/' . $id;?>"><?php echo $name;?></a>
            <?php endforeach; ?>
            <?php
            echo $this->hidden('hdn_current_ou_id', $id);
            echo $this->hidden('hdn_current_ou_name', $name);
            ?>
        </h5>
        <div class="form-actions">
            <button type="button" name="btn_add_sub_ou" class="btn btn-primary" onclick="row_ou_onclick_pop_win(0)"><i class="icon-plus"></i><i class="icon-folder-close"></i>Thêm đơn vị</button>
            <button type="button" name="btn_add_user" class="btn btn-primary" onclick="row_user_onclick_pop_win(0)"><i class="icon-plus"></i><i class="icon-user "></i>Thêm NSD</button>
            <button type="button" name="btn_add_group" class="btn btn-primary" onclick="row_group_pop_win(0)"><i class="icon-plus"></i><i class="icon-group"></i>Thêm Nhóm NSD</button>
		</div>
    
        <!-- Don vi -->
        <table class="table table-bordered table-striped table-hover">
            <colgroup>
                <col width="60%" />
                <col width="10%" />
                <col width="20%" />
                <col width="10%" />
            </colgroup>
            <thead><tr>
                <th>Tên </th>
                <th>Thứ tự</th>
                <th>Loai</th>
                <th>#</th>
            </tr></thead>
            <?php for ($i=0; $i<count($arr_all_sub_ou); $i++): ?>
                <?php
                $v_sub_ou_id     = $arr_all_sub_ou[$i]['PK_OU'];
                $v_sub_ou_name   = $arr_all_sub_ou[$i]['C_NAME'];
                $v_sub_ou_order  = $arr_all_sub_ou[$i]['C_ORDER'];
    
                $v_class = 'row' . ($v_row % 2);
                $v_row++;
    
                $v_link = $this->get_controller_url() . 'dsp_all_sub_ou/' . $v_sub_ou_id;
                ?>
                <tr class="<?php echo $v_class;?>">
                    <td>
                        <img src="<?php echo SITE_ROOT;?>public/images/unit16.png" />
                        <a href="<?php echo $v_link;?>">
                            <?php echo $v_sub_ou_name;?>
                        </a>
                    </td>
                    <td><?php echo $v_sub_ou_order;?></td>
                    <td>Đơn vị hành chính</td>
                    <td>
                        <a href="javascript:void(0)" onclick="row_ou_onclick_pop_win(<?php echo $v_sub_ou_id;?>)"><i class="icon-edit"></i> Sửa</a> |
                        <a href="javascript:void(0)" onclick="quick_delete_ou(<?php echo $v_sub_ou_id . ',' . $id;?>)"><i class="icon-trash"></i> Xoá</a>
                    </td>
                </tr>
            <?php endfor; ?>
            <!-- NSD -->
            <?php for ($i=0; $i<count($arr_all_user_by_ou); $i++): ?>
                <?php
                $v_user_id      = $arr_all_user_by_ou[$i]['PK_USER'];
                $v_user_name    = $arr_all_user_by_ou[$i]['C_NAME'];
                $v_order        = $arr_all_user_by_ou[$i]['C_ORDER'];
                $v_status       = $arr_all_user_by_ou[$i]['C_STATUS'];
    
                $v_class = 'row' . ($v_row % 2);
                $v_row++;
    
                $v_url = $this->get_controller_url() . 'dsp_single_user/' . $v_user_id
                        . '/?hdn_item_id=' . $v_user_id . '&pop_up=1';
    
                $v_icon_file_name = ($v_status > 0) ? 'icon-16-user.png' : 'icon-16-user-inactive.png';
                ?>
                <tr class="<?php echo $v_class;?>">
                    <td>
                        <img src="<?php echo $this->template_directory . 'images/' . $v_icon_file_name ;?>" border="0" align="absmiddle" />
                        <?php echo $v_user_name;?>
                    </td>
                    <td class="center"><?php echo $v_order;?></td>
                    <td>Người sử dụng</td>
                    <td>
                        <a href="javascript:void(0)" onclick="row_user_onclick_pop_win(<?php echo $v_user_id;?>)"><i class="icon-edit"></i> Sửa</a>
                        | <a href="javascript:void(0)" onclick="quick_delete_user(<?php echo $v_user_id;?>)"><i class="icon-trash"></i> Xoá</a>
                    </td>
                </tr>
            <?php endfor; ?>
            <!-- Group -->
            <?php for ($i=0; $i<count($arr_all_group_by_ou); $i++): ?>
                <?php
                $v_group_id         = $arr_all_group_by_ou[$i]['PK_GROUP'];
                $v_name             = $arr_all_group_by_ou[$i]['C_NAME'];
    
                $v_is_built_in      = $arr_all_group_by_ou[$i]['C_BUILT_IN'];
    
                $v_class = 'row' . ($v_row % 2);
                $v_row++;
                ?>
                <tr class="<?php echo $v_class;?>">
                    <td>
                        <img src="<?php echo $this->template_directory . 'images/user-group16.png' ;?>" border="0" align="absmiddle" />
                        <?php echo $v_name;?>
                    </td>
                    <td></td>
                    <td>Nhóm người sử dụng</td>
                    <td>
                        <a href="javascript:void(0)" onclick="row_group_pop_win(<?php echo $v_group_id;?>)"><i class="icon-edit"></i> Sửa</a>
                        <?php if ($v_is_built_in == 0): ?>
                            | <a href="javascript:void(0)" onclick="quick_delete_group(<?php echo $v_group_id;?>)"><i class="icon-trash"></i> Xoá</a>
                        <?php endif; ?>
                    </td>
                </tr>
    
            <?php endfor; ?>
            <?php echo $this->add_empty_rows($v_row, _CONST_DEFAULT_ROWS_PER_PAGE, 4); ?>
        </table>
    </form>
</div>
<script>
    function user_pop_win(url)
    {
        showPopWin(url ,800,500, null);
    }
    function ou_pop_win(url)
    {
        showPopWin(url ,800,420, null);
    }

    function row_user_onclick_pop_win(user_id)
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_single_user/'  + user_id + '/' + QS;
            url += 'user_id=' + user_id + '&hdn_item_id=' + user_id + '&pop_win=1';
            url += '&parent_ou_id=' + $("#hdn_current_ou_id").val();
            url += '&parent_ou_name=' + escape($("#hdn_current_ou_name").val());

        user_pop_win(url);
    }

    function row_ou_onclick_pop_win(id)
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_single_ou/' + id + '/' + QS;
            url += 'ou_id=' + id + '&hdn_item_id=' + id + '&pop_win=1';
            url += '&parent_ou_id=' + $("#hdn_current_ou_id").val();
            url += '&parent_ou_name=' + escape($("#hdn_current_ou_name").val());

        ou_pop_win(url);
    }

    function row_group_pop_win(id)
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_single_group/' + id + '/' + QS;
            url += 'group_id=' + id + '&hdn_item_id=' + id + '&pop_win=1';
            url += '&parent_ou_id=' + $("#hdn_current_ou_id").val();
            url += '&parent_ou_name=' + escape($("#hdn_current_ou_name").val());

        user_pop_win(url);
    }

    function quick_delete_ou(ou_id)
    {
        var f = document.frmMain;
        if (confirm('Bạn chắc chắn xoá đối tượng đã chọn?')){
            f.hdn_item_id.value =  ou_id;
            m = $("#controller").val() + 'delete_ou';
            $("#frmMain").attr("action", m);
            f.submit();
        }
    }


    function quick_delete_user(user_id)
    {
        var f = document.frmMain;
        if (confirm('Bạn chắc chắn xoá đối tượng đã chọn?')){
            f.hdn_item_id.value =  user_id;
            m = $("#controller").val() + 'delete_user';
            $("#frmMain").attr("action", m);
            f.submit();
        }
    }

    function quick_delete_group(group_id)
    {
        var f = document.frmMain;
        if (confirm('Bạn chắc chắn xoá đối tượng đã chọn?')){
            f.hdn_item_id.value =  group_id;
            m = $("#controller").val() + 'delete_group';
            $("#frmMain").attr("action", m);
            f.submit();
        }
    }
</script>
<?php $this->template->display('dsp_footer.php');