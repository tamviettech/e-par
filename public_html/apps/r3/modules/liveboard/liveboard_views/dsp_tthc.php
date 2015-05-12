<?php
defined('DS') or die;
/* @var $this \View */
$this->template->title = 'Danh mục thủ tục hành chính';
$this->template->display('dsp_header_pop_win.php');
?>
<style>
    #container{padding:0px;margin:0px}
    body { overflow-x: hidden;}
</style>
<script type="text/javascript" src="<?php echo SITE_ROOT ?>public/js/wz_tooltip.js"></script>
<script type="text/javascript">
    function build_col_width() {
        var html = [];
        var arr_cw = new Array();
        var browser = navigator.appName;
        if (browser === "Microsoft Internet Explorer") {
            for (var i = 0; i <= 100; i++)
            {
                arr_cw[i] = i + '%';
            }
        } else {
            var v_screen_width = screen.width;
            for (var i = 0; i <= 100; i++)
            {
                arr_cw[i] = (Math.round(i * v_screen_width / 100)) + 'px';
            }
        }

        i = -1;
        html[++i] = '<col width="' + arr_cw[10] + '"/>';//TT
        html[++i] = '<col width="' + arr_cw[10] + '"/>';//code
        html[++i] = '<col width="' + arr_cw[70] + '"/>';//name
        html[++i] = '<col width="' + arr_cw[10] + '"/>';//time
        return html.join('');
    }
</script>

<div id="liveboard">
    <div class="center">
        <div id="quoc-huy">
            <img src="<?php echo SITE_ROOT; ?>public/images/12033_quoc_huy.png" height="60" />
        </div>
        <?php
        $ou_name               = get_xml_value(simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml'), '//full_name');
        ?>
        <label id="unit_full_name"><?php echo $ou_name ?></label>
        <h5>Danh mục thủ tục hành chính</h5>
    </div>
    <table width="100%" id="th-only" class="adminlist table table-bordered table-striped">

        <script type="text/javascript">
            document.write(build_col_width());
        </script>


        <thead>
            <tr>
                <th>STT</th>
                <th>Mã thủ tục</th>
                <th>Tên thủ tục</th>
                <th>Số ngày giải quyết</th>
            </tr>
            </head>
    </table>
    <div class="detail_table">
        <table width="100%" class="adminlist table table-bordered table-striped">

            <script type="text/javascript">
                document.write(build_col_width());
            </script>

            <tbody id="thu_tuc">
                <?php array_walk($arr_all_type, 'tthc_helper::dsp_tr') ?>
            </tbody>
        </table>

    </div>
    <div style="position: fixed;bottom: 0; right: 0;background-color: #C0C0C0;opacity: 0.9">
        <label><input type="checkbox" name="chk_scroll" id="chk_scroll" onclick="chk_scroll_onclick(this)" />Cuộn</label> 
        (<u>Chú ý</u>: Di chuột vào mã thủ tục để xem tên thủ tục)
    </div>

</div>
<?php

class tthc_helper
{

    static protected $_index = 1;

    static function dsp_tr($row)
    {
        ?>
        <tr>
            <td><?php echo static::$_index++ ?></td>
            <td><?php echo $row['C_CODE'] ?></td>
            <td style='text-align:left'><?php echo $row['C_NAME'] ?></td>
            <td><?php echo $row['C_TIME'] ?></td>
        </tr>
        <?php
    }

}
?>
<script>
                //Scroll
                var scrollingboxes = $('div.detail_table');
                var sct = 0, limit = 0, step = 1;
                $(scrollingboxes).scrollTop(0);
                var refreshIntervalId;

                function scroll() {
                    var sct = {};
                    var limit = {};
                    $.each(scrollingboxes, function(index, scrollingbox) {
                        sct[index] = $(scrollingbox).scrollTop();
                        limit[index] += step;
                        $(scrollingbox).scrollTop(sct[index] + step);
                        if ($(scrollingbox).scrollTop() < sct[index] + step) {
                            sct[index] = 0;
                            limit[index] = 0;
                            $(scrollingbox).scrollTop(0);
                        }
                    });
                    $('.liveboard').width('100%');
                }

                function stop_scroll()
                {
                    $.each(scrollingboxes, function(index, scrollingbox) {
                        $(scrollingbox).scrollTop(0);
                        $(scrollingbox).css('overflow', "auto");
                    });
                    refreshIntervalId = clearInterval(refreshIntervalId);
                }

                function do_srcoll() {
                    scrollingboxes = $('div.detail_table');
                    $.each(scrollingboxes, function(index, scrollingbox) {
                        //reset
                        sct[index] = 0;
                        limit[index] = 0;
                        step = 1;
                        $(scrollingbox).css('overflow', "hidden");
                        $(scrollingbox).scrollTop(0);
                    });
                    scroll();
                    refreshIntervalId = setInterval('scroll()', 100);
                    $('tbody').css('width', '100%');
                }

                function chk_scroll_onclick(obj)
                {
                    if (obj.checked == true)
                    {
                        do_srcoll();
                    }
                    else
                    {
                        stop_scroll();
                    }
                }

                $(document).ready(function() {
                    $("body").css("overflow-x", "hidden");
                    $("body").css("background-color", "#FCFCFC");

                    remain_height = $(window).height() - 10 - $('#th-only').offset().top - $('#th-only').height();
                    count_table = $('.detail_table').length;
                    $('.detail_table').height(remain_height / count_table);
                    $("#chk_scroll").attr('checked', 'true');
                    
                    do_srcoll();
                });
</script>
<?php
$this->template->display('dsp_footer_pop_win.php');