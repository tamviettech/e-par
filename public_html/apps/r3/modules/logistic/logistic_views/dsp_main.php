<?php
defined('SERVER_ROOT') or die();
/**
 * @package logistic
 * @author Tam Viet <ltbinh@gmail.com>
 * @author Duong Tuan Anh <goat91@gmail.com>
 */
$this->template->title = 'Giám sát hoạt động tại hệ thống';
$this->template->display('dsp_header_pop_win.php');
echo $this->hidden('controller', $this->get_controller_url());
echo $this->hidden('hdn_data_source', 'dsp_new_actions');
?>
<style>
    body{
        background-color: black !important;
    }
    #data_wrap{
        color: white;
        font-family: "Courier New";
        font-size: 12px;
        background: black;
    }
</style>
<div id="data_wrap">
    <div id="data">
        <span><b>[<?php echo date('d-m-Y H:i') ?>]<?php echo Session::get('user_name') ?>:</b>Bắt đầu theo dõi</span><br/>
    </div>
</div>
<script>
    $(document).ready(function() {
        append_data();
        setInterval(append_data, 10000);
    });

    function append_data() {
        $.ajax({
            url: $('#controller').val() + $('#hdn_data_source').val(),
            cache: false,
            success: function(html) {
                $('#data').append(html);
                $(window).scrollTop($('#data').height());
            }
        });
    }
</script>
<?php
$this->template->display('dsp_footer_pop_win.php');

