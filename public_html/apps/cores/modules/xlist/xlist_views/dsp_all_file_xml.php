<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Cập nhật loại danh mục</title>
        <style type="text/css" type="text/css" media="screen" rel="stylesheet" />
            ul.xml_file_list{
                list-style-image: url('<?php echo SITE_ROOT;?>public/images/xml.gif');                
            }
            ul.xml_file_list a{
                text-decoration:none;
            }
            ul.xml_file_list a:hover{
                text-decoration:underline;
            }            
        </style>
        <script type="text/javascript">
            function file_onclick(file_name){
                window.parent.document.frmMain.txt_xml_file_name.value = file_name;
                try{window.parent.hidePopWin();}catch(e){window.close();};
            }
        </script>
    </head>
    <body>
    <h2>Chọn file:</h2>	
    <ul class="xml_file_list">
        <?php
        $dir = __DIR__ . DS . 'xml';
        if ($handle = opendir($dir)) {
            while (($file_name = readdir($handle)) != false) {
                $_file = $dir . DS . $file_name;
                if ($_file != '.' && $_file != '..' && is_file($_file)) {
                    $filetype = substr($_file, strlen($_file) - 4, 4);
                    if (strtolower($filetype) == '.xml') {
                        echo '<li><a href="#" onclick="file_onclick(\'' . $file_name . '\')">' . $file_name . '</a></li>';
                    }
                }
            }

            closedir($handle);
        }
        ?>
    </ul>
    </body></html>