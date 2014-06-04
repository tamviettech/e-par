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
<!DOCTYPE html>
<html lang="vi" class="win chrome webkit">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">		 

        <meta name="viewport" content="width=device-width,initial-scale=1.00, minimum-scale=1.00">    
        <meta name="viewport" content="width=320, target-densitydpi=150, initial-scale=0.999 , user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?php echo $this->stylesheet_url; ?>" type="text/css" media="screen" />
        <script src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/jquery.min.js"></script>
        <style>
            #headerWrapper
            {
                padding-bottom: 0px;
                margin-left: -4px;
                margin-right: -4px;
            }
        </style>
            <!--[if IE]>
            <link rel="stylesheet" href="<?php echo $this->get_apps_url; ?>ie.css" type="text/css" media="screen"  />
                <style>
                    #headerWrapper
                    {
                        position:  inherit !important;
                        padding-left:0;
                        padding-right:0;
                        margin-left:-1px;
                        margin-right:-1px;
                    }
                    #headerWrapper h2
                    {
                        color:white;
                        font-size: 1.3em;
                    }
                    #main.all-list
                    {
                        margin-right:6px;
                    }
                    #list-detail-footer
                    {
                        margin:0;
                        margin-right:4px;
                    }
                    #list-detail-footer .last .prev-next a
                    {
                        padding-bottom:1px;
                        padding-top:36px;
                    }
                    #list-detail-footer img
                    {
                        margin-left:0;
                    }
            </style>
        <![endif]-->
    </head>
    <body class="list-detail-list" style="">
        <div id="wrp-all">
        <header>
            <div id="headerWrapper">
                <!--<div class="boder-left"></div>-->
                    <img src="<?php echo SITE_ROOT . 'apps/guidance/' ?>images/header-bg-list.png" width="100%" />    
                    <!--<h2 class="title">ỦY BAN NHÂN DÂN HUYỆN YÊN BÁI</h2>-->       
                <!--<div class="border-right"></div>-->
            </div>
        </header>
        <!--End #header-->
        