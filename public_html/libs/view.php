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
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');?>
<?php

require_once 'class_xslt.php';

class View{

    protected $xml_file_name;
    protected $app_name = '';
    protected $module_name = '';
    protected $xslt;
    protected $dom;
    protected $template_directory;

    public $msg;
    /** @var \Savant3 */
    public $template;
    
    public $url_layout;

    function __construct($app, $module) {
        $this->app_name = $app;
        $this->module_name = $module;
        $this->template_directory = SITE_ROOT . 'apps/' . $app . '/';
        $this->image_directory = $this->template_directory . 'images/';

        //Savant3
        $this->template = new Savant3();
        //load local javascript
        $this->local_js = $this->template->local_js = SITE_ROOT . 'apps/' . $app . '/modules/' . $module . '/' . $module . '_views/js_' . $module . '.js';
        $this->template->controller_url = $this->get_controller_url();
        $this->template->function_url = $this->template->controller_url;

        $this->template->template_directory = 'apps' . DS . $app . DS;
        $this->stylesheet_url = $this->template->stylesheet_url = SITE_ROOT . 'apps/' . $app . '/style.css';
        
        $this->url_layout = SERVER_ROOT . 'apps' .DS. 'layout'.DS;
    }

    private function _render_error($code)
    {
        switch ($code)
        {
            case 1:
                die('Lỗi view render, không tìm thấy file view');
                break;
        }
    }

    public function check_permission($function_code)
    {
        $app_name = strtoupper($this->app_name);
        $function_code =  $app_name . '::' . $function_code;
        return in_array($function_code, Session::get('arr_function_code'));
    }

    public function get_controller_url($module=NULL, $app=NULL)
    {
        if (empty($app))
        {
            $app = $this->app_name;
        }
        if (empty($module))
        {
            $module = $this->module_name;
        }

        if (file_exists('.htaccess'))
        {
            return SITE_ROOT . $app . '/' . $module . '/';
        }
        return SITE_ROOT . 'index.php?url=' . $app . '/' . $module . '/';
    }

    public function render($name, $VIEW_DATA=array())
    {
        $v_view_file = SERVER_ROOT . 'apps' .DS . $this->app_name . DS . 'modules'.DS . $this->module_name . DS . $this->module_name . '_views'. DS . $name . '.php';
        if (file_exists($v_view_file))
        {
            //Tự động sinh các biến cho VIEW_DATA
            if (is_array($VIEW_DATA))
            {
                foreach ($VIEW_DATA as $key => $val)
                {
                    $$key = $val;
                }
            }
            require $v_view_file;
        }
        else
        {
            $this->_render_error(1);
        }

    }

    public function load_xml($xml_file_name) {
        $this->xml_file_name = 'apps' .DS . $this->app_name . DS.'modules'.DS . $this->module_name . DS . $this->module_name . '_views'.DS. 'xml' .DS . $xml_file_name;

        if (!file_exists(SERVER_ROOT . $this->xml_file_name))
        {
            return FALSE;
        }

        $this->xslt = new Xslt();
        $this->xslt->setXml($this->xml_file_name);
        $this->dom = simplexml_load_file($this->xml_file_name);

        return TRUE;
    }

    public function load_abs_xml($abs_file_path)
    {
        if (!file_exists($abs_file_path))
        {
            return FALSE;
        }
        $this->xslt = new Xslt();
        $this->xslt->setXml($abs_file_path);
        $this->dom = simplexml_load_file($abs_file_path);

        return TRUE;
    }

    public function transform($full_path)
    {
        if (!file_exists($full_path)){
            return '';
        }

        $this->xslt = new Xslt();
        $this->xslt->setXml($full_path);
        $html = '';
        $this->xslt->setXslString(file_get_contents(SERVER_ROOT . 'libs' .DS . 'Transform.xslt'));
        $this->xslt->setParameter(array('p_site_root' => FULL_SITE_ROOT, 'p_current_date' => date('d-m-Y')));
        if ($this->xslt->transform()) {
            $html = $this->xslt->getOutput();
            $this->xslt->destroy();
        }
        return $html;

    }

