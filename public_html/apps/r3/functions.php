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
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class r3_View extends View {

	function __construct($app, $module) {
		parent::__construct($app, $module);
	}
	
	private function get_root_record_type_code($record_type_code)
	{
        return substr($record_type_code, 0, 2) . '00';
        /*
	    $ret =  preg_replace('/([0-9]+[A-Z0-9-_]*)/', '00', $record_type_code);
	    //$ret = str_replace('0000', '00', $ret);
	    return $ret;
         * 
         */
	}
    
    public function get_book_config($book_code)
    {
        return SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS
		    . 'common' . DS . $book_code . '_book.xml';
    }

	public function get_xml_config($record_type_code, $config_type, $get_default_config=TRUE)
	{
	    if (strtolower($config_type) == 'auto_lock_unlock')
		{
		    return SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS
		    . 'common' . DS . 'auto_lock_unlock.xml';
		}
		
		if (strtolower($config_type) == 'lookup')
		{
		    //Uu tien file chinh xac cua thu tuc
		    $file_path =  SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . $record_type_code . DS . $record_type_code . '_' . $config_type . '.xml';
		    if (is_file($file_path))
		    {
		        return $file_path;
		    }
		    else
		    {
		        //Uu tien tiep theo cho linh vuc
		        //$record_type_code  = preg_replace('/([0-9]+[A-Z]*)/', '00', $record_type_code);
		        $record_type_code  = $this->get_root_record_type_code($record_type_code);
		        $file_path         = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . 'common' . DS . $record_type_code . '_' . $config_type . '.xml';
		        
		        if (is_file($file_path))
		        {
		            return $file_path;
		        }
		        else
		        {
		            //Neu khong, lay mac dinh chung
		            return SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . 'common' . DS . 'record_lookup.xml';
		        }
		    }
		}

        if (strtolower($config_type) == 'result')
		{
			return SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS
			. 'common' . DS . 'xml_record_result.xml';
		}

		if ($config_type == 'list' && $record_type_code == '')
		{
		    return SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . 'common' . DS . 'common_list.xml';;
		}

		$file_path =  SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS
		. $record_type_code . DS . $record_type_code . '_' . $config_type . '.xml';

        if (!is_file($file_path) && ($get_default_config))
        {
            //$record_type_code = preg_replace('/([0-9]+[A-Z]*)/', '00', $record_type_code);
            $record_type_code  = $this->get_root_record_type_code($record_type_code);
            $file_path = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . 'common' . DS . $record_type_code . '_' . $config_type . '.xml';

            if (is_file($file_path))
            {
                return $file_path;
            }
            return NULL;
        }
        else
		{
                    return $file_path;
		}
	}

	/**
	 * Kiem tra xem 1 task co phai la task cuoi cung trong step hay khong?
	 * @param type $task_code
	 * @param type $record_type_code
	 *
	 */
	public function is_last_task($task_code, $record_type_code)
	{
		$dom = simplexml_load_file($this->get_xml_config($record_type_code, 'workflow'));
		$r = xpath($dom, "//step/task[last()][@code='$task_code']/@code");
		return count($r)> 0;
	}

	public function get_xsl_ho_teplate($record_type_code)
	{
		$file_path = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS
		. $record_type_code . DS . $record_type_code . '_ho_template.xsl';

		if (!is_file($file_path))
		{
			//$record_type_code = preg_replace('/([0-9]+[A-Z]*)/', '00', $record_type_code);
			$record_type_code  = $this->get_root_record_type_code($record_type_code);
			$file_path = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . 'common' . DS . $record_type_code . '_ho_template.xsl';
		}

		return $file_path;
	}

	public function get_role_url($role)
	{
		return $this->get_controller_url() . 'ho_so/' . $role;
	}

	public static function return_date_by_text($return_date_yyyymmdd)
	{
        if ($return_date_yyyymmdd == '')
        {
            return '';
        }

		$space = chr(32);
		$arr_date_part = explode($space, $return_date_yyyymmdd);
		$v_hour = intval(array_pop($arr_date_part));

		if ($v_hour <= intval(_CONST_MORNING_END_WORKING_TIME))
		{
			return jwDate::yyyymmdd_to_ddmmyyyy($return_date_yyyymmdd) . ' Từ ' . _CONST_MORNING_BEGIN_WORKING_TIME . ' đến ' . _CONST_MORNING_END_WORKING_TIME;
		}

		return jwDate::yyyymmdd_to_ddmmyyyy($return_date_yyyymmdd) . ' Từ ' . _CONST_AFTERNOON_BEGIN_WORKING_TIME . ' đến ' . _CONST_AFTERNOON_END_WORKING_TIME;
	}

	public static function break_date_string($date_string)
	{
		return preg_replace('/(^[0-9\-\/]*)\s/','$1<br/>', $date_string);
	}

	public function dsp_div_filter($v_record_type_code, $arr_all_record_type)
	{?>
		<div id="div_filter">
    		<table>
    		    <tr>
    		        <td width="14%">
            			<label>Mã loại hồ sơ (Alt+1)</label>
        			</td>
        			<td>
            			<input type="text" name="txt_record_type_code" id="txt_record_type_code"
            					value="<?php echo $v_record_type_code; ?>"
            					class="inputbox upper_text" size="10" maxlength="10"
            					onkeypress="txt_record_type_code_onkeypress(event);"
            					autofocus="autofocus"
            					accesskey="1"
            			/>
            			<select name="sel_record_type" id="sel_record_type" style="width:75%; color:#000000;"
            				onchange="sel_record_type_onchange(this)">
            				<option value="">-- Chọn loại hồ sơ --</option>
            				<?php $v_la_ho_so_lien_thong = FALSE;?>
            				<?php foreach ($arr_all_record_type as $code=>$info):?>
            				    <?php 
                                            $str_selected = ($code == strval($v_record_type_code)) ? ' selected':'';
                                            $v_mapping_code = (string)$info['C_MAPPING_CODE'];
                                            ?>
                                <option value="<?php echo $code;?>"<?php echo $str_selected?> data-mapping="<?php echo $v_mapping_code;?>" data-scope="<?php echo $info['C_SCOPE'];?>"><?php echo $info['C_NAME'];?></option>
                                <?php if (($code == $v_record_type_code) && ($info['C_SCOPE'] == 1)) {$v_la_ho_so_lien_thong = TRUE;}?>
            				<?php endforeach;?>
            	            <?php //echo $this->generate_select_option($arr_all_record_type, $v_record_type_code); ?>
            	        </select>
        	        </td>
    	        </tr>
    	        <?php if ( ($v_la_ho_so_lien_thong == TRUE) && (!session::get('la_can_bo_cap_xa')) ):?>
    	            <tr>
    	                <td>Xã tiếp nhận:</td>
    	                <td> 
            	            <select name="sel_village_filter" id="sel_village_filter" style="width:25%; color:#000000;"
                				onchange="sel_can_bo_cap_xa_onchange(this)">
            				    <option value="">-- Tất cả các xã --</option>
            				<?php echo $this->generate_select_option(Session::get('arr_all_village'), get_post_var('sel_village_filter')); ?>
            				</select>
        				</td>
    				</tr>
    	        <?php endif;?>
	        </table>
	        <input type="text" name="noname" style="visibility: hidden;width:1px;height:1px;" />
	    </div><?php
    }

	public function render_form_display_all_record($data, $edit=TRUE) {
	    
		$html = '';
		$arr_status = array('0'=>__('active status'),'1'=>__('inactive status'));

		$p =xpath($this->dom, "//display_all/list/item[@type = 'primarykey']/@id");
		$primarykey = strval($p[0]);

		$cols = xpath($this->dom,"//display_all/list/item[@type != 'primarykey']");

		//List header
		$table_col_size = $table_header = '';
		foreach ($cols as $col) {
			$table_col_size .= '<col width="' . $col->attributes()->size . '" />';
			if (strval($col->attributes()->type != 'checkbox')){
				$table_header .= '<th>' . $col->attributes()->name . '</th>';
			} else {
				if (sizeof($data) > 0){
					$table_header .= '<th><input type="checkbox" name="chk_check_all" onclick="toggle_check_all(this,this.form.chk);"/></th>';
				} else {
					$table_header .= '<th>&nbsp;</th>';
				}
			}
		}
		$html .= '<table width="100%" class="adminlist" cellspacing="0" cellpading="0" border="1">' . $table_col_size
		. '<tr>' . $table_header . '</tr>';

		//List item
		//$html .= '<table width="100%" class="adminlist" cellspacing="0" cellpading="0" border="1">';
		//$html .= $table_col_size;
		$i = 0;
		for ($i = 0; $i < sizeof($data); $i++)
		{
		    
			$v_xml_processing = $data[$i]['C_XML_PROCESSING'];
			$dom_processing = @simplexml_load_string($v_xml_processing);
			$v_promote = xpath($dom_processing,"//next_task/@promote", true);

			$v_reason = xpath($dom_processing,"//next_task/@reason", true);

			$v_owner = $data[$i]['C_OWNER'];

			$v_promote_class = '';
			$v_tooltip = '';
			if ($v_promote == _CONST_RECORD_APPROVAL_REJECT)
			{
				$v_promote_class = ' promote-reject';
				$v_tooltip = ' onMouseOver="return overlib(\'<u>Lý do:</u><br/>' . $v_reason . '\',BELOW, RIGHT, CAPTION, \'TỪ CHỐI\');" onMouseOut="return nd();"';
			}
			elseif ($v_promote == _CONST_RECORD_APPROVAL_SUPPLEMENT)
			{
				$v_promote_class = ' promote-supplement';
				$v_tooltip = ' onMouseOver="return overlib(\'<u>Lý do:</u><br/>' . $v_reason . '\',BELOW, RIGHT, CAPTION, \'BỔ SUNG HỒ SƠ\');" onMouseOut="return nd();"';
			}

			$v_row_class = 'row' . ($i % 2);
                        //LienND update 2013-02-07: Kiem tra next task la NO_CHAIN
                        $v_record_no = $data[$i]['C_RECORD_NO'];
                        $v_record_type_code = preg_replace('/^([A-Z0-9_]+)-([A-Z0-9]+)$/', '$1', $v_record_no);
			$v_next_task_code = xpath($dom_processing,"//next_task[last()]/@code[last()]", true );
			$v_next_role = get_role($v_next_task_code);
			 
			$html .= '<tr class="' . $v_row_class . $v_promote_class . '"
					role="presentation" data-item_id="' . $data[$i][$primarykey] . '"
                                            data-item-type="'. $v_record_type_code .'"
                                            data-deleted="'. $data[$i]['C_DELETED'] .'"
							data-owner="'.  $v_owner . '"'
									. $v_tooltip . '>';

			if (isset($data[$i]['C_XML_DATA']))
			{
				$v_xml_data             = $data[$i]['C_XML_DATA'];
				$dom_xml_data           = simplexml_load_string($v_xml_data);
			}
			else
			{
				$dom_xml_data = NULL;
			}
			
			
			
			
			$dom_workflow = simplexml_load_file($this->get_xml_config($v_record_type_code, 'workflow'));
            if ($dom_workflow)
            {
                $v_next_task_is_no_chain = get_xml_value($dom_workflow, "//task[@code='$v_next_task_code']/../@no_chain");
            }
            else
            {
                echo "$v_record_type_code: Thiếu XML hoặc XML hỏng <br/>";
                $v_next_task_is_no_chain = '';
            }

			reset($cols);
			foreach ($cols as $col)
			{
				$index = strval($col->attributes()->id);
				$v_clickable = strval($col->attributes()->clickable);
				switch (strval($col->attributes()->type)) {

					case 'checkbox':
						$html .= '<td class="center"><input type="checkbox" name="chk"
								value="' . $data[$i][$primarykey] . '"';
						if ($v_owner == 0)
						{
							$html .= ' disabled';
						}
						else
						{
							$html .= ' onclick="if (!this.checked) this.form.chk_check_all.checked=false;" ';
						}
						$html .= ' /></td>';
						break;

					case 'moving':
						$html .= '<td>#</td>';
						break;

					case 'order':
						$html .= '<td class="right">' . $data[$i][$index] . '</td>';
						break;

					case 'status':
						$html .= '<td class="center">' . $arr_status[$data[$i][$index]] . '</td>';
						break;

					case 'action':
						$v_data_reject = '';
						if ($v_promote == _CONST_RECORD_APPROVAL_REJECT)
						{
							$v_data_reject = ' data-reject="1"';
						}
                                                
						$html .= "<td role=\"action\" $v_data_reject>
                                                    <div class=\"quick_action\" id=\"action_{$data[$i][$primarykey]}\"
                                                        data-item_id=\"{$data[$i][$primarykey]}\"
                                                        data-item-type=\"{$v_record_type_code}\"
                                                        data-deleted=\"{$data[$i]['C_DELETED']}\"
                                                        $v_data_reject >&nbsp;</div></td>";
						break;

					case 'rownum':
						$html .= '<td class="center">' . strval($i+1) . '</td>';
						break;
						
					case 'text_concat':
					    //Ket hop nhieu cot
					    $arr_index = explode(',', $index);
					    
					    $html .= '<td>';
					    foreach ($arr_index as $index)
					    {
					        $index = trim($index);
					        $item_value = '';
					        if (strpos($index , 'xml/') !== FALSE) //Cot du lieu nam trong XML
					        {
					            $index = str_replace('xml/','',$index);
					            $item_value = get_xml_value($dom_xml_data, "/data/item[@id='$index']/value");
					        }
					        else
					        {
					            $item_value = $data[$i][$index];
					        }
					        
					        if (strtolower($index) == 'ddlxaphuong')
					        {
					            $dom_xa_phuong = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_phuong_xa.xml');
					            $item_value = get_xml_value($dom_xa_phuong, "//item[@value='$item_value']/@name");
					            
					            $item_value = trim(preg_replace('/([0-9]+)\.(.*)/', '$2', $item_value));
					        }
					        					        
					        $html .= $item_value;
					        $html .= ' ';
					        
					    }//end foreach
					    $html .= '</td>';
					    break;

					case 'text':
					default:
						if (strpos($index , 'xml/') !== FALSE) //Cot du lieu nam trong XML
						{
							$index = str_replace('xml/','',$index);
							$d = xpath($dom_xml_data,"/data/item[@id='$index']/value", true);

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
							$val = trim($data[$i][$index]);
							if ($edit && $v_clickable != 'no')
							{
							    $val = '<a href="javascript:void(0)" onclick="row_onclick(\'' . $data[$i][$primarykey] . '\')">' . $val . '</a>';
							}

							$append = '';

							if ($index == 'C_RECORD_NO')
							{
							    if (isset($data[$i]['C_LAST_RECORD_COMMENT']))
							    {
							        $val .= '<br/><a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' . $data[$i][$primarykey] . '\', \'comment\')" onmouseover="console.log(\'Chức năng đang phát triển\')">
					                        <span class="reason"><i><img src="' . SITE_ROOT . 'public/images/icon-32-warning.png" width="20" height="20"/>' . $data[$i]['C_LAST_RECORD_COMMENT'] . '</i></span></a>';
							    }
							    //Co tin nhan voi HS?
							    $v_reason = xpath($dom_processing,"//step[last()]/reason[last()]", true);
							    if ($v_reason != '')
							    {
							        $val .= '<br/><span class="reason"><i><img src="' . SITE_ROOT . 'public/images/icon-32-message.png" width="20" height="20"/>' . $v_reason . '</i></span>';
							    }
							}

							if ($index == 'C_RECEIVE_DATE')
							{
								$val = $this->break_date_string(jwDate::yyyymmdd_to_ddmmyyyy($val, 1));
							}
							elseif ($index == 'C_RETURN_DATE')
							{
                                //hour ?
                                $val = $this->break_date_string($this->return_date_by_text($val));
    
                                //tra cuu ?
                                $v_activity = isset($data[$i]['C_ACTIVITY']) ? $data[$i]['C_ACTIVITY'] : 1;
                                if ($v_activity == 1)
                                {
                                        if($data[$i]['C_DELETED']){
                                            $append .= '<br/><font color="gray"><b>Đã xoá</b></font>';
                                        }
                                        elseif($data[$i]['C_PAUSE_DATE'] && !$data[$i]['C_UNPAUSE_DATE']){
                                            $append .= '<br/><font color="gray"><b></b></font>';
                                        }
                                        else{
                                            $v_return_days_remain = $data[$i]['C_RETURN_DAYS_REMAIN'];
                                            if ($v_return_days_remain < 0 )
                                            {
                                                    $append .= '<br><span class="days-remain overdue">(Đã quá hạn ' . abs($v_return_days_remain) . ' ngày)</span></b>';
                                            }
                                            elseif ($v_return_days_remain == 0)
                                            {
                                                    $append .= '<br><span class="days-remain today">Hôm nay</span></b>';
                                            }
                                            else
                                            {
                                                    $append .= '<br><span class="days-remain during">(Còn ' . abs($v_return_days_remain) . ' ngày)</span></b>';
                                            }
                                        }
                                }                         
							} //endif ($index == 'C_RETURN_DATE')
							elseif ($index == 'C_DOING_STEP_DAYS_REMAIN') //So ngay con lai cua step (đang thực hiện)
							{
							    //Khong thong bao han giai quyet vois step NO_CHAIN
							    if (!$v_next_task_is_no_chain && ($v_next_role != _CONST_THU_PHI_ROLE && ($v_next_role != _CONST_TRA_KET_QUA_ROLE)))
							    {
    								//Số ngày quy định của step
    								$v_step_time = trim($data[$i]['C_STEP_TIME']);
    								if ($v_step_time == 0)
    								{
    								    $arr_date_part = explode(chr(32), $data[$i]['C_DOING_STEP_BEGIN_DATE']);
    									$v_step_begin_hour = intval(array_pop($arr_date_part));
    									if (date('H',strtotime($this->DATETIME_NOW)) >=12 && $v_step_begin_hour < 12)
    									{
    										$val = $val - 0.5;
    									}
    								}
    
    								if ($val < 0)
    								{
    									$val = '<span class="days-remain overdue">Đã quá hạn ' . abs($val) . ' ngày</span></b>';
    								}
    								elseif ($val == 0)
    								{
    									if (date('H',strtotime($this->DATETIME_NOW)) < 12)
    									{
    										$val = '<span class="days-remain today">Trong sáng nay</span></b>';
    									}
    									else
    									{
    										$val = '<span class="days-remain today">Trong chiều nay</span></b>';
    									}
    								}
    								else
    								{
    									$val = '<span class="days-remain during">Còn ' . abs($val) . ' ngày</span></b>';
    								}
							    }
							    else
							    {
							        $val = '';
							    }

							}
							elseif ($index == 'C_ACTIVITY')
							{
								$append = '';
                                if($data[$i]['C_DELETED'])
                                {
                                    $val = '<font color="gray"><b>Đã xoá</b></font>';
                                }
                                else
                                {
                                    if ($val == 1)
                                    {
                                            $xresult = xpath($dom_processing,"//next_task[last()]/@group_name[last()]", true);
                                            $v_group_name = $xresult ? $xresult : '[Tên phòng]';

                                            $v_next_task_name = isset($this->role_text[$v_next_role]) ? $this->role_text[$v_next_role] : $v_next_role;

                                            if ($v_next_task_is_no_chain )
                                            {
                                                $val = '<span class="no_chain"><b>'.$v_group_name . '</b> đang <b>' . $v_next_task_name . '</b></span>';
                                            }
                                            else
                                            {
                                                $val = '<b>'.$v_group_name . '</b> đang <b>' . $v_next_task_name . '</b>';
                                            }

                                            //So ngay con lai cua step dang thực hiện
                                            $v_step_days_remain = $data[$i]['C_DOING_STEP_DAYS_REMAIN'];
                                            //Số ngày quy định của step
                                            $v_step_time = trim($data[$i]['C_STEP_TIME']);
                                            if ($v_step_time == 0)
                                            {
                                                $arr_date_part = explode(chr(32), $data[$i]['C_DOING_STEP_BEGIN_DATE']);
                                                    $v_step_begin_hour = intval(array_pop($arr_date_part));
                                                    if (date('H', strtotime($this->DATETIME_NOW) ) >=12 && $v_step_begin_hour < 12)
                                                    {
                                                            $v_step_days_remain = $v_step_days_remain - 0.5;
                                                    }
                                            }
                                            
                                            /*

                                            $v_biz_done = $data[$i]['C_BIZ_DAYS_EXCEED'];
                                            if ($v_biz_done == NULL && 
                                                    !$v_next_task_is_no_chain && 
                                                    $v_next_role != _CONST_THU_PHI_ROLE && 
                                                    $v_next_role != _CONST_TRA_KET_QUA_ROLE &&
                                                    $v_next_role != _CONST_BO_SUNG_ROLE)
                                            {
                                                    $val .= '<br/ >';

                                                    if ($v_step_days_remain < 0)
                                                    {
                                                            $val .= '<span class="days-remain flood">Đã quá hạn ' . abs($v_step_days_remain) . ' ngày</span></b>';
                                                    }
                                                    elseif ($v_step_days_remain == 0)
                                                    {
                                                            if (date('H',strtotime($this->DATETIME_NOW) ) < 12)
                                                            {
                                                                    $val .= '<span class="days-remain today">Trong sáng nay</span></b>';
                                                            }
                                                            else
                                                            {
                                                                    $val .= '<span class="days-remain today">Trong chiều nay</span></b>';
                                                            }
                                                    }
                                                    else
                                                    {
                                                            $val .= '<span class="days-remain during">Còn ' . abs($v_step_days_remain) . ' ngày</span></b>';
                                                    }
                                            }
                                            */
                                    }
                                    if ($val == 2)
                                    {
                                            //Da tra ket qua
                                            //Ngay tra?
                                            $v_last_task_code = trim($data[$i]['C_LAST_TASK_CODE']);
                                            $arr_return_date = xpath($dom_processing,"//step[@code='$v_last_task_code'][last()]/datetime",true);
                                            $v_return_date = jwDate::yyyymmdd_to_ddmmyyyy($arr_return_date, 1);
                                            $v_done = trim($data[$i]['C_BIZ_DAYS_EXCEED']);
                                            $val = '<span>Đã trả kết quả</span>';
                                            $val .= '<br /><span>Ngày trả <b>' . $v_return_date . '</b></span>';
                                            if ($v_done < 0)
                                            {
                                                    $val .= '<br /><span>Quá hạn  <b>' . abs($v_done) . '</b> ngày</span>';
                                            }
                                            elseif ($v_done == 0)
                                            {
                                                    $val .= '<br /><span>Đúng hạn</span>';
                                            }
                                            else
                                            {
                                                    $val .= '<br />Trước hạn <b>' . $v_done . '</b> ngày</span>';
                                            }
                                    }
                                    if ($val == 3)
                                    {
                                            //Da bi tu choi
                                            //Ngay tu choi
                                            $arr_reject_date = xpath($dom_processing,"//step[@code='REJECT'][last()]/datetime", true);
                                            $v_reject_date = jwDate::yyyymmdd_to_ddmmyyyy($arr_reject_date, 0);

                                            //Ly do tu choi
                                            $v_reason   = xpath($dom_processing,"//step[@code='REJECT'][last()]/reason[last()]", true);
                                            $v_group_name   = xpath($dom_processing,"//step[@code='REJECT'][last()]/group_name[last()]", true);

                                            $val = '<span><b>' .  $v_group_name . '</b> đã từ chôi</span>';
                                            $val .= '<br /><span>Ngày từ chối <b>' . $v_reject_date . '</b></span>';
                                            $val .= '<br /><span><b><u>Lý do:</u></b> <i>'.  $v_reason . '</i></span>';
                                    }
                                }
								
							}
							else
							{
								//Nothing
							}

							if (!$edit OR $v_clickable == 'no')
							{
								$html .= '<td>' . $val . $append . '</td>';
							}
							else
							{
								$html .= '<td><a href="javascript:void(0)" onclick="row_onclick(\'' . $data[$i][$primarykey] . '\')">' . $val . $append . '</a></td>';
							}
						}
						break;
				}
			}
			$html .= '</tr>';
		}

		$html .= $this->add_empty_rows($i+1,_CONST_DEFAULT_ROWS_PER_PAGE,count($cols));
		$html .= '</table>';

		return $html;
	}

	public function dsp_div_notice($title)
	{
		//$html = '<div class="page-title">' . $title . '</div>';
		$html = '';
		$html .= '<div class="page-notice">
				<div id="notice">
				<div class="notice-title">Thống kê hồ sơ</div>
				<div class="notice-container" id="notice-container"><ul></ul></div>
				</div>
				</div>';
		return $html;
	}
	
	public function get_unit_info($tag)
	{
	    $dom_unit_info = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
	    return get_xml_value($dom_unit_info, "/unit/$tag");
	}
    
    
    public function render_book($data) 
    {
	    
		$html = '';
		$arr_status = array('0'=>__('active status'),'1'=>__('inactive status'));

		$primarykey = xpath($this->dom,"//display_all/list/item[@type = 'primarykey']/@id", true);

		$cols = xpath($this->dom,"//display_all/list/item[@type != 'primarykey']");

		//List header
		$table_col_size = $table_header = '';
		foreach ($cols as $col) {
			$table_col_size .= '<col width="' . $col->attributes()->size . '" />';
			if (strval($col->attributes()->type != 'checkbox')){
				$table_header .= '<th>' . $col->attributes()->name . '</th>';
			} else {
				if (sizeof($data) > 0){
					$table_header .= '<th><input type="checkbox" name="chk_check_all" onclick="toggle_check_all(this,this.form.chk);"/></th>';
				} else {
					$table_header .= '<th>&nbsp;</th>';
				}
			}
		}
		$html .= '<table width="100%" class="adminlist" cellspacing="0" cellpading="0" border="1">' . $table_col_size
		. '<tr>' . $table_header . '</tr>';

		//List item
		//$html .= '<table width="100%" class="adminlist" cellspacing="0" cellpading="0" border="1">';
		//$html .= $table_col_size;
		$i = 0;
		for ($i = 0; $i < sizeof($data); $i++)
		{
		    
			$v_xml_processing = $data[$i]['C_XML_PROCESSING'];
			$dom_processing = @simplexml_load_string($v_xml_processing);
			$v_promote = xpath($dom_processing,"//next_task/@promote", true);
			$v_reason = xpath($dom_processing,"//next_task/@reason", true);

			//$v_owner = $data[$i]['C_OWNER'];

			$v_promote_class = '';
			$v_tooltip = '';
			if ($v_promote == _CONST_RECORD_APPROVAL_REJECT)
			{
				$v_promote_class = ' promote-reject';
				$v_tooltip = ' onMouseOver="return overlib(\'<u>Lý do:</u><br/>' . $v_reason . '\',BELOW, RIGHT, CAPTION, \'TỪ CHỐI\');" onMouseOut="return nd();"';
			}
			elseif ($v_promote == _CONST_RECORD_APPROVAL_SUPPLEMENT)
			{
				$v_promote_class = ' promote-supplement';
				$v_tooltip = ' onMouseOver="return overlib(\'<u>Lý do:</u><br/>' . $v_reason . '\',BELOW, RIGHT, CAPTION, \'BỔ SUNG HỒ SƠ\');" onMouseOut="return nd();"';
			}

			$v_row_class = 'row' . ($i % 2);
			$html .= '<tr class="' . $v_row_class . $v_promote_class . '"
					role="presentation" data-item_id="' . $data[$i][$primarykey] . '"
							data-owner=""'
									. $v_tooltip . '>';

			if (isset($data[$i]['C_XML_DATA']))
			{
				$v_xml_data             = $data[$i]['C_XML_DATA'];
				$dom_xml_data           = simplexml_load_string($v_xml_data);
			}
			else
			{
				$dom_xml_data = NULL;
			}
			
			$v_record_no = $data[$i]['C_RECORD_NO'];
			$v_next_task_code = xpath($dom_processing,"//next_task[last()]/@code[last()]", true);
			$v_next_role = get_role($v_next_task_code);
			 
			//LienND update 2013-02-07: Kiem tra next task la NO_CHAIN
			$v_record_type_code = preg_replace('/^([A-Z0-9_]+)-([A-Z0-9]+)$/', '$1', $v_record_no);
			
			$dom_workflow = simplexml_load_file($this->get_xml_config($v_record_type_code, 'workflow'));
			$v_next_task_is_no_chain = get_xml_value($dom_workflow, "//task[@code='$v_next_task_code']/../@no_chain" );

			reset($cols);
			foreach ($cols as $col)
			{
				$index = strval($col->attributes()->id);
				$v_clickable = strval($col->attributes()->clickable);
				switch (strval($col->attributes()->type)) {

					case 'checkbox':
						$html .= '<td class="center"><input type="checkbox" name="chk"
								value="' . $data[$i][$primarykey] . '"';
						if ($v_owner == 0)
						{
							$html .= ' disabled';
						}
						else
						{
							$html .= ' onclick="if (!this.checked) this.form.chk_check_all.checked=false;" ';
						}
						$html .= ' /></td>';
						break;

					case 'moving':
						$html .= '<td>STT</td>';
						break;

					case 'order':
						$html .= '<td class="right">' . $data[$i][$index] . '</td>';
						break;

					case 'status':
						$html .= '<td class="center">' . $arr_status[$data[$i][$index]] . '</td>';
						break;

					case 'action':
						$html .= '<td role="action"><div class="quick_action" data-item_id="' . $data[$i][$primarykey] . '">&nbsp;</div></td>';
						break;

					case 'rownum':
						$html .= '<td class="center">' . strval($i+1) . '</td>';
						break;
						
					case 'text_concat':
                        $val = $this->get_text_concat_value($data[$i], $index);
                        $html .= "<td>$val</td>";
                        break;

                    case 'text':
					default:
                        $align = $col->attributes()->align ? "text-align:" . $col->attributes()->align : '';
                        $html .= "<td style=\"$align\">" . $this->get_book_row_value_by_xml($data[$i], $index) . '</td>';
                    break;
				}
			}
			$html .= '</tr>';
		}

		$html .= $this->add_empty_rows($i+1,_CONST_DEFAULT_ROWS_PER_PAGE,count($cols));
		$html .= '</table>';

		return $html;
	}
    
    function get_book_row_value_by_xml($row_data, $xml_id_attr)
    {
        $xml_id_attr        = (string) $xml_id_attr;
        $v_xml_data         = $row_data['C_XML_DATA'];
        $dom_xml_data       = simplexml_load_string($v_xml_data);
        $v_xml_processing   = $row_data['C_XML_PROCESSING'];
        $dom_xml_processing = simplexml_load_string($v_xml_processing);
        $v_xml_result = $row_data['XML_RECORD_RESULT'];
        $dom_xml_result = simplexml_load_string($v_xml_result);
        if (strpos($xml_id_attr, 'p_xml/') !== FALSE) //XML_PROCESSING
        {
            return $this->get_p_xml_value($dom_xml_processing, $xml_id_attr);
        }
        elseif (strpos($xml_id_attr, 'r_xml/') !== FALSE) //XML_RECORD_RESULT
        {
            return $this->get_r_xml_value($dom_xml_result, $xml_id_attr);
        }
        elseif (strpos($xml_id_attr, 'xml/') !== FALSE) //XML_DATA
        {
            return $this->get_xml_form_value($dom_xml_data, $xml_id_attr);
        }
        else //cot tuong minh
        {
            return $this->get_normal_field_value($row_data, $xml_id_attr);
        }
    }
    
    function get_text_concat_value($row_data, $xml_id_attr)
    {
        //Ket hop nhieu cot
        $arr_index = explode(',', str_replace(' ', '', $xml_id_attr));
        $val       = '';
        foreach ($arr_index as $index)
        {
            $index = strval($index);
            $val .= $this->get_book_row_value_by_xml($row_data, $index) . ' ';
        }
        return $val;
    }
    
    function get_p_xml_value($dom_xml_processing, $xml_id_attr)
    {
        $index_parts = explode('/', $xml_id_attr);
        if ($dom_xml_processing)
        {
            $role       = $index_parts[1];
            $child_node = $index_parts[2];

            $val = xpath($dom_xml_processing,"//step[contains(@code,'$role') and last()]/$child_node", true);
            if ($val instanceof SimpleXMLElement && $val->getName() == 'datetime')
            {
                return date_create((string) $val)->format('d-m-Y');
            }
            return (string) $val;
        }
        return NULL;
    }
    
    function get_normal_field_value($row_data, $xml_id_attr)
    {
        $val         = isset($row_data[$xml_id_attr]) ? $row_data[$xml_id_attr] : '';
        $date_fields = array('C_RECEIVE_DATE', 'C_RETURN_DATE', 'C_CLEAR_DATE');

        if (in_array($xml_id_attr, $date_fields))
        {
            $val = date_create($val)->format('d-m-Y');
        }
        elseif ($xml_id_attr == 'C_SCOPE')
        {
            $arr_scope = array(
                '0' => 'UBND Xã',
                '1' => 'Liên thông xã -> huyện',
                '2' => 'UBND huyện',
                '3' => 'Liên thông huyện -> xã',
            );
            $val       = $arr_scope[$val];
        }//endif ($index == 'C_SCOPE')
        return $val;
    }
    
    function get_xml_form_value($dom_xml_data, $xml_id_attr)
    {
        $xml_id_attr = str_replace('xml/', '', $xml_id_attr);
        return xpath($dom_xml_data,"/data/item[@id='$xml_id_attr']/value", true);
    }
    
    function get_r_xml_value($dom_xml_result, $xml_id_attr)
    {
        $xml_id_attr = str_replace('r_xml/', '', $xml_id_attr);
        return xpath($dom_xml_result,"//item[@id='$xml_id_attr']/value", true);
    }
}

