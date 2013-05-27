<?php 
/**
// File name   : dsp_header_pop_win.php
// Version     : 1.0.0.1
// Begin       : 2012-12-01
// Last Update : 2010-12-25
// Author      : TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
//Copyright (C) 2012-2013  TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn

// E-PAR is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// E-PAR is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// See LICENSE.TXT file for more information.
*/
?>
<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <title>Go-Office::<?php echo $this->eprint($this->title); ?></title>

        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo $this->stylesheet_url;?>" type="text/css" media="screen" />
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <!--  Datepicker -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.ui.datepicker-vi.js" type="text/javascript"></script>

        <!-- Right-click context menu -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.contextMenu.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.contextMenu.css" rel="stylesheet" type="text/css"/>

        <!-- Upload -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MultiFile.js" type="text/javascript"></script>

        <script type="text/javascript">
            var SITE_ROOT='<?php echo SITE_ROOT;?>';
            var _CONST_LIST_DELIM = '<?php echo _CONST_LIST_DELIM;?>';
        </script>
        <!--  Modal dialog -->
        <script src="<?php echo SITE_ROOT; ?>public/js/submodal.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/css/subModal.css" rel="stylesheet" type="text/css"/>

        <!-- Tooltip -->
        <script src="<?php echo SITE_ROOT; ?>public/js/overlib_mini.js" type="text/javascript"></script>

        <script src="<?php echo SITE_ROOT; ?>public/js/mylibs.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/DynamicFormHelper.js" type="text/javascript"></script>

        <?php if (isset($this->local_js)):?>
            <script src="<?php echo $this->local_js;?>" type="text/javascript"></script>
        <?php endif;?>
    </head>
    <body style="background-color: #FFF">
        <DIV id=overDiv style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></DIV>
        <div id="container">
            <div id="content">