<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

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

<?php
defined('SERVER_ROOT') or die();

$this->template->title = $arr_single_book['c_name'];
$this->template->display('dsp_header.php');
?>
<style>
  
</style>
<div style="width:70%;margin:0 auto;">
    <h3 class="page-title"><?php echo $arr_single_book['c_name'] ?></h3>
    <?php
    $v_download_url        = $this->get_controller_url()
            . 'export_book/' . $book_id
            . "&begin_date=$begin_date"
            . "&end_date=$end_date"
            . "&file_type=$file_type"
            . "&request_type=download";

    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_svc_is_ready_method', 'svc_is_download_ready');
    echo $this->hidden('hdn_temp_name', $temp_name);
    echo $this->hidden('hdn_download_url', $v_download_url);

    $arr_ext      = array('cvs' => 'CVS', 'xls' => 'Microsoft Excel 2003', 'pdf' => 'PDF document');
    $arr_est_time = array('cvs' => 'Nhanh', 'xls' => 'Nhanh', 'pdf' => 'Khá lâu');
    ?>
    <table width="100%" class="adminlist" cellspacing="0" cellpading="0" border="1">
        <colgroup>

        </colgroup>
        <thead>
            <tr>
                <th>Từ ngày</th>
                <th>Đến ngày</th>
                <th>Loại File</th>
                <th>Thời gian xử lý</th>
            </tr>
        </thead>
        <tbody>
            <tr class="row1">
                <td class="center"><?php echo $begin_date ?></td>
                <td class="center"><?php echo $end_date ?></td>
                <td><?php echo $arr_ext[$file_type] ?></td>
                <td><?php echo $arr_est_time[$file_type] ?></td>
            </tr>
        </tbody>
    </table>
    <?php if (!isset($error)): ?>
        <label class="required">Đang xử lý yêu cầu, vui lòng chờ...</label>
        <div class="process wraper">
            <div class="percent-outter">
                <div class="percent-inner" style="width: 0%;"></div>
            </div>
            <div class="percent-text">0%</div>
        </div>
    <?php else: ?>
        <label class="required"><?php echo $error ?></label>
    <?php endif; ?>
    <h4></h4>
</div>
<script>
    window.timeout = null;
    window.process = 0;
    function download_ready() {
        check_url = $('#controller').val()
                + $('#hdn_svc_is_ready_method').val()
                + '/' + $('#hdn_temp_name').val();
        process_interval = 1;
        $.ajax({
            type: 'get',
            cache: false,
            url: check_url,
            dataType: 'json',
            timeout: 2000,
            success: function(result) {
                if (result == true) {
                    window.timeout = window.clearTimeout(window.timeout);
                    window.location = $('#hdn_download_url').val();
                    window.process = 100;
                    $('.percent-inner').css({width: window.process + '%'});
                    $('.percent-text').html(window.process + '%');
                } else {
                    window.timeout = window.setTimeout(download_ready, 10000);
                }
            },
            error: function() {
                if (window.process < 91) {
                    if (window.process < 91) {
                        window.process += 1;
                    }
                }
                $('.percent-inner').css({width: window.process + '%'});
                $('.percent-text').html(window.process + '%');
                window.timeout = window.setTimeout(download_ready, 10000);
            }
        });


    }

    $(document).ready(function() {
        $('.process').each(function() {
            process_width = $(this).width();
            percent_text = $(this).find('.percent-text');
            $(percent_text).css('left', (process_width - $(percent_text).width()) / 2);
        });
        window.timeout = window.setTimeout(download_ready, 1000);
    });
</script>
<?php
$this->template->display('dsp_footer.php');