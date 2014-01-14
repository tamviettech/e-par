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

<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}

deny_bad_http_referer();

//display header
$this->template->title = 'Thêm ý kiến';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_record_id = isset($_REQUEST['hdn_item_id']) ? $_REQUEST['hdn_item_id'] : 0;

($v_record_id > 0) OR DIE();

Session::set('add_comment_token', uniqid());
?>
<form name="frmMain" id="frmMain" method="POST" action="<?php echo $this->get_controller_url();?>do_add_comment">
    <?php echo $this->hidden('hdn_item_id', $v_record_id);?>
    <?php echo $this->user_token();?>
    <br/>
    <table class="none-border-table" style="width:100%">
        <tr>
            <td width="25%" valign="top" style="vertical-align:top">Nội dung ý kiến: <span class="required">*</span></td>
            <td>
                <textarea style="width: 320px; height: 161px; margin: 0px;" rows="2" 
                    name="txt_content" id="txt_content" cols="20" maxlength="400"
                    ></textarea>
           </td>
        </tr>
    </table>
    <div class="clear">&nbsp;</div>
    <!-- Buttons -->
    <div class="button-area">
        <input type="button" name="btn_do_add_comment" class="button save" value="<?php echo __('update');?>" onclick="btn_do_add_comment_onclick();" />
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script>
    var f=document.frmMain;
    f.txt_content.focus();
    function btn_do_add_comment_onclick()
    {
        var v_content = trim(f.txt_content.value);

        if (v_content == '')
        {
            alert('Bạn chưa nhập nội dung!');
            f.txt_content.focus();
            return false;
        }
        
        jQuery.post(
                '<?php echo $this->get_controller_url();?>do_add_comment'
                ,{ hdn_item_id: $("#hdn_item_id").val()
                    ,txt_content: $("#txt_content").val()
                    ,sid:'<?php echo session_id();?>'
                    ,user_token:'<?php echo Session::get('user_token');?>'
                    ,add_comment_token:'<?php echo Session::get('add_comment_token');?>'
                 }
                ,function( data ) {
                    html = '';
                    for (i=0; i<data.length; i++)
                    {
                        v_comment_id = data[i].comment_id;
                        v_user_code = data[i].user_code;
                        v_user_name = data[i].user_name;
                        v_job_title = data[i].job_title;
                        v_create_date = data[i].date;
                        v_content = data[i].content;
                        v_type = data[i].type;

                        v_html_class = (v_type == 1) ? ' class="bod_comment"' : '';

                        html += '<tr data-cid="c_' + v_comment_id + '"' + v_html_class + '>';
                        html += '<td class="center"><input type="checkbox" name="chk_comment" value="' + v_comment_id + '" data-user="' + v_user_code + '"/></td>';
                        html += '<td>' + v_content + '</td>';
                        html += '<td>' + v_user_name + '(' + v_job_title + ')</td>';
                        html += '<td>' + v_create_date + '</td>';
                        html += '</tr>';
                    }
                    $('#tbl_comment_header', window.parent.document).after(html);
                    window.parent.hidePopWin();
                }
                , "json"
       );
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');