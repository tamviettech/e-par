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
defined('DS') or die();

$finfo     = pathinfo($fname);
$extension = strtolower($finfo['extension']);
$img_ext   = explode(',', str_replace(' ', '', strtolower(EXT_IMAGE)));
if (in_array($extension, $img_ext))
{
    $img = $furl;
}
else
{
    $img     = SITE_ROOT . "public/images/ext/$extension.png";
    $imgpath = SERVER_ROOT . "public" . DS . "images" . DS . "ext" . DS . "$extension.png";
    if (!file_exists($imgpath))
    {
        $img = SITE_ROOT . "public/images/ext/file.png";
    }
}
?>
<img style="max-height: 98%;max-width: 100%;" src="<?php echo $img ?>"/><br/>