    public function render_form_display_single()
    {
        $html = '';
        $this->xslt->setXslString(file_get_contents(SERVER_ROOT . 'libs' .DS. 'Transform.xslt'));
        $this->xslt->setParameter(array('p_site_root' => FULL_SITE_ROOT, 'p_current_date' => date('d-m-Y')));
        if ($this->xslt->transform()) {
            $html = $this->xslt->getOutput();
            $this->xslt->destroy();
        }
        return $html;
    }

    public function render_form_display_all($data, $edit=TRUE) {
        $html = '';

        $arr_status = array('0'=>__('inactive status'),'1'=>__('active status'));

        $p = $this->dom->xpath("//display_all/list/item[@type = 'primarykey']/@id");
        $primarykey = strval($p[0]);

        $cols = $this->dom->xpath("//display_all/list/item[@type != 'primarykey']");

        //List header
        $table_col_size = $table_header = '';
        foreach ($cols as $col) {
            $table_col_size .= '<col width="' . $col->attributes()->size . '" />';
            if (strval($col->attributes()->type != 'checkbox'))
            {
                $table_header .= '<th class="center">' . __($col->attributes()->name) . '</th>';
            }
            else
            {
               if (sizeof($data) > 0)
               {
        	       $table_header .= '<th><input type="checkbox" name="chk_check_all" onclick="toggle_check_all(this,this.form.chk);"/></th>';
        	   }
        	   else
        	   {
                    $table_header .= '<th>&nbsp;</th>';
               }
            }
        }
        $html .= '<table width="100%" class="adminlist table table-bordered table-striped" cellspacing="0" border="1">' . $table_col_size
                . '<tr>' . $table_header . '</tr></table>';

        //List item
        $html = '<table width="100%" class="table table-bordered table-striped">';
        $html .= $table_col_size;
        $html .= '<thead><tr>' . $table_header . '</tr></thead>';
        $i = 0;
        for ($i = 0; $i < sizeof($data); $i++) {
            $v_row_class = 'row' . ($i % 2);
            $html .= '<tr class="' . $v_row_class . '" height="20" role="presentation" data-item_id="' . $data[$i][$primarykey] . '">';

            if (isset($data[$i]['C_XML_DATA']))
            {
                $v_xml_data             = $data[$i]['C_XML_DATA'];
                $dom_xml_data           = simplexml_load_string($v_xml_data);
            }
            else
            {
                $dom_xml_data = NULL;
            }

            reset($cols);
            foreach ($cols as $col) {
                $index = strval($col->attributes()->id);
                $v_clickable = strval($col->attributes()->clickable);
                switch (strval($col->attributes()->type)) {

                    case 'checkbox':
                        $html .= '<td class="center"><input type="checkbox" name="chk"
                            value="' . $data[$i][$primarykey] . '"
                            onclick="if (!this.checked) this.form.chk_check_all.checked=false;" /></td>';
                        break;

                    case 'moving':
                        $html .= '<td>#</td>';
                        break;

                    case 'order':
                        $html .= '<td class="center">' . $data[$i][$index] . '</td>';
                        break;

                    case 'status':
                        $html .= '<td class="center">' . $arr_status[$data[$i][$index]] . '</td>';
                        break;

                    case 'action':
                        $html .= '<td role="action"><div class="quick_action" data-item_id="' . $data[$i][$primarykey] . '">&nbsp;</div></td>';
                        break;

                    case 'text':
                    default:
                        if (strpos($index , 'xml/') !== FALSE) //Cot du lieu nam trong XML
                        {
                            $index = str_replace('xml/','',$index);
                            $d = xpath($dom_xml_data, "/data/item[@id='$index']/value", XPATH_DOM);
                            if (!$edit OR $v_clickable == 'no')
                            {
                                $html .= '<td>' . $d . '</td>';
                            }
                            else
                            {
                                $html .= '<td><a href="javascript:void(0)" onclick="row_onclick(\'' . $data[$i][$primarykey] . '\')">' . $d . '</a></td>';
                            }
                        }
                        else //Cot tuong minh
                        {
                            if (!$edit OR $v_clickable == 'no')
                            {
                                $text = isset($data[$i][$index]) ? $data[$i][$index] : '';
                                $html .= '<td>' . $text . '</td>';
                            }
                            else
                            {
                                $text = isset($data[$i][$index]) ? $data[$i][$index] : '';
                                $html .= '<td><a href="javascript:void(0)" onclick="row_onclick(\'' . $data[$i][$primarykey] . '\')">' . $text. '</a></td>';
                            }
                        }
                        break;
                }
            }
            $html .= '</tr>';
        }

        $rows_per_page = get_post_var('sel_rows_per_page', _CONST_DEFAULT_ROWS_PER_PAGE);
        $html .= $this->add_empty_rows($i+1, $rows_per_page, count($cols));
        $html .= '</table>';

        return $html;
    }

