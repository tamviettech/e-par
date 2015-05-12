<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}
//info of media
$media_id   = $arr_info_of_media['PK_MEDIA'];
$media_type = $arr_info_of_media['C_TYPE'];

$user_active = isset($arr_all_user_shared[0]['FK_USER'])?$arr_all_user_shared[0]['FK_USER']:'';
?>
<div class="container-fluid">
    <form name="frmMain" method="post" id="frmMain" action="" class="form-horizontal"><?php
        echo $this->hidden('controller', $this->get_controller_url());
        echo $this->hidden('hdn_all_user_method', 'dsp_main/dsp_all_users_to_add/');
        echo $this->hidden('XmlData', '');
        echo $this->hidden('hdn_update_method', 'do_share_media');
        echo $this->hidden('hdn_user_id_list', '');
        echo $this->hidden('hdn_media_id', $media_id);
        
        echo $this->hidden('hdn_user_active', $user_active);
        ?>
        <div class="row-fluid">
            <div id="group_user" class="tab-pane">
                <div>
                    <div id="users_in_group" class="span8">
                        <table width="100%" class="table table-bordered" cellspacing="0" id="tbl_user_in_group">
                            <colgroup>
                                <col width="5%" />
                                <col width="95%" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo __('user name')?></th>
                                </tr>
                            </thead>
                            <?php for ($i=0; $i<count($arr_all_user_shared); $i++): ?>
                                <?php
                                $v_user_id     = $arr_all_user_shared[$i]['FK_USER'];
                                $v_user_name   = $arr_all_user_shared[$i]['C_NAME'];

                                $v_icon_file_name = 'icon-16-user.png';
                                
                                ?>
                                <tr id="tr_<?php echo $v_user_id;?>" onclick="tr_active(<?php echo $v_user_id?>)">
                                    <td class="center">
                                        <input type="checkbox" name="chk" value="<?php echo $v_user_id;?>" id="user_<?php echo $v_user_id;?>" />
                                    </td>
                                    <td>
                                        <label for="user_<?php echo $v_user_id;?>">
                                            <img src="<?php echo $this->template_directory . 'images/' . $v_icon_file_name ;?>" border="0" align="absmiddle" />
                                            <?php echo $v_user_name;?>
                                        </label>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </table>
                    </div>
                    <div id="users_in_ou_action" class="span4" style="text-align: center">
                        <?php if($media_type == 1):?>
                        <div id="div_all_grant" class="content-widgets light-gray" style="width: 98%;text-align: left;">
                            
                            <?php foreach ($arr_all_user_shared as $arr_user_shared):
                                    $v_user_id   = $arr_user_shared['FK_USER'];
                                    $v_user_name = $arr_user_shared['C_NAME'];
                            
                                    $v_grant  = $arr_user_shared['C_GRANT'];
                                    $arr_grant = explode(',', $v_grant);
                            ?>
                            <div class="grant_permit" id="grant_<?php echo $v_user_id?>" style="display: none">
                                <div class="widget-head blue">
                                    <h3>Phân quyền - <?php echo $v_user_name;?></h3>
                                </div>
                                <div  class="widget-container" >
                                    <label>
                                        <input type="checkbox" <?php echo (in_array(CONST_PERMIT_UPLOAD_MEDIA, $arr_grant))?'checked':'';?> 
                                               name="chk_grant_<?php echo $v_user_id?>[]" 
                                               value="<?php echo CONST_PERMIT_UPLOAD_MEDIA;?>"/>
                                        Upload file
                                    </label>
                                    <label>
                                        <input type="checkbox" 
                                            <?php echo (in_array(CONST_PERMIT_DELETE_MEDIA, $arr_grant))?'checked':'';?> 
                                               name="chk_grant_<?php echo $v_user_id?>[]" 
                                               value="<?php echo CONST_PERMIT_DELETE_MEDIA;?>"/>
                                        Xóa file
                                    </label>
                                </div>
                            </div>
                            <?php endforeach;?>
                        </div>
                        <?php endif;?>
                        <button type="button" name="btn_add_user" class="btn btn-primary input-medium" onclick="dsp_all_user_to_add();"><i class="icon-plus icon-user"></i><?php echo __('add user to group');?></button>
                        <button type="button" name="btn_remove_user" class="btn input-medium" onclick="remove_user_from_group();"><i class="icon-trash"></i><?php echo __('remove user from group')?></button>
                    </div>
                </div>
                <div class="clear">&nbsp;</div>
            </div>
            <!-- Button -->
            <div class="form-actions" style="text-align: center;">
                <button type="button" name="btn_update_group" class="btn btn-primary" onclick="btn_update_group_onclick();"><i class="icon-save"></i><?php echo __('update');?></button>
                <?php $v_back_action = 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
                <button type="button" class="btn" onclick="<?php echo $v_back_action;?>"><i class="icon-remove"></i>Đóng cửa sổ</button>
            </div>
        </div><!-- /.row-fluid -->
    </form>
