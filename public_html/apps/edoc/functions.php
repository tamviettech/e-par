<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class edoc_View extends View {

	function __construct($app, $module) {
		parent::__construct($app, $module);
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
	

	public function render_form_display_all_by_doc_type($data)
    {
        $html = '';

        $p = $this->dom->xpath("//display_all/list/item[@type = 'primarykey']/@id");
        $primarykey = strval($p[0]);

        $cols = $this->dom->xpath("//display_all/list/item[@type != 'primarykey']");

        //List header
        $table_col_size = $table_header = '';
        foreach ($cols as $col)
        {
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

        //header
        $html .= '<table width="100%" class="adminlist" cellspacing="0" border="1">' . $table_col_size
                . '<tr>' . $table_header . '</tr></table>';

        //List item
        $html .= '<table width="100%" class="adminlist" cellspacing="0" border="1">';
        $html .= $table_col_size;

        $row_index = 0;
        for ($i = 0; $i < sizeof($data); $i++)
        {
            $v_xml_doc_processing   = $data[$i]['DOC_TINH_TRANG_XU_LY'];
            $dom_processing         = simplexml_load_string($v_xml_doc_processing);

            $v_xml_data             = $data[$i]['C_XML_DATA'];
            $dom_xml_data           = simplexml_load_string($v_xml_data);
            
            $v_doc_id               = $data[$i][$primarykey];

            $v_xml_attach_file_list = $data[$i]['DOC_ATTACH_FILE_LIST'];

            if (isset($data[$i]['DOC_FOLDER_LIST']))
            {
                $v_folder_list_list     = $data[$i]['DOC_FOLDER_LIST'];
            }
            else
            {
                $v_folder_list_list = NULL;
            }

            $v_doc_direction        = strtoupper($data[$i]['C_DIRECTION']);
            $v_doc_type             = strtoupper($data[$i]['C_TYPE']);
            $v_is_folded            = strtoupper($data[$i]['C_FOLDED']);
            $v_doc_so_ky_hieu       = strtoupper($data[$i]['DOC_SO_KY_HIEU']);


            $v_row_class = 'row' . ($row_index % 2);
            $html .= '<tr class="' . $v_row_class . '" data-doc_id="' . $v_doc_id . '" role="presentation">';

            reset($cols);
            foreach ($cols as $col)
            {
                $v_col_data_type = strtolower(strval($col->attributes()->type));

                $index = strval($col->attributes()->id);
                switch ($v_col_data_type)
                {
                    case 'checkbox':
                        $html .= '<td class="center checkbox"><input type="checkbox" name="chk"
                            value="' . $data[$i][$primarykey] . '"
                            onclick="if (!this.checked) this.form.chk_check_all.checked=false;" /></td>';
                        break;

                    case 'text':
                        if (strpos($index , 'xml/') !== FALSE) //Cot du lieu nam trong XML
                        {
                            $index = str_replace('xml/','',$index);

                            $r = $dom_xml_data->xpath("/data/item[@id='$index']/value");
                            $d = sizeof($r) ? $r[0] : '';

                            $v_url = $this->get_controller_url() . $v_doc_direction . '/';
                            $v_url .= 'dsp_single_doc/&doc_id=' . $data[$i][$primarykey]
                                        . '&hdn_item_id=' . $data[$i][$primarykey]
                                        . '&pop_win=1'
                                        . '&direction=' . strtoupper($data[$i]['C_DIRECTION'])
                                        . '&type=' . strtoupper($data[$i]['C_TYPE']);

                            $html .= '<td><a href="javascript:void(0);" onclick="row_onclick_pop_win(\'' . $v_url . '\')">' . $d . '</a>';

                            if ($index == 'doc_trich_yeu')
                            {
                                if ($v_xml_attach_file_list != NULL)
                                {
                                    $f_dom = simplexml_load_string('<root>' . $v_xml_attach_file_list . '</root>');
                                    $rows = $f_dom->xpath('//row');

                                    $html.= '<br/><img src="' . SITE_ROOT . 'public/images/attach.png" /><u>File đính kèm:</u>';

                                    foreach ($rows as $row)
                                    {
                                        $v_attach_file_name = $row->attributes()->C_FILE_NAME;
                                        $v_attach_file_id = $row->attributes()->PK_DOC_FILE;

                                        $v_file_path    = $this->get_controller_url() . 'view_attachment/'. $v_attach_file_id;

                                        $v_file_extension = substr(strrchr($v_attach_file_name,'.'),1);
                                        $html .= '<br/>&nbsp;&nbsp;<img src="' . SITE_ROOT . 'public/images/' . $v_file_extension . '-icon.png" width="16px" height="16px"/>';
                                        $html .=  '<a href="' . $v_file_path .'" target="_blank">' .$v_attach_file_name . '</a>&nbsp;';
                                    }
                                }

                                if ($v_folder_list_list != NULL)
                                {
                                    $f_dom = simplexml_load_string('<root>' . $v_folder_list_list . '</root>');
                                    $folders = $f_dom->xpath('//row');

                                    $html.= '<br/><img src="' . SITE_ROOT . 'public/images/folder-16x16.png" /><u>Hồ sơ lưu:</u>';
                                    foreach ($folders as $folder)
                                    {
                                        $v_folder_name = $folder->attributes()->C_CODE . ' - ' . $folder->attributes()->C_NAME;
                                        $v_folder_id = $folder->attributes()->PK_FOLDER;
                                        $html .=  '<br/><label>' . $v_folder_name . '</label>&nbsp;';
                                        $html .=  '<a href="javascript:void(0)" onclick="remove_doc_folder('. $v_doc_id . ',' . $v_folder_id . ')"><img src="' . SITE_ROOT . 'public/images/trash.png" /></a>';
                                    }
                                }

                            }

                            $html .= '</td>';
                        }
                        else //Cot tuong minh
                        {
                            if ($index === 'DOC_TINH_TRANG_XU_LY')
                            {
                                $v_desc = '';
                                $steps = $dom_processing->xpath('/data/step[last()]');
                                $v_desc .= '<ul class="doc-step">';
                                foreach ($steps as $step)
                                {
                                    $v_step_code = $step->attributes()->code;
                                    if ($v_step_code == 'sub_allot')
                                    {
                                        $v_create_user_name_tag = 'allot_user_name';
                                    }
                                    else
                                    {
                                        $v_create_user_name_tag = $v_step_code . '_user_name';
                                    }

                                    $v_receive_user_name = '';
                                    if ($v_step_code == 'submit')
                                    {
                                        $v_receive_user_name = $step->allot_user_name;
                                    }

                                    $v_create_user_name = $step->$v_create_user_name_tag;


                                    if (isset($step->allot_detail->deadline_exec_date))
                                    {
                                        if (isset($step->allot_detail->exec_day) && intval($step->allot_detail->exec_day) > 0)
                                        {
                                            $v_deadline_exec_date = ' (Hạn thụ lý: <b>' . $step->allot_detail->deadline_exec_date . '</b>)';
                                        }
                                    }
                                    elseif ($step->attributes()->code == 'reject')
                                    {
                                        $v_deadline_exec_date = ' (Lý do: <b>' . $step->reject_reason . '</b>)';
                                    }
                                    else
                                    {
                                        $v_deadline_exec_date = '';
                                    }

                                    $v_tooltip = '';
                                    if (isset($step->allot_detail->allot_message) && $step->allot_detail->allot_message != '')
                                    {
                                        $v_tooltip = ' <br/><u><b>Ý kiến chỉ đạo:</b></u><br/>' . $step->allot_detail->allot_message;
                                    }

                                    $v_commit_img = '';
                                    if ($step->attributes()->code == 'commit')
                                    {
                                        $v_commit_img = '<img src="' . SITE_ROOT . 'public/images/complete.png" />';
                                    }
                                    $v_desc .= '<li>' . $v_commit_img
                                            . '<span class="doc-step-date">' . date("d/m/Y", strtotime($step->datetime))
                                            . ' [<i>' . $v_create_user_name . '</i>]</span> '
                                            . $step->action_text;

                                    if ($v_receive_user_name != '')
                                    {
                                        $v_desc .= ' [<i>' . $v_receive_user_name . '</i>]';
                                    }

                                    $v_desc .= $v_deadline_exec_date . $v_tooltip . '</li>';
                                }
                                $v_desc .= '</ul>';
                                $v_url = $this->get_controller_url() . 'dsp_process/' . $v_doc_id;
                                $v_more = "<a href=\"javascript:;\" onclick=\"row_onclick_pop_win('$v_url')\">
                                                Xem thêm
                                           </a>";
                                $html .= '<td>'. $v_desc . $v_more . '</td>';
                            }
                            else
                            {
                                $v_url = $this->get_controller_url() . $v_doc_direction . '/';
                                $v_url .= 'dsp_single_doc/&doc_id=' . $data[$i][$primarykey]
                                            . '&hdn_item_id=' . $data[$i][$primarykey]
                                            . '&pop_win=1'
                                            . '&direction=' . strtoupper($data[$i]['C_DIRECTION'])
                                            . '&type=' . strtoupper($data[$i]['C_TYPE']);
                                $html .= '<td><a href="javascript:void(0);" onclick="row_onclick_pop_win(\'' . $v_url . '\')">' . $data[$i][$index] . '</a>';
                            }

                        }
                        break;

                    case 'action':
                        $html .= '<td role="action"><div class="quick_action" data-item_id="' . $v_doc_id . '" data-doc_type="' . strtoupper($data[$i]['C_TYPE']) . '">';

                        $arr_action = explode(',', strval($col->attributes()->action));
                        for ($j=0; $j < sizeof($arr_action); $j++)
                        {
                            $act = trim($arr_action[$j]);

                            //Xoa nhanh
                            if ($act === 'delete')
                            {
                                //Kiem tra quyen
                                $v_is_granted_delete = FALSE;
                                if ($v_doc_direction == 'VBDEN')
                                {
                                    $v_is_granted_delete = $this->check_permission('XOA_VAN_BAN_DEN');
                                }
                                if ($v_doc_direction == 'VBDI')
                                {
                                    $v_is_granted_delete = $this->check_permission('XOA_VAN_BAN_DI');
                                }
                                if ($v_doc_direction == 'VBNOIBO')
                                {
                                    $v_is_granted_delete = $this->check_permission('XOA_VAN_BAN_NOI_BO');
                                }

                                //Kiem tra VB da dua vao HS luu chua?
                                if ($v_is_folded > 0)
                                {
                                    $v_is_granted_delete = FALSE;
                                }

                                if ($v_is_granted_delete)
                                {
                                    //Van ban da phan cong thu ly thi khong duoc xoa
                                    $r   = $dom_processing->xpath("//step[@code='submit']/allot_user_id");
                                    $v_allot_user_id    = (sizeof($r) > 0) ? $r[0] : 0;

                                    if ( ($v_allot_user_id == 0) OR ($v_allot_user_id == Session::get('user_id') ))
                                    {
                                        $html .= '<a href="javascript:void(0)" onclick=quick_delete_item(\'' . $data[$i][$primarykey] . '\')>'
                                                . '<img src="' . SITE_ROOT . 'public/images/delete-32x32.png" title="Xoá văn bản" class="quick-action"></a>';
                                    }
                                }
                            }//end Xoa nhanh

                            //Trinh lanh dao
                            if ($act === 'submit')
                            {
                                //Van ban nay da duoc trinh len cho ai?
                                $arr_xpath_result   = $dom_processing->xpath("//step[@code='submit']/allot_user_id");
                                $v_allot_user_id    = (sizeof($arr_xpath_result) > 0) ? $arr_xpath_result[0] : 0;

                                $r = $dom_processing->xpath("//step[@code='reject']");
                                $v_is_rejected = (count($r) > 0) ? TRUE : FALSE;
                                //Neu chua trinh lanh dao, hoac da trinh nhung bi tu choi
                                if ( (intval($v_allot_user_id) < 1) OR ($v_is_rejected) )
                                {
                                    //Kiem tra NSD co quyen Trinh lanh dao khong
                                    $v_is_granted_submit = FALSE;
                                    $v_is_granted_pre_processing = FALSE;
                                    if ($data[$i]['C_DIRECTION'] === 'VBDEN')
                                    {
                                        $v_is_granted_submit = $this->check_permission('TRINH_VAN_BAN_DEN');
                                        $v_is_granted_pre_processing = $this->check_permission('XU_LY_BAN_DAU_VAN_BAN_DEN');

                                        //Da xu ly ban dau hay chua?
                                        $r = $dom_processing->xpath("//step[@code != 'init']");
                                        if (count($r) > 0)
                                        {
                                            $v_is_granted_submit = $v_is_granted_pre_processing = FALSE;
                                        }
                                    }
                                    if ($data[$i]['C_DIRECTION'] === 'VBDI')
                                    {
                                        $v_is_granted_submit = $this->check_permission('TRINH_DUYET_VAN_BAN_DI');
                                    }
                                    if ($data[$i]['C_DIRECTION'] === 'VBNOIBO')
                                    {
                                        $v_is_granted_submit = $this->check_permission('TRINH_DUYET_VAN_BAN_NOI_BO');
                                        //Kiem tra VB da duoc duyet chua?
                                        $v_is_granted_submit = !(count($dom_processing->xpath("//step[@code='approve']")) > 0);
                                    }

                                    if ($v_is_granted_pre_processing)
                                    {
                                        $v_url = $this->get_controller_url();
                                        $v_url .= 'dsp_pre_processing/&doc_id=' . $data[$i][$primarykey]
                                                    . '&hdn_item_id=' . $data[$i][$primarykey]
                                                    . '&pop_win=1'
                                                    . '&direction=' . strtoupper($data[$i]['C_DIRECTION'])
                                                    . '&type=' . strtoupper($data[$i]['C_TYPE']);
                                        $html .= '<a href="javascript:void(0)" onclick="submit_doc_pop_win(\'' . $v_url . '\')">'
                                                        . '<img src="' . SITE_ROOT . 'public/images/allot-32x32.png" onMouseOver="return overlib(\'Xử lý ban đầu\',BELOW, RIGHT);" onMouseOut="return nd();"  /></a>';
                                    }
                                    elseif ($v_is_granted_submit)
                                    {
                                        $v_url = $this->get_controller_url() . $v_doc_direction . '/';
                                        $v_url .= 'dsp_submit_doc/&doc_id=' . $data[$i][$primarykey]
                                                . '&hdn_item_id=' . $data[$i][$primarykey]
                                                . '&pop_win=1'
                                                . '&direction=' . strtoupper($data[$i]['C_DIRECTION'])
                                                . '&type=' . strtoupper($data[$i]['C_TYPE']);
                                        $html .= '<a href="javascript:void(0)" onclick="submit_doc_pop_win(\'' . $v_url . '\')">'
                                                . '<img src="' . SITE_ROOT . 'public/images/submit-32x32.png" onMouseOver="return overlib(\'Trình lãnh đạo\',BELOW, RIGHT);" onMouseOut="return nd();"  /></a>';
                                    }
                                }
                            }//End trinh lanh dao

                            //Phan cong thu ly
                            if ($act === 'allot')
                            {
                                //Van ban nay da phan cong thu ly chua?
                                $r = $dom_processing->xpath("//step[@code='allot']");
                                if (sizeof($r) < 1) //Chua phan cong thu ly
                                {
                                    //Van ban nay da duoc trinh len cho ai?
                                    $arr_xpath_result   = $dom_processing->xpath("//step[@code='submit']/allot_user_id");
                                    $v_allot_user_id    = (sizeof($arr_xpath_result) > 0) ? $arr_xpath_result[0] : 0;

                                    //Trinh len cho TOI, thi TOI moi duoc phan cong thu ly
                                    if ( Session::get('user_id') == $v_allot_user_id )
                                    {
                                        $v_url = $this->get_controller_url();
                                            $v_url .= strtolower($data[$i]['C_DIRECTION']) . '/';
                                            $v_url .= 'dsp_allot_doc/&doc_id=' . $data[$i][$primarykey]
                                                    . '&hdn_item_id=' . $data[$i][$primarykey]
                                                    . '&pop_win=1'
                                                    . '&direction=' . strtoupper($data[$i]['C_DIRECTION'])
                                                    . '&type=' . strtoupper($data[$i]['C_TYPE']);

                                        $html .= '<a href="javascript:void(0)" onclick="allot_doc_pop_win(\'' . $v_url . '\' )">'
                                                . '<img src="' . SITE_ROOT . 'public/images/allot-32x32.png" onMouseOver="return overlib(\'Phân công thụ lý\',BELOW, RIGHT);" onMouseOut="return nd();" /></a>';
                                    }
                                }
                            }//End Phan cong thu ly

                            //Truong phong phan cong cho CB trong phong
                            if ($act === 'sub_allot')
                            {
                                //Kiem tra VB da phan cong ve phong chua
                                $r = $dom_processing->xpath("//step/allot_detail[@type='p']");
                                if (count($r) > 0)
                                {
                                    //Kiem tra quyen phan phoi van ban cua phong
                                    $v_is_granted_sub_allot = FALSE;
                                    if ($data[$i]['C_DIRECTION'] === 'VBDEN')
                                    {
                                        $v_is_granted_sub_allot = $this->check_permission('PHU_TRACH_PHAN_PHOI_VAN_BAN_CUA_PHONG');
                                    }

                                    if ($v_is_granted_sub_allot)
                                    {
                                        $v_url = $this->get_controller_url();
                                                $v_url .= strtolower($data[$i]['C_DIRECTION']) . '/';
                                                $v_url .= 'dsp_sub_allot_doc/&doc_id=' . $data[$i][$primarykey]
                                                        . '&hdn_item_id=' . $data[$i][$primarykey]
                                                        . '&pop_win=1'
                                                        . '&direction=' . strtoupper($data[$i]['C_DIRECTION'])
                                                        . '&type=' . strtoupper($data[$i]['C_TYPE']);

                                            $html .= '<a href="javascript:void(0)" onclick="sub_allot_doc_pop_win(\'' . $v_url . '\' )">'
                                                    . '<img src="' . SITE_ROOT . 'public/images/sub-allot-32x32.png" onMouseOver="return overlib(\'Phân công thụ lý cho cán bộ trong phòng\',BELOW, RIGHT);" onMouseOut="return nd();"/></a>';
                                    }
                                }

                            }

                            //Phe duyet van ban di
                            if ($act === 'approve')
                            {
                                //Van ban duoc trinh len cho ai?
                                $arr_xpath_result   = $dom_processing->xpath("//step[@code='submit']/allot_user_id");
                                $v_allot_user_id    = (sizeof($arr_xpath_result) > 0) ? $arr_xpath_result[0] : 0;

                                //Trinh len cho TOI, thi TOI moi duoc duyet
                                if ( Session::get('user_id') == $v_allot_user_id )
                                {
                                    //Va VB chua dat trang thai "approve" duoc duyet
                                    $r = $dom_processing->xpath("//step[@code='approve']");
                                    if (count($r) < 1)
                                    {
                                        $v_url = $this->get_controller_url();
                                                $v_url .= strtolower($data[$i]['C_DIRECTION']) . '/';
                                                $v_url .= 'dsp_approve_doc/&doc_id=' . $data[$i][$primarykey]
                                                        . '&hdn_item_id=' . $data[$i][$primarykey]
                                                        . '&pop_win=1'
                                                        . '&direction=' . strtoupper($data[$i]['C_DIRECTION'])
                                                        . '&type=' . strtoupper($data[$i]['C_TYPE']);

                                        $html .= '<a href="javascript:void(0)" onclick="approve_doc_pop_win(\'' . $v_url . '\' )">'
                                                . '<img src="' . SITE_ROOT . 'public/images/approve.png" title="Phê duyệt văn bản đi" width="24px" height="24px"/></a>';
                                    }
                                }
                            }//End Phe duyet van ban di

                            //Cap nhat tien do xy ly van ban
                            if ($act === 'exec')
                            {
                                //Kiem tra van ban da o trang thai hoan thanh chua?
                                //$r = $dom_processing->xpath("//step[@code='commit']");
                                //if (count($r) < 1)
                                //{
                                    //Kiem tra van ban da duoc phan cong thu ly chua?
                                    $r = $dom_processing->xpath("//step[@code='allot']");
                                    if (count($r) > 0)
                                    {
                                        //Kiem tra quyen thu ly van ban
                                        $v_is_granted_exec = FALSE;
                                        if ($data[$i]['C_DIRECTION'] === 'VBDEN')
                                        {
                                            $v_is_granted_exec = $this->check_permission('THU_LY_VAN_BAN_DEN');
                                        }

                                        if ($v_is_granted_exec)
                                        {
                                            $v_url = $this->get_controller_url();
                                                        $v_url .= strtolower($data[$i]['C_DIRECTION']) . '/';
                                                        $v_url .= 'dsp_exec_doc/&doc_id=' . $data[$i][$primarykey]
                                                                . '&hdn_item_id=' . $data[$i][$primarykey]
                                                                . '&pop_win=1'
                                                                . '&direction=' . strtoupper($data[$i]['C_DIRECTION'])
                                                                . '&type=' . strtoupper($data[$i]['C_TYPE']);

                                            $html .= '<a href="javascript:void(0)" onclick="exec_doc_pop_win(\'' . $v_url . '\' )">'
                                                    . '<img src="' . SITE_ROOT . 'public/images/exec-32x32.png" onMouseOver="return overlib(\'Cập nhật tiến độ thụ lý\',BELOW, RIGHT);" onMouseOut="return nd();" /></a>';
                                        } // End $v_is_granted_exec
                                    } // End Kiem tra phan cong thu ly
                                //} //End kiemtr trang thai hoan thanh
                            }//end Cap nhat tien do xy ly van ban
                        }

                        //Dua vao HS Luu
                        $v_be_folded = TRUE;
                        if (strtoupper($data[$i]['C_DIRECTION']) == 'VBTRACUU')
                        {
                            $v_be_folded = FALSE;
                        }
                        //VB di chua co so
                        if (strtoupper($data[$i]['C_DIRECTION']) == 'VBDI' && $v_doc_so_ky_hieu=='')
                        {
                            $v_be_folded = FALSE;
                        }


                        if ($v_be_folded)
                        {
                            $v_url = $this->get_controller_url() . $v_doc_direction . '/';
                            $v_url .= 'dsp_add_doc_to_folder/&doc_id=' . $data[$i][$primarykey]
                                    . '&hdn_item_id=' . $data[$i][$primarykey]
                                    . '&pop_win=1'
                                    . '&direction=' . strtoupper($data[$i]['C_DIRECTION'])
                                    . '&type=' . strtoupper($data[$i]['C_TYPE']);
                            $html .= '<a href="javascript:void(0)" onclick="submit_doc_pop_win(\'' . $v_url . '\')">'
                                        . '<img src="' . SITE_ROOT . 'public/images/add-folder-32x32.png"
                                            onMouseOver="return overlib(\'Đưa vào hồ sơ lưu\',BELOW, RIGHT);" onMouseOut="return nd();"
                                            />
                                        </a>';
                            //$html .= strtoupper($data[$i]['C_DIRECTION']);
                        }

                        $html .= '</div></td>';

                        break;
                    }
                }
                $html .= '</tr>';
                $row_index++;
            //}//end _check_user_duty
        }

        $html .= $this->add_empty_rows($row_index + 1,_CONST_DEFAULT_ROWS_PER_PAGE,count($cols));
        $html .= '</table>';
        return $html;

    }

	

}


