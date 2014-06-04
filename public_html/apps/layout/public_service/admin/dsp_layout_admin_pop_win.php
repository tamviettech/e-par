<?php
/**


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
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
?>
<!DOCTYPE html>
<html ng-app>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <title>Dịch vụ công::<?php echo isset($this->title) ? $this->title : ''; ?></title>

        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/admin/style.css" type="text/css" media="screen" />

        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui-1.8.18.css" rel="stylesheet" type="text/css"/>
        <!--  Datepicker -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.ui.datepicker-vi.js" type="text/javascript"></script>

        <!-- Right-click context menu -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.contextMenu.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.contextMenu.css" rel="stylesheet" type="text/css"/>

        <!-- Upload -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MultiFile.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MetaData.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
        <!--combobox-->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquerycombobox/js/jquery.combobox.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquerycombobox/css/style.css" rel="stylesheet" type="text/css">
        
        <script type="text/javascript">
            var SITE_ROOT='<?php echo SITE_ROOT; ?>';
            var _CONST_LIST_DELIM = '<?php echo _CONST_LIST_DELIM; ?>';
        </script>
        <!--  Modal dialog -->
        <script src="<?php echo SITE_ROOT; ?>public/js/submodal.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/css/subModal.css" rel="stylesheet" type="text/css"/>

        <!-- Tooltip -->
        <script src="<?php echo SITE_ROOT; ?>public/js/overlib_mini.js" type="text/javascript"></script>

        <script src="<?php echo SITE_ROOT; ?>public/js/mylibs.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/DynamicFormHelper.js" type="text/javascript"></script>
        <?php if (isset($this->local_js)): ?>
            <!-- Module JS -->
            <script src="<?php echo $this->local_js; ?>" type="text/javascript"></script>
              <!--Grild-css-->
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/1008_24_0_0.css" type="text/css" media="screen" />  
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/layout/public_service/admin/report.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/layout/public_service/admin/style.css" type="text/css" media="screen" />        
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/layout/public_service/admin/style_mobile.css" type="text/css" media="screen" />
        <!--[if IE]>
            <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/layout/public_service/admin/style_ie.css" type="text/css" media="screen" />
        <![endif]-->
        <script src="<?php echo FULL_SITE_ROOT . 'public/js/public_service/functions.js'?>"></script>
        <style>
            #filemenu a
            {
                padding: 0;
            }
        </style>
<?php endif; ?>
    </head>
    <body>
        <DIV id=overDiv style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></DIV>
        <div id="container">
            <div id="content">
                <?php echo isset($content) ? $content : ''; ?>
            </div>
    </body>
</html>