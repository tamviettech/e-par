<?php
?>
<table width="100%" class="adminlist table table-bordered table-striped">
    <col width="5%" />
    <col width="50%" />
    <col width="15%" />
    <col width="15%" />
    <col width="*"  />
   
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
    
    <?php foreach($arr_all_media_share as $arr_media):
            $v_media_id    = $arr_media['PK_MEDIA'];
            $v_ext         = $arr_media['C_EXT'];
            $v_name        = $arr_media['C_NAME'];
            $v_upload_date = $arr_media['C_UPLOAD_DATE'];
            $v_type        = $arr_media['C_TYPE'];
            $v_file_name   = $arr_media['C_FILE_NAME'];
            $v_shared      = $arr_media['C_SHARED'];
            $v_grant      = $arr_media['C_GRANT'];            
            
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
                    $onclick = "change_folder($v_media_id,'$v_grant')";
                }
                $href    = 'javascript:void(0)';
            ?>
            <span class="<?php echo $class?>">
                <a href="<?php echo $href;?>" onclick="<?php echo $onclick?>"  style="text-decoration:none">
                    <?php echo $v_name?>
                </a>
            </span>
        </td>
        <td><?php echo $v_ext?></td>
        <td><?php echo $v_upload_date?></td>
        <td style="text-align: right;">
            <?php if($v_type == 0):?>
            <!--xem truơc-->
            <a href="<?php echo $v_file_link?>" target="_blank" title="Xem trước">
                 <img src="<?php echo FULL_SITE_ROOT.'public/images/Folder_Open.png'?>"  width="20px"/>
                <!--<i class="icon-eye-open"></i>-->
            </a>
            <?php endif;?>
        </td>
    </tr>
    <?php endforeach;?>
</table>