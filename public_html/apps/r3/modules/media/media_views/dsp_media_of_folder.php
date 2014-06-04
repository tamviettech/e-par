<?php
/**


This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
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
    <!--quay lai 1 thu muc-->
    <?php if($parent_folder_id != '-1'):?>
    <tr>
        <td></td>
        <td>
            <span class="folder">
                <a href="javascript:void(0)" onclick="change_folder(<?php echo $parent_folder_id?>)" >
                    ........
                </a>
            </span>
        </td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <?php endif;?>
    
    <?php foreach($arr_media_of_folder as $arr_media):
            $v_media_id    = $arr_media['PK_MEDIA'];
            $v_ext         = $arr_media['C_EXT'];
            $v_name        = $arr_media['C_NAME'];
            $v_upload_date = $arr_media['C_UPLOAD_DATE'];
            $v_type        = $arr_media['C_TYPE'];
            $v_file_name   = $arr_media['C_FILE_NAME'];
            $v_shared      = $arr_media['C_SHARED'];
            
            
            //tao link file
            $v_year = $arr_media['C_YEAR'];
            $v_month = $arr_media['C_MONTH'];
            $v_day = $arr_media['C_DAY'];
            $v_file_link = CONST_FILE_UPLOAD_LINK . "$v_year/$v_month/$v_day/$v_file_name";
    ?>
    <tr>
        <td>
            <input type="checkbox" name="chk"
               value="<?php echo $v_media_id?>" 
               onclick="if (!this.checked) this.form.chk_check_all.checked=false;" 
               />
        </td>
        <td >
            <?php
                if($v_type == 0)
                {
                    $class = ($v_shared > 0)?'file_shared':'file';
                    $onclick = '';
                }
                else
                {
                    $class = ($v_shared > 0)?'folder_shared':'folder';
                    $onclick = "change_folder($v_media_id)";
                }
                $href    = 'javascript:void(0)';
            ?>
            <span class="<?php echo $class?>">
                <a href="<?php echo $href;?>" onclick="<?php echo $onclick?>" >
                    <?php echo $v_name?>
                </a>
            </span>
        </td>
        <td><?php echo $v_ext?></td>
        <td><?php echo $v_upload_date?></td>
        <td style="text-align: right;">
            <?php if($v_type == 0):?>
            <!--xem truơc-->
            <a href="<?php echo $v_file_link?>" target="_blank" title="Xem trước" style="text-decoration:none">
                 <img src="<?php echo FULL_SITE_ROOT.'public/images/Folder_Open.png'?>"  width="20px"/>
                <!--<i class="icon-eye-open"></i>-->
            </a>
            |
            <?php endif;?>
            
            <?php if($is_share != '1'):
                    //neu la chia se thi khong hien thi
            ?>
            <!--sua-->
            <a href="javascript:void(0)" onclick="btn_rename_onclick(<?php echo $v_media_id;?>);" title="Sửa" style="text-decoration:none">
                <img src="<?php echo FULL_SITE_ROOT.'public/images/edit-file.png'?>"  width="20px"/>
                  <!--<i class="icon-edit"></i>-->
            </a>
            |
            <!--chia se-->
            <a href="#" title="Chia sẻ" onclick="btn_share_onclick(<?php echo $v_media_id;?>);" style="text-decoration:none">
                 <img src="<?php echo FULL_SITE_ROOT.'public/images/share-file.png'?>"  width="25px"/>
                 <!--<i class="icon-share "></i>-->
            </a>
            <?php endif;?>
        </td>
    </tr>
    <?php endforeach;?>
</table>

<div class="control-group" class="media-btn-delete" style="float: right;margin-top: 5px;">
    <!--upload-->
    <button class="btn btn-primary" type="button" style="margin-bottom: 3px;" onclick="dsp_upload_onclick();">
        <i class="icon-upload"></i>
        Tải lên
    </button>
    <!--xoa-->
    <button class="btn btn-danger" type="button" style="margin-bottom: 3px;" onclick="btn_media_delete_onclick();">
        <i class="icon-trash"></i>
        Xóa
    </button>
</div>