    /*===================================================================================================*/
    //HTML Helper
    public static function hidden($name, $value='') {
        if (strpos($value, '"') !== FALSE) {
            return '<input type="hidden" name="' . $name . '" id="' . $name . '" value=\'' . $value . '\' />';
        } else {
            return '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />';
        }
    }
    
    public static function user_token()
    {
        return self::hidden('user_token', Session::get('user_token'));
    }
    

    public static function add_empty_rows($pCurrentRow, $pTotalRow, $pTotalColumn)
    {
        if ($pCurrentRow >= $pTotalRow) {
            return '';
        }
        $html = '';

        for ($i = $pCurrentRow + 1; $i <= ($pTotalRow + 1); $i++)
        {
            $v_row_class = 'row' . ($i % 2);

            $html .= '<tr class="' . $v_row_class . '">';
            for ($j = 1; $j <= $pTotalColumn; $j++) {
                $html .= '<td>&nbsp;</td>';
            }
            $html .= '</tr>';
        }
        return $html;
    }//end func add_empty_rows

    public static function generate_select_option($arrData, $selected=NULL, $public_xml_file_name=''){
        $html = '';
        if ($public_xml_file_name !== '')
        {
            $f = SERVER_ROOT . 'public/xml/' . $public_xml_file_name;
            if (file_exists($f))
            {
                $xml = simplexml_load_file($f);
                $items = $xml->xpath("//item");
                foreach ($items as $item)
                {
                    $str_selected = ($item->attributes()->name == strval($selected)) ? ' selected':'';
                    $html .= '<option value="' . $item->attributes()->name . '"' . $str_selected . '>' . $item->attributes()->value . '</option>';
                }
            }
        }
        else
        {
            foreach ($arrData as $key => $val)
            {
                $str_selected = ($key == strval($selected)) ? ' selected':'';
                $html .= '<option value="' . $key .'"' .$str_selected.'>'. $val .'</option>';
            }
        }
    	return $html;
    }

    public static function paging($page, $rows_per_page, $total_record){
        $html = '';

        if ($total_record % $rows_per_page == 0)
        {
            $v_total_page = $total_record / $rows_per_page;
        }
        else
        {
            $v_total_page = intval($total_record / $rows_per_page) + 1;
        }

        $arr_page = array();
        for ($i=1; $i <= $v_total_page; $i++){
            $arr_page[$i] = __('page') . '&nbsp;' . $i;
        }

        $html .= '<div class="pager" id="pager">';
        $html .= __('total') . ' ' . $v_total_page . ' ' . __('page');

        $html .= '. ' . __('go to') . '<select class="input-small" name="sel_goto_page" onchange="this.form.submit();">';
        $html .= self::generate_select_option($arr_page, $page);
        $html .= '</select>';

        $html .= __('display') . '<select class="input-small" name="sel_rows_per_page" onchange="this.form.sel_goto_page.value=1;this.form.submit();">';
        $html .= self::generate_select_option(null, $rows_per_page, 'xml_rows_per_page.xml');
        $html .= '</select> ' . __('record'). '/1 ' . __('page');

        $html .= '</div>';

        return $html;
    }

