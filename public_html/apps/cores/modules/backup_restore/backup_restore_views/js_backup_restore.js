

/**
 * Show theme backup
 */
function btn_backup_onclick() 
{
    var f= document.forms.frmMain;
    f.hdn_status_tab.value = '1';
    f.submit();

}
/**
 * Hiển thị tất các tập tin trong thư mục
 */
function btn_show_all_item_onclick() 
{
    var f= document.forms.frmMain;
    f.hdn_status_tab.value = '0';
    f.submit();
}
/**
 * Khoi phuc du lieu
 * @param string v_item_name: ten file
 **/
function onlcik_restore(v_item_name)
{
        if(!trim(v_item_name))
        {
            alert('Đã có lỗi sự cố xảy ra. Xin kiểm tra lại!');
            return false;
        }
        if(!confirm('Bạn xác định sao lưu dữ liệu từ tập tin này?'))
        {
            return;
        }
        var f = document.forms.frmMain;
        status_restore = f.hdn_restore_status.value;
        var v_url = f.controller.value + f.hdn_restore_method.value;
        $.ajax({
                url: v_url,
                data: {item_name: v_item_name},
                type: 'post',
                sync: true,
                beforeSend: function()
                                    {
                                        if(status_restore == 0)
                                            {
                                                $('#loading').show();
                                                f.hdn_restore_status.value = 1; 
                                            }
                                            else
                                            {
                                                return false;
                                            }
                                    },
                success: function(data)
                {
                        $('#loading').hide();
                        alert(data);
                        f.hdn_restore_status.value = 0;
                }
            }); 
}

/**
 * fc start backup
 */
function btn_do_backup_onclick() 
{
     
        var f             = document.forms.frmMain;
        var status_backup = f.hdn_backup_status.value;
        var v_url         = f.controller.value + f.hdn_backup_method.value;
        var rad_type      = $('[name="rad_type"]').val();
        $('#frmMain').attr('action',v_url);
        $('#frmMain').submit();
        return false;
        $.ajax({
           type: 'POST',
           url: v_url,
           sync: true,
           data:{rad_type:rad_type},
//           beforeSend: function()
//                                {
//                                    if(status_backup == 0)
//                                    {
//                                        $('#loading').show();
//                                        f.hdn_backup_status.value = 1;
//                                    }
//                                    else
//                                    {
//                                        return false; 
//                                    }
//                                },
           success: function(data){
                alert(data);
            }
        });
}

