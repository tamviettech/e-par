<?php

//de qui hien thi tat ca thu muc con cua 1 thu muc
function show_folder(&$arr_all_folder,$index)
{
    //thu muc muon de qui
    $arr_folder = $arr_all_folder[$index];
    //lay bien 
    $v_name = $arr_folder['C_NAME'];
    $v_id   = $arr_folder['PK_MEDIA'];
    
    echo "<li class=\"closed\">";
    echo "<span  class=\"folder\">
               <a href=\"javascript:void(0)\" id=\"folder_$v_id\" onclick=\"change_folder($v_id)\">$v_name</a> 
          </span>";
    //kiem tra co node con k
    for($i=0;$i<count($arr_all_folder);$i++)
    {
        if($arr_all_folder[$i]['FK_PARENT'] == $v_id)
        {
            echo '<ul>';
            show_folder($arr_all_folder,$i);
            echo '</ul>';
        }
    }
    echo "</li>";
}
//de qui hien thi tat ca thu muc con cua 1 thu muc
function show_folder_public(&$arr_all_folder_public,$index)
{
    //thu muc muon de qui
    $arr_folder = $arr_all_folder_public[$index];
    //lay bien 
    $v_name = $arr_folder['C_NAME'];
    $v_id   = $arr_folder['PK_MEDIA'];
    
    echo "<li class=\"closed\">";
    echo "<span  class=\"folder\">
               <a href=\"javascript:void(0)\" id=\"folder_public_$v_id\" onclick=\"change_folder_public($v_id)\">$v_name</a> 
          </span>";
    //kiem tra co node con k
    for($i=0;$i<count($arr_all_folder_public);$i++)
    {
        if($arr_all_folder_public[$i]['FK_PARENT'] == $v_id)
        {
            echo '<ul>';
            show_folder_public($arr_all_folder_public,$i);
            echo '</ul>';
        }
    }
    echo "</li>";
}

$v_user_share_to_you = isset($arr_all_user_share_to_you[0]['PK_USER'])?$arr_all_user_share_to_you[0]['PK_USER']:'';
?>
<style>
    .nav-tabs > li > a {
        padding-top: 0px;
        padding-bottom: 0px;
        line-height: 20px;
        border: 1px solid transparent;
    }