    public static function paging2($arr_all_record){
        $html = '';

        $rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
        if (isset($arr_all_record[0]['TOTAL_RECORD']))
        {
            $page = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 1;
            $total_record = $arr_all_record[0]['TOTAL_RECORD'];
        }
        else
        {
            $page = 1;
            $total_record = $rows_per_page;
        }

        if ($total_record % $rows_per_page == 0)
        {
            $v_total_page = $total_record / $rows_per_page;
        }
        else
        {
            $v_total_page = intval($total_record / $rows_per_page) + 1;
        }

        $arr_page = array();
        for ($i=1; $i <= $v_total_page; $i++){
            $arr_page[$i] = __('page') . '&nbsp;' . $i;
        }

        $html .= '<div class="pager" id="pager">';
        $html .= __('total') . ' ' . $v_total_page . ' ' . __('page');

        $html .= '. ' . __('go to') . ': <select name="sel_goto_page" class="input-small" id="sel_goto_page" onchange="this.form.submit();">';
        $html .= self::generate_select_option($arr_page, $page);
        $html .= '</select>';

        $html .= __('display') . ' <select name="sel_rows_per_page" class="input-small" id="sel_rows_per_page" onchange="this.form.sel_goto_page.value=1;this.form.submit();">';
        $html .= self::generate_select_option(null, $rows_per_page, 'xml_rows_per_page.xml');
        $html .= '</select> ' . __('record'). '/1 ' . __('page');

        $html .= '</div>';

        return $html;
    }

    public static function req($n=1){
        $html = '<font color="#FF0000">';
        for ($i=1; $i<=$n; $i++)
        {
            $html .= '*';
        }
        $html .= '</font>';

        return $html;
    }//end func req

    public static function textbox($p_name, $p_value, $p_data_validate, $p_data_name, $p_allow_null=TRUE, $arr_attr=array('style'=>'width:40%'))
    {
        $v_allow_null = ($p_allow_null) ? 'yes' : 'no';
        $html = '<input type="text" name="' . $p_name . '" value="' . $p_value . '" id="' . $p_name . '"
                   class="inputbox" onKeyDown="return handleEnter(this, event);"
                   data-allownull="' .  $v_allow_null . '" data-validate="' .  $p_data_validate . '"
                   data-name="' . $p_data_name . '"
                   data-xml="no" data-doc="no"';
        if (sizeof($arr_attr) > 0)
        {
            foreach ($arr_attr as $key => $val)
            {
                $html .= ' ' . $key . '="' . $val . '"';
            }
        }
        $html .= '/>';

        if (!$p_allow_null)
        {
            $html .= self::req(1);
        }

        return $html;
    }

    public static function write_filter_condition($arr_html_object_name=array())
    {
        foreach ($arr_html_object_name as $v_html_object_name)
        {
            echo self::hidden($v_html_object_name, get_request_var($v_html_object_name));
        }
    }

    public static function nav_home()
    {
        $html = '<a href="' . SITE_ROOT . '"><img src="' . SITE_ROOT
        . 'public/images/home.png" border="0" width="28px" height="28px"/> Trang chủ</a></span>';

        return $html;
    }
    
    public function layout_render($layout,$view,$data=array())
    {
       ob_start();
       $this->render($view,$data);
       $html_content = ob_get_clean();
       $LAYOUT_DATA['content'] = $html_content;
       $this->get_layout($layout,$LAYOUT_DATA);
    }

    public function get_layout($layout,$LAYOUT_DATA)
    {
       $v_layout_file = $this->url_layout . $layout .'.php';
       if (file_exists($v_layout_file))
       {
           //Tự động sinh các biến cho VIEW_DATA
           if (is_array($LAYOUT_DATA))
           {
               foreach ($LAYOUT_DATA as $key => $val)
               {
                   $$key = $val;
               }
           }
           require $v_layout_file;
       }
       else
       {
           $this->_layout_render_error(1);
       }
    }
    private function _layout_render_error($code)
   {
       switch ($code)
       {
           case 1:
               die('layout ko ton tai');
               break;
       }
   }
}