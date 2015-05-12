<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');
?>
<form name="frmMain" method="post" id="frmMain" action="#">
    <div class="search-box">
        <div class="input-append input-icon">
            <input placeholder="Tìm kiếm..." type="text" style="width: 90%;" onkeypress="search_user_onclick(this,event)">
            <i class=" icon-search"></i>
        </div>
    </div>
    <table width="100%" class="adminlist" cellspacing="0" border="1">
        <colgroup>
             <col width="5%" />
            <col width="75%" />
            <col width="20%" />
        </colgroup>
        <tr>
            <th>#</th>
            <th>Tên NSD</th>
            <th>Chức vụ</th>
        </tr>
    </table>
    <div style="height:210px;overflow: scroll">
        <table width="100%" class="adminlist table table-bordered table-striped" cellspacing="0" border="1">
            <colgroup>
                <col width="5%" />
                <col width="75%" />
                <col width="20%" />
            </colgroup>

            <?php for ($i=0; $i<count($arr_all_user_to_add); $i++): ?>
                <?php
                $v_ou_id       = $arr_all_user_to_add[$i]['PK_OU'];
                $v_ou_name     = $arr_all_user_to_add[$i]['C_NAME'];
                $v_ou_user     = $arr_all_user_to_add[$i]['C_XML_USER'];
                
                $dom_user = simplexml_load_string('<root>' . $v_ou_user . '</root>');

                $users = $dom_user->xpath("//row");

                //$v_icon_file_name = ($v_status > 0) ? 'icon-16-user.png' : 'icon-16-user-inactive.png';
                $v_class = 'row' . strval($i % 2);
                ?>
                <tr class="module_name">
                    <td align="center">
                        <input type="checkbox" name="chk_ou"
                            data-ou_id="<?php echo $v_ou_id?>"
                            id="chk_ou_<?php echo $v_ou_id;?>"
                            onchange="chk_ou_onchange(this)" />
                    </td>
                    <td>
                        <label for="chk_ou_<?php echo $v_ou_id;?>"><?php echo $v_ou_name;?></label>
                    </td>
                    <td></td>
                </tr>
                <?php foreach ($users as $user):?>
                    <tr class="user_name">
                        <td class="center">
                            <input type="checkbox" name="chk_user"
                                value="<?php echo $user->attributes()->PK_USER;?>"
                                id="user_<?php echo $user->attributes()->PK_USER;?>"
                                data-user_name="<?php echo $user->attributes()->C_NAME;?>"
                                data-ou="<?php echo $v_ou_id?>"
                            />
                        </td>
                        <td style="padding-left: 20px;">
                            <img src="<?php echo SITE_ROOT;?>public/images/icon-16-user.png" border="0" align="absmiddle" />
                            <label for="user_<?php echo $user->attributes()->PK_USER;?>">
                                <?php echo $user->attributes()->C_NAME;?>
                            </label>
                        </td>
                        <td>
                            <?php echo $user->attributes()->C_JOB_TITLE;?>
                        </td>
                    </tr>
                <?php endforeach;?>

            <?php endfor; ?>
            <?php //echo $this->add_empty_rows($i+1, _CONST_DEFAULT_ROWS_PER_PAGE, 2); ?>
        </table>
    </div>
    <!-- Button -->
    <div class="button-area">
        
        <button type="button" name="btn_update_group" class="btn btn-primary" onclick="get_selected_user();">
            <i class="icon-save"></i>
                <?php echo __('update');?>
        </button>    
        <?php $v_back_action = 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <button type="button" name="cancel" class="btn" onclick="<?php echo $v_back_action;?>">
            <i class="icon-remove"></i>
            Đóng cửa sổ
        </button>
    </div>
</form>
<script>
    function search_user_onclick(search,evt)
    {
        if (IE()){
            var theKey=window.event.keyCode
        } else {
            var theKey=evt.which;
        }
        
        if(theKey == 13){
            evt.preventDefault();
            $('table .user_name').show();
            var str = $(search).val();
            $('table .user_name').each(function(){
                var user_name = $(this).find('td:eq(1) label').html();
               if (user_name.indexOf(str) == -1) {
                   $(this).hide();
                }
            });
        }
    }
    
    function chk_ou_onchange(chk_ou)
    {
        v_ou_id = $(chk_ou).attr('data-ou_id');

        q = 'input[name="chk_user"][data-ou="'+v_ou_id+'"]';
        if ($(chk_ou).is(':checked'))
        {
        	$(q).each(function(index) {
        		$(this).attr('checked', true);
        	});
        }
        else
        {
        	$(q).each(function(index) {
        		$(this).attr('checked', false);
        	});
        }
    }
    function get_selected_user()
    {
        var jsonObj = []; //declare array

        q = "input[name='chk_user']";
        $(q).each(function(index) {
            if ($(this).is(':checked'))
            {
                v_user_id = $(this).val();
                v_user_name = $(this).attr('data-user_name');
                //v_user_status = $(this).attr('data-user_status');

                jsonObj.push({'user_id': v_user_id, 'user_name': v_user_name, 'user_status': 1});
            }
        });

        returnVal = jsonObj;
        window.parent.hidePopWin(true);
    }
</script>