</style>
<form id="frmMain" name="frmMain" action="" method="POST" class="form-horizontal"> 
    <?php
        //method
        echo $this->hidden('controller',$this->get_controller_url());
        echo $this->hidden('hdn_upload_method','dsp_main/dsp_upload/');
        echo $this->hidden('hdn_share_method','dsp_main/dsp_share_media/');
        echo $this->hidden('hdn_newdir_method','create_folder');
        echo $this->hidden('hdn_mof_method','dsp_media_of_folder/');
        echo $this->hidden('hdn_delete_method','do_delete_media/');
        echo $this->hidden('hdn_rename_method','do_rename_media/');
        //param
        echo $this->hidden('hdn_item_id_list','');
        //media
        echo $this->hidden('hdn_folder_id','');
        //share
        echo $this->hidden('hdn_user_share_to_you',$v_user_share_to_you);
        echo $this->hidden('hdn_sty_method','dsp_all_share_to_you');
        echo $this->hidden('hdn_share_folder_id','');
        echo $this->hidden('hdn_grant_permit_share','');
        //tab
        echo $this->hidden('hdn_tab_selected','');
    ?>
    <div class="tab-widget" >
        <ul class="nav nav-tabs" id="myTab1" >
            <li><a href="#media">Tài liệu</a></li>
            <li><a href="#share">Chia sẻ tài liệu</a></li>
            <li><a href="#public">Tài liệu dùng chung</a></li>
        </ul>
        <div class="tab-content">
            <div id="media" class="tab-pane">
                <div class="row-fluid">
                    <!--danh sach thu muc-->
                    <div class="span4" style="width: 29%;background-color: #D2DFE8;height: 500px">
                        <div class="clear" style="height: 10px">&nbsp;</div>
                        <div class="content-widgets" style="width: 97%;margin: 0 auto;background: white;border: 1px solid #3498DB;">
                            <div class="widget-head bondi-blue">
                                <h3 style="font-weight: 900">Danh sách thư mục</h3>
                            </div>
                            <div id="list_folder" style="width: 100%;height: 300px">
                                <ul id="browser" class="filetree treeview">
                                        <li>
                                            <span  class="folder">
                                                <a href="javascript:void(0)" id="folder_0" onclick="change_folder()">Thư mục gốc</a>
                                            </span>
                                            <ul>
                                                <?php 
                                                foreach($arr_all_folder as $key => $arr_folder)
                                                {
                                                    $v_parent_id = $arr_folder['FK_PARENT'];
                                                    if($v_parent_id == NULL)
                                                    {
                                                        if($arr_folder['C_PUBLIC'] != 1)
                                                        {
                                                            show_folder($arr_all_folder,$key);
                                                        }
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                </ul>
                            </div>
                        </div>
                        <div class="clear" style="height: 10px">&nbsp;</div>
                        <!--button-->
                        <div class="content-widgets" style="text-align: center;">                            
                             <!--buton tao thu muc-->
                            <button type="button" name="addnew" class="btn"  onclick="newdir_onclick()" style="margin-bottom: 3px;">
                                <i class="icon-folder-close"></i>
                                Tạo thư mục
                            </button>
                             
                        </div>
                    </div>
                    <!--danh sach-->

                    <div  class="span8" style="margin-left: 5px;width: 70%;border-bottom: solid 1px rgb(206, 206, 206);">
                        <table width="100%" class="adminlist table table-bordered table-striped">
                            <col width="5%" />
                            <col width="50%" />
                            <col width="15%" />
                            <col width="15%" />
                            <col width="*" />
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" name="chk_check_all" onclick="toggle_check_all(this,this.form.chk);"/>
                                    </th>
                                    <th>Tên</th>
                                    <th>Loại</th>
                                    <th>Ngày khởi tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                        </table>
                        <div id="content_media">
                        </div>
                    </div>
                    <div class="control-group" class="media-btn-delete" style="float: right;margin-top: 5px;">
                        <!--upload-->
                        <button class="btn btn-primary" type="button" style="margin-bottom: 3px;" onclick="dsp_upload_onclick();">
                            <i class="icon-upload"></i>
                            Tải lên
                        </button>
                        <!--xoa-->
                        <button class="btn" type="button" style="margin-bottom: 3px;" onclick="btn_media_delete_onclick();">
                            <i class="icon-trash"></i>
                            Xóa
                        </button>
                    </div>

                </div>
            </div>
            <div id="share" class="tab-pane">
               <div class="row-fluid">
                    <!--danh sach thu muc-->
                    <div class="span4" style="width: 29%;background-color: #D2DFE8;height: 500px">
                        <div class="clear" style="height: 10px">&nbsp;</div>
                        <div class="content-widgets" style="width: 97%;margin: 0 auto;background: white;border: 1px solid #3498DB;">
                            <div class="widget-head bondi-blue">
                                    <h3>Chia sẻ file với bạn</h3>
                            </div>
                            <div id="list_user_share" style="width: 100%;height: 300px">
                                <?php foreach($arr_all_user_share_to_you as $arr_user_share_to_you):
                                        $v_user_name = $arr_user_share_to_you['C_NAME'];
                                        $v_user_id   = $arr_user_share_to_you['PK_USER'];
                                ?>
                                <a id="user_share_<?php echo $v_user_id?>" 
                                   href="javascript:void(0)" 
                                   style="margin-left: 10px;margin-top: 5px;" 
                                   onclick="change_user_share(<?php echo $v_user_id?>)">
                                    <img src="<?php echo $this->template_directory . 'images/icon-16-user.png';?>" border="0" align="absmiddle" />
                                    <?php echo $v_user_name;?>
                                </a>
                                <br>
                                <?php endforeach;?>
                            </div>
                        </div>
                        <div class="clear" style="height: 10px">&nbsp;</div>
                        <!--button-->
                        <div class="content-widgets" style="text-align: center;">
                            <!--upload-->
                            <button id="btn_<?php echo CONST_PERMIT_UPLOAD_MEDIA?>" class="btn btn-primary" type="button" style="margin-bottom: 3px;display: none" onclick="dsp_upload_onclick();">
                                <i class="icon-upload"></i>
                                Upload
                            </button>
                            <!--xoa-->
                            <button id="btn_<?php echo CONST_PERMIT_DELETE_MEDIA?>" class="btn" type="button" style="margin-bottom: 3px;display: none" onclick="btn_media_delete_onclick();">
                                <i class="icon-trash"></i>
                                Xóa
                            </button>
                        </div>
                    </div>
                    <!--danh sach-->
                    <div class="span8" style="margin-left: 5px;width: 70%;border-bottom: solid 1px rgb(206, 206, 206);">
                        <table width="100%" class="adminlist table table-bordered table-striped">
                            <col width="5%" />
                            <col width="50%" />
                            <col width="15%" />
                            <col width="15%" />
                            <col width="*" />
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" name="chk_check_all" onclick="toggle_check_all(this,this.form.chk);"/>
                                    </th>
                                    <th>Tên</th>
                                    <th>Loại</th>
                                    <th>Ngày khởi tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                        </table>
                        <div id="content_media_shared" >
                        </div>
                    </div>
                </div>
            </div>
            
            <!--start media public-->
            <div id="public" class="tab-pane">
                <div class="row-fluid">
                    <!--danh sach thu muc-->
                    <div class="span4" style="width: 29%;background-color: #D2DFE8;height: 500px">
                        <div class="clear" style="height: 10px">&nbsp;</div>
                        <div class="content-widgets" style="width: 97%;margin: 0 auto;background: white;border: 1px solid #3498DB;">
                            <div class="widget-head bondi-blue">
                                <h3 style="font-weight: 900">Danh sách thư mục</h3>
                            </div>
                            <div id="list_folder-public" style="width: 100%;height: 300px">
                                <ul id="browser-public" class="filetree treeview">
                                        <li>
                                            <span  class="folder">
                                                <a href="javascript:void(0)" id="folder_0-public" onclick="change_folder_public()">Thư mục gốc</a>
                                            </span>
                                            <ul>
                                                <?php    
                                                    foreach($arr_all_folder_public as $key => $arr_folder)
                                                    {
                                                        $v_parent_id = $arr_folder['FK_PARENT'];
                                                        if($v_parent_id == NULL)
                                                        {
                                                                show_folder_public($arr_all_folder_public,$key);
                                                        }
                                                    }
                                                ?>
                                            </ul>
                                        </li>
                                </ul>
                            </div>
                        </div>
                        <div class="clear" style="height: 10px">&nbsp;</div>
                        <!--button-->
                        <?php   if(check_permission('MEDIA_PUBLIC', 'r3')) :; ?>
                            <div class="content-widgets" style="text-align: center;">                            
                                 <!--buton tao thu muc-->
                                <button type="button" name="addnew" class="btn"  onclick="newdir_onclick('1')" style="margin-bottom: 3px;">
                                    <i class="icon-folder-close"></i>
                                    Tạo thư mục
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!--danh sach-->
                    <div class="span8" style="margin-left: 5px;width: 70%;border-bottom: solid 1px rgb(206, 206, 206);">
                        <table width="100%" class="adminlist table table-bordered table-striped">
                            <col width="5%" />
                            <col width="50%" />
                            <col width="15%" />
                            <col width="15%" />
                            <col width="*" />
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" name="chk_check_all" onclick="toggle_check_all(this,this.form.chk);"/>
                                    </th>
                                    <th>Tên</th>
                                    <th>Loại</th>
                                    <th>Ngày khởi tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                        </table>
                        <div id="content_media_public" >
                        </div>
                    </div>
                    <?php
    if(check_permission('MEDIA_PUBLIC', 'r3'))
    {
        echo <<<END
            <div class="control-group" class="media-btn-delete" style="float: right;margin-top: 5px;">
            <!--upload-->
            <button class="btn btn-primary" type="button" style="margin-bottom: 3px;" onclick="dsp_upload_onclick(1);">
                <i class="icon-upload"></i>
                Tải lên
            </button>
            <!--xoa-->
            <button class="btn" type="button" style="margin-bottom: 3px;" onclick="btn_media_delete_onclick('1');">
                <i class="icon-trash"></i>
                Xóa
            </button>
            </div>
END;
        }
    ?>

                </div>
            </div>
            <!--End media public-->
        </div>
    </div>
</form>
<script>
    $(function () {
        $('#myTab1 a:eq(0)').tab('show')
    })
    //tab click
    $('#myTab1 a').click(function(e){
        var index = $(this).parent('li').index();
        $('#hdn_tab_selected').val(index);
    });
    
    $(document).ready(function(){
        //tao tree view
        init_tree_view();
        //tab
        $('#hdn_tab_selected').val('0');
        
        //hien folder mac dinh
        change_folder();
        
        //Load media mac dinh danh cho puclic        
        change_folder_public();
        
        //hien thi thu muc cua nguoi chia se dau tien
        user_share = $('#hdn_user_share_to_you').val();
        if(user_share != '' && user_share != null && typeof(user_share) != 'undefined')
        {
            change_user_share(user_share);
        }
        //slim cho list folder
        $('#list_folder').slimscroll({
            height: '300px',
            color: '#006699',
            size: '8px',
            alwaysVisible: true
        });
        //slim cho list user share
        $('#list_user_share').slimscroll({
            height: '300px',
            color: '#006699',
            size: '8px',
            alwaysVisible: true
        });
        
        //slim cho danh sach media
        $('#content_media').slimscroll({
            height: '450px',
            color: '#006699',
            size: '8px',
            alwaysVisible: true
        });
          //slim cho list folder
        $('#list_folder-public').slimscroll({
            height: '300px',
            color: '#006699',
            size: '8px',
            alwaysVisible: true
        });
        //slim cho danh media public
        $('#content_media_public').slimscroll({
            height: '450px',
            color: '#006699',
            size: '8px',
            alwaysVisible: true
        });
        
        //slim cho danh sach media share
        $('#content_media_shared').slimscroll({
            height: '450px',
            color: '#006699',
            size: '8px',
            alwaysVisible: true
        });
    });
</script>