<script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js"></script>
<style>
    @media print
    {
        input[type="button"]
        {
            display: none;
        }
    }
</style>
<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}
//lay record type
$v_record_type_code = $arr_single_record['C_RECORD_TYPE_CODE'];

$v_assistance_form_dir = get_request_var('tpl_file_dir','');

$v_assistance_form_dir = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS. $v_record_type_code . DS . $v_assistance_form_dir;
$full_path = str_replace('\\', '/', $v_assistance_form_dir);
$html = '';

if (file_exists($full_path))
{
    $this->xslt = new Xslt();
    $this->xslt->setXmlString($arr_single_record['C_XML_DATA']);

//    $v_xsl_string = file_get_contents(SERVER_ROOT . 'libs' .DS . 'transform_to_print_form.xslt');
    $v_xsl_string = file_get_contents($full_path);
   
    $this->xslt->setXslString($v_xsl_string);
    $this->xslt->setParameter(array(
                    'p_site_root'                  => SITE_ROOT
                    ,'p_server_root'               => str_replace('\\', '/', SERVER_ROOT)
                    , 'p_current_date'             => date('d-m-Y')
                    , 'p_form_struct_file_name'    => $full_path
                    , 'p_day'                      => date('d')
                    , 'p_month'                    => date('m')
                    , 'p_year'                     => date('Y')
                    //current url => in mau bao cao ra doc
//                    , 'p_url_assistance_form_to_doc' => $url_assistance_for_to_doc
    ));
    if ($this->xslt->transform()) {
        $html .= $this->xslt->getOutput();
        $this->xslt->destroy();
    }
    
    //yeu cau in file doc
    $print_assistant_form_to_doc = get_request_var('print_to_doc','0');
    
    if($print_assistant_form_to_doc == '1')
    {
        //lay html 
        $html_value = get_post_var('hdn_html','');
        $html_value = html_entity_decode($html_value);
        
        //include thu vien html_to_doc
        $library_dir = SERVER_ROOT . 'libs/html_to_doc.inc.php';
        include($library_dir);
        
        //tao doi tuong 
        $htmltodoc = new HTML_TO_DOC();
        
        //Thiep lap ten file default
        $file_name_save = session::get('user_name').'ho_tro_thu_ly';
        //tao file doc
        $htmltodoc->createDoc($html_value,$file_name_save,true);
    }
    //hien thi in html 
    else 
    {
        echo $html;
    }
}
else
{
     die('Không có mẫu hỗ trợ thụ lý !!!');
}


/*
// create new PDF document
$v_layout = strtoupper('P');
$pdf      = new ZREPORT($v_layout, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Ngo Duc Lien');

// set header and footer fonts
$pdf->setPrintHeader(0);
$pdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 023', '');
//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, 'B', 16));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', 13));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------
// add a page
$pdf->AddPage($v_layout);

$pdf->SetFont('liennd.times', '', 11);



$pdf->writeHtmlReport($html);
$pdf->lastPage();

//Change To Avoid the PDF Error
@ob_end_clean();
//Close and output PDF document
$v_attach_file_path = 'in_don.pdf';
//$pdf->Output($v_attach_file_path, 'I');
*/?>
<script>
    function hidde_button_onclik()
    {
        $('.print-button').hide();
    }

    function assistance_print_onclick()
    {
        //gan gia tri thay doi vao textbox
        $('input[type="textbox"]').each(function(){
            new_html = '';
            new_html = '<label>' + $(this).val() + '</label>';
            $(this).after(new_html);
            $(this).remove();
        });
        
        var html_value = $('html').html();
        html_value = '<html>' + html_value + '</html>';
        
        $('#hdn_html').val(html_value);
        $('#print_to_doc').val(1);
        $('#frmMain').submit();
    }
</script>