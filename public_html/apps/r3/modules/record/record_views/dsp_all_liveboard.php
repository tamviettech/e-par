<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');
/* @var $this \View */
//header
$this->template->title = 'Theo dõi trực tuyến';
$this->template->display('dsp_header.php');
?>

<div class="tab-widget" >
    <ul class="nav nav-tabs" id="myTab1" >
        <li class="active"><a href="#supplement_0">Biểu đồ tiến độ xử lý hồ sơ</a></li>
        <li><a href="#supplement_1">Bảng theo dõi trực tuyến </a></li>
        <?php if(session::get('is_admin') == '1'):?>
        <li><a href="#logistic">Theo dõi hoạt động người dùng</a></li>
        <?php endif;?>
    </ul>
    <div class="tab-content">
        <div id="supplement_0" class="tab-pane active">
            <iframe name="ifr_supplement_0" id="ifr_supplement_0"
                    src="<?php echo SITE_ROOT?>r3/chart"
                    width="100%" style="min-height:620px;overflow-y: scroll;border:0;background-color: white"></iframe>
        </div>
        <div id="supplement_1" class="tab-pane">
            <form name="frmMain" id="frmMain" action="" method="POST">
                <iframe src="<?php echo SITE_ROOT?>r3/liveboard" style="width:100%;height:620px; border:0; background-color: white;overflow: scroll;"></iframe>
            </form>
        </div>
        <?php if(session::get('is_admin') == '1'):?>
        <div id="logistic" class="tab-pane">
            <form name="frmMain" id="frmMain" action="" method="POST">
                <iframe src="<?php echo SITE_ROOT?>r3/logistic" style="width:100%;height:620px; border:0; background-color: white;overflow: scroll;"></iframe>
            </form>
        </div>
        <?php endif;?>
    </div>
</div>
<div class="clear">&nbsp;</div>

<?php
$this->template->display('dsp_footer.php');