</div>
<script>
    $(document).ready(function(){
        user_active = $('#hdn_user_active').val();
        tr_active(user_active)
    });
    
    function tr_active(index)
    {
        //list NSD
        $('#tbl_user_in_group tr').attr('class','');
        selector_tr = '#tr_' + index;
        $(selector_tr).attr('class','tr_active');   
        
        //phan quuyen
        $('.grant_permit').hide();
        selector_grant = '#grant_' + index;
        $(selector_grant).show();   
    }
    //them NSD vao danh sach
    function add_user(returnVal) {

        json_data = JSON.stringify(returnVal);

        for (i=0; i<returnVal.length; i++)
        {
            v_user_id = returnVal[i].user_id;
            v_user_name = returnVal[i].user_name;
            v_user_status = returnVal[i].user_status;

            //Neu user chua co trong chia se => them vao danh sach user
            q = '#user_' + v_user_id;
            if( $(q).length < 1 )
            {
                html = '<tr id="tr_' + v_user_id + '" onclick="tr_active(' + v_user_id + ')">';
                html += '<td class="center">';
                html +=     '<input type="checkbox" name="chk" value="' + v_user_id + '" id="user_' + v_user_id + '" />';
                html += '</td>';

                v_icon_file_name = (v_user_status > 0) ? 'icon-16-user.png' : 'icon-16-user-inactive.png';
                html += '<td>';
                html += '<label for="user_' + v_user_id + '"><img src="<?php echo $this->template_directory;?>images/' + v_icon_file_name + '" border="0" align="absmiddle" />';
                html += v_user_name + '</label>';
                html += '</td></tr>';
                $('#tbl_user_in_group').append(html);
            }
            //Neu user chua co trong chia se => them vao danh sach phanq uyen
            q = '#grant_' + v_user_id;
            if( $(q).length < 1 )
            {
                html ='<div class="grant_permit" id="grant_' + v_user_id + '" style="display: none">';
                html +=     '<div class="widget-head blue">';
                html +=         '<h3>Phân quyền - ' + v_user_name + '</h3>';
                html +=     '</div>';
                html +=      '<div class="widget-container">';
                html +=         '<label>';
                html +=             '<input type="checkbox" name="chk_grant_' + v_user_id + '[]" value="<?php echo CONST_PERMIT_UPLOAD_MEDIA;?>"/>';
                html +=             'Upload file';
                html +=         '</label>';
                html +=         '<label>';
                html +=             '<input type="checkbox" name="chk_grant_' + v_user_id + '[]" value="<?php echo CONST_PERMIT_DELETE_MEDIA;?>"/>';
                html +=             'Xóa file';
                html +=         '</label>';
                html +=     '</div>';
                html += '</div>';
                $('#div_all_grant').append(html);
            }
        }
    }
    //hien thi man hinh tat ca NSD
    function dsp_all_user_to_add()
    {
        var url = $('#controller').val() + $('#hdn_all_user_method').val() + QS +'pop_win=1';
        showPopWin(url, 450, 350, add_user);
    }
    //bo NSD
    function remove_user_from_group()
    {
        var q = "input[name='chk']";
        $(q).each(function(index) {
            if ($(this).is(':checked'))
            {
                v_user_id = $(this).val();
                s = '#tr_' + v_user_id;
                $(s).remove();
            }
        });
    }
    
    //ghi lai danh sach User hien tai trong Group
    function btn_update_group_onclick()
    {
        var arr_user_id = new Array();
        var q = "input[name='chk']";
        $(q).each(function(index) {
            arr_user_id.push($(this).val());
        });

        document.frmMain.hdn_user_id_list.value = arr_user_id.join();

        btn_update_onclick();
    }
</script>