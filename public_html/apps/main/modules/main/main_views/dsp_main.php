<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>

<?php $this->template->display('dsp_header.php'); ?>
<div class="container-fluid" style="padding: 20px">
    <style>
        .switch-item li,
        .switch-item li a
        {
            width: 150px !important;
            height: 150px !important;
        }
        .app:hover{

        }
        .app:hover .green{
            background-color: rgba(0, 163, 0, 0.5);
        }
        .app:hover .board-widgets-head{
            background-color: rgba(245, 245, 245, 0.5);
        }
    </style>
    <div class="row-fluid" style="text-align: center">
        <?php if (Session::get('is_admin') == 1): ?>
            <?php $v_url = SITE_ROOT . 'cores/application/'; ?>
            <div class="span3 app">
                <div class="board-widgets green">
                    <div class="board-widgets-head clearfix">
                        <h4 class="pull-left"><i class="icon-desktop"></i> Quản trị hệ thống</h4>
                        <a href="<?php echo $v_url; ?>" class="widget-settings"><i class="icon-cog"></i></a>
                    </div>
                    <div class="board-widgets-content">
                        <a href="<?php echo $v_url; ?>">
                            <span class="n-sources">Quản trị hệ thống</span>
                        </a>
                    </div>
                    <div class="board-widgets-botttom">
                        <a href="<?php echo $v_url; ?>">
                            Truy câp Quản trị hệ thống<i class="icon-double-angle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php $i = 1; ?>
        <?php foreach ($arr_all_application as $app): ?>
            <?php
            $v_url = SITE_ROOT . strtolower($app['C_CODE'] . '/' . $app['C_DEFAULT_MODULE']);
            $span_style = ($i % 4 == 0) ? 'margin-left: 0px;' : '';
            ?>
            <div class="span3 app" style="<?php echo $span_style ?>">
                <div class="board-widgets green">
                    <div class="board-widgets-head clearfix">
                        <h4 class="pull-left"><i class="icon-desktop"></i> <?php echo $app['C_CODE']; ?></h4>
                    </div>
                    <div class="board-widgets-content">
                        <a href="<?php echo $v_url; ?>">
                            <span class="n-sources"><?php echo $app['C_NAME']; ?></span>
                        </a>
                    </div>
                    <div class="board-widgets-botttom">
                        <a href="<?php echo $v_url; ?>">
                            Truy cập phần mềm <?php echo $app['C_CODE']; ?><i class="icon-double-angle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php $i++; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php
$this->template->display('dsp_footer.php');
