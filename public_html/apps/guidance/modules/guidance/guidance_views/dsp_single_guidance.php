<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}
$arr_single_guidance = isset($arr_single_guidance[0]) ? $arr_single_guidance : array();
$v_id_linh_vuc   = isset($arr_single_guidance['PK_LIST']) ? $arr_single_guidance['PK_LIST'] : '';   
$v_name_linh_vuc = isset($arr_single_guidance['C_NAME_THU_TUC']) ? $arr_single_guidance['C_NAME_THU_TUC'] : '';  
$v_name          = isset($arr_single_guidance['C_NAME']) ? $arr_single_guidance['C_NAME'] : '';
$v_xml           = isset($arr_single_guidance['C_XML_DATA']) ? $arr_single_guidance['C_XML_DATA'] : '';
$this->template->title = 'Hướng dẫn thủ tục hành chính';
$this->template->template_is_metro = 'true';
$this->template->display('dsp_header.php');
?>
<!--Start #main-->
<div id="wrapper" ></div>
<div id="main" class="single">
    <!--Start .box-item-->
    <div class="box-item">
        <div class="single-item">
            <div class="title">
                    <h1 class="linhvuc"><?php echo $v_name_linh_vuc; ?></h1>
                 <h2 class="thutuc"><?php echo $v_name;?></h2>
            </div>
            <?php
            $r = array();
            if( $v_xml != '')
            {
                
                $dom        = simplexml_load_string($v_xml,'SimpleXMLElement',LIBXML_NOCDATA);
                $i = 0;
                $html ='';
                // Trình tự thực hiện
                $x_path                       = "//item[@id='txta_trinh_tu_thuc_hien']/value";
                $r_txta_trinh_tu_thuc_hien        = $dom->xpath($x_path);
                $v_txta_trinh_tu_thuc_hien        = (string)(isset($r_txta_trinh_tu_thuc_hien[0]) ? $r_txta_trinh_tu_thuc_hien[0] : '');
                if( $v_txta_trinh_tu_thuc_hien != '' && $v_txta_trinh_tu_thuc_hien != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span>'.($i += 1).',&nbsp;</span>Trình tự thực hiện:</h3>';
                    $html .= '<p class="content">'.  html_entity_decode((string)$r_txta_trinh_tu_thuc_hien[0]).'</hp>';
                }
                
                //Cánh thực hiện
                $x_path                       = "//item[@id='cach_thuc_thuc_hien']/value";
                $r_cach_thuc_thuc_hien        = $dom->xpath($x_path);
                $v_cach_thuc_thuc_hien        = (string)(isset($r_cach_thuc_thuc_hien[0]) ? $r_cach_thuc_thuc_hien[0] :'');
                if( $v_cach_thuc_thuc_hien != '' && $v_cach_thuc_thuc_hien != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span>'.($i += 1).',&nbsp;</span>Cách thực hiện:</h3>';
                    $html .= '<p class="content">'.html_entity_decode((string)$r_cach_thuc_thuc_hien[0]).'</hp>';
                }
                
                
                // Hồ sơ
                $x_path                       = "//item[@id='txta_ho_so']/value";
                $r_txta_ho_so        = $dom->xpath($x_path);
                $v_dtxta_ho_so        = (string)(isset($r_txta_ho_so[0])? $r_txta_ho_so[0] : '');
                if( $v_dtxta_ho_so != '' && $v_dtxta_ho_so != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span>'.($i += 1).',&nbsp;</span>Thành phần, số lượng hồ sơ:</h3>';
                    $html .= '<p class="content">'.html_entity_decode((string)$r_txta_ho_so[0]).'</hp>';
                }
                
                // Thời hạn giải quyết
                $x_path                       = "//item[@id='thoi_han_giai_quyet']/value";
                $r_thoi_han_giai_quyet        = $dom->xpath($x_path);
                $v_thoi_han_giai_quyet        = (string)(isset($r_thoi_han_giai_quyet[0])? $r_thoi_han_giai_quyet[0]: '');
                if( $v_thoi_han_giai_quyet != '' && $v_thoi_han_giai_quyet != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span>'.($i += 1).',&nbsp;</span>Thời hạn giải quyết:</h3>';
                    $html .= '<p class="content">'.html_entity_decode((string)$r_thoi_han_giai_quyet[0]).'</hp>';
                }
                
                // đối tượng thực hiện
                $x_path                       = "//item[@id='doi_tuong_thuc_hien']/value";
                $r_doi_tuong_thuc_hien        = $dom->xpath($x_path);
                $v_doi_tuong_thuc_hien        = (string)(isset($r_doi_tuong_thuc_hien[0]) ? $r_doi_tuong_thuc_hien[0] : '');
                if( $v_doi_tuong_thuc_hien != '' && $v_doi_tuong_thuc_hien != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span>'.($i += 1).',&nbsp;</span>Đối tượng thực hiện thủ tục hành chính:</h3>';
                    $html .= '<p class="content">'.html_entity_decode((string)$r_doi_tuong_thuc_hien[0]).'</hp>';
                }
                
                // Cơ quan thực hiện
                $x_path                       = "//item[@id='txta_co_quan_thuc_hien']/value";
                $r_txta_co_quan_thuc_hien       = $dom->xpath($x_path);
                $v_txta_co_quan_thuc_hien        = (string)(isset($r_txta_co_quan_thuc_hien[0]) ? $r_txta_co_quan_thuc_hien[0] : '');
                if( $v_txta_co_quan_thuc_hien != '' && $v_txta_co_quan_thuc_hien != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span>'.($i += 1).',&nbsp;</span>Cơ quan thực hiện TTHC:</h3>';
                    $html .= '<p class="content">'.html_entity_decode((string)$r_txta_co_quan_thuc_hien[0]).'</hp>';
                }
                
                // Kết quả
                $x_path                       = "//item[@id='txta_ket_qua']/value";
                $r_txta_ket_qua        = $dom->xpath($x_path);
                $v_txta_ket_qua        = (string)(isset($r_txta_ket_qua[0]) ? $r_txta_ket_qua[0] :'');
                if( $v_txta_ket_qua != '' && $v_txta_ket_qua != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span>'.($i += 1).',&nbsp;</span>Kết quả thực hiện thủ tục hành chính:</h3>';
                    $html .= '<p class="content">'.html_entity_decode((string)$r_txta_ket_qua[0]).'</hp>';
                }
                
                // Lệ phí
                $x_path                       = "//item[@id='le_phi']/value";
                $r_le_phi                     = $dom->xpath($x_path);
                $v_le_phi                     = (string)(isset($r_le_phi[0]) ? $r_le_phi[0] :'');
                if( $v_le_phi != '' && $v_le_phi != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span>'.($i += 1).',&nbsp;</span>Phí, lệ phí nếu có:</h3>';
                    $html .= '<p class="content">'.html_entity_decode((string)$r_le_phi[0]).'</hp>';
                }
                
                // Căn cứ pháp lý
                $x_path                       = "//item[@id='txta_can_cu_phap_ly']/value";
                $r_txta_can_cu_phap_ly        = $dom->xpath($x_path);
                $v_txta_can_cu_phap_ly        = (string)(isset($r_txta_can_cu_phap_ly[0]) ? $r_txta_can_cu_phap_ly[0]  :'');
                if( $v_txta_can_cu_phap_ly != '' && $v_txta_can_cu_phap_ly != NULL)
                {
                    $html .= '<h3 class="thutuc-title"><span>'.($i += 1).',&nbsp;</span>Căn cứ pháp lý</h3>';
                    $html .= '<p class="content">'.html_entity_decode((string)$r_txta_can_cu_phap_ly[0]).'</hp>';
                }
                if($i == 0)
                {
                    $html .= '<p>Thủ tục này chưa được cập nhật thông tin</p>'; 
                }
                echo $html;
                
            }
            ?>
            </div>
        </div>
        <!--End .single-item-->
        <div class="last">
            <div class="prev-top">
                <?php
                   $page           =isset($_GET['page'])? '?page='.$_GET['page'] :'';
                   $v_url_all_list =    $this->get_controller_url().'dsp_all_list_guidance/'.$v_id_linh_vuc.'/'.$page;
                ?>
                <a class="prev" href="<?php echo $v_url_all_list; ?>">Trang trước</a>
                <a class="back-home" href="<?php echo $this->get_controller_url(); ?>">Giao diện chính</a>
                <a class="top" href="#">Lên trên</a>
            </div>
        </div> 
    </div>
    <!--End .box-item-->

</div>
<!--End #main-->
<?
$this->template->display('dsp_footer.php');
?>