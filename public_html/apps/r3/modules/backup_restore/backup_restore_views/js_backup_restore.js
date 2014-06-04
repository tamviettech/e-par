

/**
 * Show theme backup
 */
function onclick_backup() 
{
    var f= document.forms.frmMain;
    f.hdn_status_tab.value = '1';
    f.submit();

}
/**
 * Hiển thị tất các tập tin trong thư mục
 */
function onclick_show_all_item() 
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
function start_backup() 
{
     
        var f   = document.forms.frmMain;
        status_backup = f.hdn_backup_status.value;
        var v_url = f.controller.value + f.hdn_backup_method.value;
        $.ajax({
           url: v_url,
           sync: true,
           beforeSend: function()
                                {
                                    if(status_backup == 0)
                                    {
                                        $('#loading').show();
                                        f.hdn_backup_status.value = 1;
                                    }
                                    else
                                    {
                                        return false; 
                                    }
                                },
           success: function(data){
                alert(data);
                f.hdn_backup_status.value  = 0;
                $('#loading').hide();
                window.location.href = f.controller.value;
            }
        });
}

