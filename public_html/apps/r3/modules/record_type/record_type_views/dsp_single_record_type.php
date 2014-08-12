<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
//header
$this->active_menu =  'quan_tri_ho_so';
$this->template->title =  $this->title = $this->title = 'Quản trị danh mục loại hồ sơ (thủ tục)';
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header.php');
?>

<div class="container-fluid">
    <div class="row-fluid ">
        <ul class="breadcrumb">
            <li><a href="<?php echo SITE_ROOT;?>" class="icon-home"></a><span class="divider "><i class="icon-angle-right"></i></span></li>
            <li class="active">Quản trị hệ thống<span class="divider"><i class="icon-angle-right"></i></span></li>
            <li class="active">Loại hồ sơ </li>
        </ul>
        <?php
        $arr_single_record_type = $VIEW_DATA['arr_single_record_type'];
        if (isset($arr_single_record_type['PK_RECORD_TYPE']))
        {
            $v_record_type_id      = $arr_single_record_type['PK_RECORD_TYPE'];
            $v_xml_data            = $arr_single_record_type['C_XML_DATA'];
            $v_code                = trim($arr_single_record_type['C_CODE']);
            $v_name                = $arr_single_record_type['C_NAME'];
            $v_status              = $arr_single_record_type['C_STATUS'];
            $v_scope               = $arr_single_record_type['C_SCOPE'];
            $v_order               = $arr_single_record_type['C_ORDER'];
            $v_send_over_internet  = $arr_single_record_type['C_SEND_OVER_INTERNET'];
            $v_spec_code           = $arr_single_record_type['C_SPEC_CODE'];
            $v_allow_verify_record = $arr_single_record_type['C_ALLOW_VERIFY_RECORD'];
            
            $dom_xml_data = simplexml_load_string($arr_single_record_type['C_XML_DATA']);
            
            $v_cach_thuc_thuc_hien = get_xml_value($dom_xml_data, "//item[@id='cach_thuc_thuc_hien']/value");
            $v_thoi_han_giai_quyet = get_xml_value($dom_xml_data, "//item[@id='thoi_han_giai_quyet']/value");
            $v_doi_tuong_thuc_hien = get_xml_value($dom_xml_data, "//item[@id='doi_tuong_thuc_hien']/value");
            $v_phi_le_phi          = get_xml_value($dom_xml_data, "//item[@id='le_phi']/value");
            $v_trinh_tu_thuc_hien  = get_xml_value($dom_xml_data, "//item[@id='txta_trinh_tu_thuc_hien']/value");
            $v_ho_so               = get_xml_value($dom_xml_data, "//item[@id='txta_ho_so']/value");
            $v_co_quan_thuc_hien   = get_xml_value($dom_xml_data, "//item[@id='txta_co_quan_thuc_hien']/value");
            $v_ket_qua             = get_xml_value($dom_xml_data, "//item[@id='txta_ket_qua']/value");
            $v_can_cu_phap_ly      = get_xml_value($dom_xml_data, "//item[@id='txta_can_cu_phap_ly']/value");
        }
        else
        {
            $v_record_type_id      = 0;
            $v_xml_data            = '';
            $v_code                = '';
            $v_name                = '';
            $v_status              = 1;
            $v_scope               = 3;
            $v_order               = $arr_single_record_type['C_ORDER'];
            $v_send_over_internet  = 0;
            $v_allow_verify_record = 0;
            
            $dom_xml_data = simplexml_load_string('<data/>');
            $v_cach_thuc_thuc_hien = '';
            $v_thoi_han_giai_quyet = '';
            $v_doi_tuong_thuc_hien = '';
            $v_phi_le_phi          = '';
            $v_trinh_tu_thuc_hien  = '';
            $v_ho_so               = '';
            $v_co_quan_thuc_hien   = '';
            $v_ket_qua             = '';
            $v_can_cu_phap_ly      = '';
        }
        ?>
        <form name="frmMain" method="post" id="frmMain" enctype="multipart/form-data" action=""class="form-horizontal"><?php
            echo $this->hidden('controller', $this->get_controller_url());

            echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record_type');
            echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record_type');
            echo $this->hidden('hdn_update_method', 'update_record_type');
            echo $this->hidden('hdn_delete_method', 'delete_record_type');
            
            echo $this->hidden('hdn_delete_file_list_id', '');
            
            echo $this->hidden('hdn_item_id', $v_record_type_id);
            echo $this->hidden('XmlData', $v_xml_data);

            $this->write_filter_condition(array('txt_filter', 'sel_goto_page', 'sel_rows_per_page'));
            ?>
            
            <div class="content-widgets light-gray">
                <div class="widget-head blue">
        			<h3>Cập nhật loại hồ sơ</h3>
        		</div>
                
                <div class="widget-container">
        		    <div class="control-group">
            	        <label class="control-label">Mã loại hồ sơ <span class="required">(*)</span></label>
            	        <div class="controls">
            	            <input type="text" name="txt_code" value="<?php echo trim($v_code); ?>" id="txt_code"
                                class="inputbox" maxlength="50" size="10"
                                onKeyDown="handleEnter(this, event);" onkeyup="ConverUpperCase('txt_code', this.value)"
                                data-allownull="no" data-validate="text"
                                data-name="Mã loại hồ sơ"
                                data-xml="no" data-doc="no" autofocus="autofocus"
                            />
            			</div>
            		</div>
                    
        		    <div class="control-group">
            	        <label class="control-label">Tên loại hồ sơ <span class="required">(*)</span></label>
            	        <div class="controls">
            	            <textarea name="txt_name" id="txt_name"
                              class="inputbox" style="width:80%"
                              data-allownull="no" data-validate="text"
                              data-name="Tên loại hồ sơ"
                              data-xml="no" data-doc="no" rows="3"
                              ><?php echo $v_name; ?></textarea>   
            			</div>
            		</div>
                    
        		    <div class="control-group">
            	        <label class="control-label">Thuộc Lĩnh vực <span class="required">(*)</span></label>
            	        <div class="controls">
            	            <select name="sel_spec_code" id="sel_spec_code" class="ddl" data-allownull="no" data-validate="ddli" 
                                data-name="Lĩnh vực" data-xml="no" data-doc="no"
                            >
                                <option value="">--Chọn lĩnh vực--</option>
                                <?php echo $this->generate_select_option($arr_all_spec, $v_spec_code); ?>
                            </select>
            			</div>
            		</div>
                    
        		    <div class="control-group">
            	        <label class="control-label">Thuộc thuộc sổ theo dõi <span class="required">(*)</span></label>
            	        <div class="controls">
            	            <?php foreach ($arr_all_report_books as $book): ?>
                                <?php
                                $checked = $book['C_IS_CHECKED'] ? 'checked' : '';
                                ?>
                                <label for="chk_report_book_<?php echo $book['PK_LIST'] ?>">
                                    <input 
                                        id="chk_report_book_<?php echo $book['PK_LIST'] ?>"
                                        type="checkbox" name="chk_report_book[]" 
                                        value="<?php echo $book['C_CODE'] ?>" 
                                        <?php echo $checked ?>
                                        />
                                        <?php echo $book['C_NAME'] ?>
                                </label>
                            <?php endforeach; ?>
            			</div>
            		</div>
                    
        		    <div class="control-group">
            	        <label class="control-label">Phạm vi <span class="required">(*)</span></label>
            	        <div class="controls">
                            <label class="radio">
                                <input type="radio" name="rad_scope" value="0" id="rad_scope_0" <?php echo ($v_scope == 0) ? ' checked' : ''; ?>/> Cấp xã/phường
                            </label>
                            
                            <label class="radio">
                                <input type="radio" name="rad_scope" value="1" id="rad_scope_1" <?php echo ($v_scope == 1) ? ' checked' : ''; ?> />Liên thông Xã -> Huyện
                            </label>
                            
                            <label class="radio">
                                <input type="radio" name="rad_scope" value="2" id="rad_scope_2" <?php echo ($v_scope == 2) ? ' checked' : ''; ?> />Liên thông Huyện -> Xã
                            </label>

                            <label class="radio">
                                <input type="radio" name="rad_scope" value="3" id="rad_scope_3" <?php echo ($v_scope == 3) ? ' checked' : ''; ?> />Cấp Huyện
                            </label>
            			</div>
            		</div>
                    
        		    <div class="control-group">
            	        <label class="control-label">Dịch vụ công trực tuyến</label>
            	        <div class="controls">
                            <?php $checked         = ($v_send_over_internet) ? ' checked' : ''; ?>
                            <label class="checkbox">
                                <input type="checkbox" name="chk_send_over_internet" value="1" <?php echo $checked ?> id="chk_send_over_internet" />
                                <?php echo __('send over internet'); ?>
                            </label>
                            
                            <?php $checked         = ($v_allow_verify_record) ? ' checked' : ''; ?>
                            <label class="checkbox">
                                <input type="checkbox" name="chk_allow_verify_record" value="1" <?php echo $checked ?> id="chk_allow_verify_record" />
                                Cho phép nộp thử để kiểm tra tính hợp lệ
                            </label>
            			</div>
            		</div>
                        <div class="control-group">
                            <label class="control-label"><?php echo __('order'); ?> <span class="required">(*)</span></label>
                            <div class="controls">
                                <input type="text" name="txt_order" value="<?php echo $v_order; ?>" id="txt_order"
                                       class="input-small" maxlength="50" size="10"
                                       onKeyDown="handleEnter(this, event);"
                                       data-allownull="no" data-validate="number"
                                       data-name="<?php echo __('order'); ?>"
                                       data-xml="no" data-doc="no"
                                       />
                            </div>
                        </div>
                    
        		    <div class="control-group">
            	        <label class="control-label"><?php echo __('status'); ?></label>
            	        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" name="chk_status" value="1" <?php echo ($v_status > 0) ? ' checked' : ''; ?> id="chk_status" />
                                <?php echo __('active status'); ?>
                            </label>
                            
                            <label class="checkbox">
                                <input type="checkbox" name="chk_save_and_addnew" value="1" <?php echo ($v_record_type_id > 0) ? '' : ' checked'; ?> id="chk_save_and_addnew" />
                                <?php echo __('save and add new'); ?>
                            </label>
            			</div>
            		</div>
                    
                    <!-- XML data -->
                    <?php
                    $v_xml_file_name = 'xml_record_type_edit.xml';
                    if ($this->load_xml($v_xml_file_name))
                    {
                        echo $this->render_form_display_single();
                    }
                    ?>
                    <div class="form-actions">
                        <button type="button" name="update" class="btn btn-primary" onclick="btn_update_onclick();"><i class="icon-save"></i><?php echo __('update');?></button>
                        <button type="button" name="cancel" class="btn" onclick="btn_back_onclick()"><i class="icon-reply"></i><?php echo __('go back'); ?></button>
                        <button type="button" name="btn_view_workflow" class="btn btn-info" onclick="window.open('<?php echo $this->get_controller_url('workflow'); ?>' + QS + 'sel_record_type=<?php echo $v_code; ?>');"><i class="icon-search"></i>Xem quy trình xử lý hồ sơ</button>
                        <button type="button" name="btn_view_form" class="btn btn-info" onclick="btn_dsp_plaintext_form_struct_onclick()"><i class="icon-search"></i>Xem biểu mẫu đơn</button>
                        <?php 
                            if(intval($v_record_type_id) >0)
                            {
                                echo '<button type="button" name="btn_view_workflow" class="btn btn-info" onclick="window.open(\''.SITE_ROOT .'guidance/guidance/dsp_single_guidance/'. $v_record_type_id .'\');"><i class="icon-search"></i>Xem thông tin hướng dẫn tại cổng thông tin</button>';
                            }   
                        ?>
                        
                    </div>
                </div><!-- / .widget-container -->
            </div><!-- /.content-widgets light-gray-->
            <div class="content-widgets light-gray">
                <div class="widget-head blue">
        			<h3>Thông tin hướng dẫn thủ tục hành chính</h3>
        		</div>
        		<div class="widget-container">
                    <div class="control-group">
            	        <label class="control-label">Cách thức thực hiện:</label>
            	        <div class="controls">
                            <input type="text" name="cach_thuc_thuc_hien" id="cach_thuc_thuc_hien" value="<?php echo $v_cach_thuc_thuc_hien;?>" class="span6" 
                                data-allownull="yes" data-validate="text"
                                data-name="Cách thức thực hiện"
                                data-xml="yes" data-doc="no"
                            />
            			</div>
            		</div>
                    
                    <div class="control-group">
            	        <label class="control-label">Thời hạn giải quyết:</label>
            	        <div class="controls">
                            <input type="text" name="thoi_han_giai_quyet" id="thoi_han_giai_quyet" size="60" value="<?php echo $v_thoi_han_giai_quyet;?>" class="span6"
                                data-allownull="yes" data-validate="text"
                                data-name="Thời hạn giải quyết"
                                data-xml="yes" data-doc="no"
                            />
            			</div>
            		</div>
                    <div class="control-group">
            	        <label class="control-label">Đối tượng thực hiện:</label>
            	        <div class="controls">
                            <input type="text" name="doi_tuong_thuc_hien" id="doi_tuong_thuc_hien" size="60" value="<?php echo $v_doi_tuong_thuc_hien;?>" class="span6"
                                data-allownull="yes" data-validate="text"
                                data-name="Đối tượng thực hiện"
                                data-xml="yes" data-doc="no"
                            />
            			</div>
            		</div>
                    
                    <div class="control-group">
            	        <label class="control-label">Phí, lệ phí</label>
            	        <div class="controls">
                            <input type="text" name="le_phi" id="le_phi" size="60" value="<?php echo $v_phi_le_phi;?>" class="span6" 
                                data-allownull="yes" data-validate="text"
                                data-name="Phí, lệ phí"
                                data-xml="yes" data-doc="no"
                            />
            			</div>
            		</div>
                    
                    <div class="control-group">
            	        <label class="control-label">Trình tự thực hiện:</label>
            	        <div class="controls">
                            <div id="container_trinh_tu_thuc_hien">
                                <textarea id="txta_trinh_tu_thuc_hien" name="txta_trinh_tu_thuc_hien" class="span6"  
                                    data-allownull="yes" data-validate="text"
                                    data-name="Phí, lệ phí"
                                    data-xml="yes" data-doc="no" ><?php echo $v_trinh_tu_thuc_hien;;?></textarea>
                            </div>
            			</div>
            		</div>
                    <div class="control-group">
            	        <label class="control-label">Hồ sơ:</label>
            	        <div class="controls">
                            <div id="container_ho_so">
                                <textarea id="txta_ho_so" name="txta_ho_so" class="span6" 
                                    data-allownull="yes" data-validate="text"
                                    data-name="Phí, lệ phí"
                                    data-xml="yes" data-doc="no"><?php echo $v_ho_so;?></textarea>
                            </div>
            			</div>
            		</div>
                    <div class="control-group">
            	        <label class="control-label">Cơ quan thực hiện:</label>
            	        <div class="controls">
                            <div id="container_co_quan_thuc_hien">
                                <textarea id="txta_co_quan_thuc_hien" name="txta_co_quan_thuc_hien" class="span6"
                                     data-allownull="yes" data-validate="text"
                                    data-name="Phí, lệ phí"
                                    data-xml="yes" data-doc="no"><?php echo $v_co_quan_thuc_hien;?></textarea>
                            </div>
            			</div>
            		</div>
                    <div class="control-group">
            	        <label class="control-label">Kết quả:</label>
            	        <div class="controls">
                            <div id="container_ket_qua">
                                <textarea id="txta_ket_qua" name="txta_ket_qua" class="span6"  
                                    data-allownull="yes" data-validate="text"
                                    data-name="Phí, lệ phí"
                                    data-xml="yes" data-doc="no"><?php echo $v_ket_qua;?></textarea>
                            </div>
            			</div>
            		</div>
                    <div class="control-group">
            	        <label class="control-label">Căn cứ pháp lý:</label>
            	        <div class="controls">
                            <textarea id="txta_can_cu_phap_ly" name="txta_can_cu_phap_ly" class="span6" 
                                data-allownull="yes" data-validate="text"
                                data-name="Phí, lệ phí"
                                data-xml="yes" data-doc="no"><?php echo $v_can_cu_phap_ly;?></textarea>
                        </div>
            		</div>
                    <div class="control-group">
            	        <label class="control-label">File đính kèm:</label>
            	        <div class="controls">
                            <div class="control-group">
                                <div id="attach_file_list">
                                    <div class="file span12">
                                        <div class="span6">
                                            <?php
                                                $arr_accept = explode(',', _CONST_TYPE_FILE_ACCEPT);
                                                $class      = '';
                                                foreach ($arr_accept as $ext)
                                                {
                                                    $class .= " accept-$ext";
                                                }
                                            ?>
                                            <input type="file" 
                                                   style="border: solid #D5D5D5; color: #000000"
                                                   class="multi <?php echo $class; ?>"
                                                   name="uploader[]" id="File1"/> 
                                            <font style="font-weight: normal;">Hệ thống chỉ chấp nhận đuôi file pdf,doc</font><br/>                               
                                        </div>
                                        
                                        <div class="span4 attachment-file-name">
                                            <?php  if($arr_all_template_file):?>
                                            
                                            <?php foreach ($arr_all_template_file as $key => $val):; ?>
                                            <?php
                                                    $v_file_name = $val['file_name'];
                                                    $v_file_path = $val['path'];
                                                    $v_file_type = $val['type'];
                                                    $v_key = $key;
                                                    $v_div_id_file = explode('_', $v_key,2);
                                             ?>
                                            <div id="FILE_<?php echo $v_div_id_file[0]; ?>" >
                                                <div  class="attachment-thumbnail">
                                                        <img  src="<?php echo SITE_THEME_ROOT; ?>images/document.png" alt="Thumbnail">
                                                </div>
                                                <div class="attachment-action">
                                                    <input type="hidden" name="hdn_file_name[]" value="mau-tthc-tnmt.pdf" />
                                                    <div class="filename"><?php echo $v_file_name; ?></div>
                                                    <div class="edit-attachment-asset"><a href="javascript:void(0);" div_parent_id="<?php echo $v_div_id_file[0]; ?>" file-id="<?php echo $v_key; ?>"  onclick="onlick_delete_file(this)"><i class="icon-trash"></i> Xoá file</a></div>
                                                </div>  
                                            </div>
                                                <?php endforeach; ?>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
            		</div>
                      <td>Thêm file đính kèm:</td>
                    <td colspan="2">
                    <div class="form-actions">
                        <button type="button" name="update" class="btn btn-primary" onclick="btn_update_onclick();"><i class="icon-save"></i><?php echo __('update');?></button>
                        <button type="button" name="cancel" class="btn" onclick="btn_back_onclick()"><i class="icon-reply"></i><?php echo __('go back'); ?></button>
                        <button type="button" name="btn_view_workflow" class="btn btn-info" onclick="window.open('<?php echo $this->get_controller_url('workflow'); ?>' + QS + 'sel_record_type=<?php echo $v_code; ?>');"><i class="icon-search"></i>Xem quy trình xử lý hồ sơ</button>
                        <button type="button" name="btn_view_form" class="btn btn-info" onclick="btn_dsp_plaintext_form_struct_onclick()"><i class="icon-search"></i>Xem biểu mẫu đơn</button>
                        <?php 
                            if(intval($v_record_type_id) >0)
                            {
                                echo '<button type="button" name="btn_view_workflow" class="btn btn-info" onclick="window.open(\''.SITE_ROOT .'guidance/guidance/dsp_single_guidance/'. $v_record_type_id .'\');"><i class="icon-search"></i>Xem thông tin hướng dẫn tại cổng thông tin</button>';
                            }   
                        ?>
                    </div>
                </div>
            </div>
        </form>
    </div><!-- ./row-fluid -->
</div><!-- / .container-fluid -->

<script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery.cleditor.js"></script>
<script>
    function onlick_delete_file(anchor) 
    {
        if(confirm('Bạn có chắc chắn xóa file này?'))
        {
            var file_id         =  $(anchor).attr('file-id');
            var div_parent_id   = $(anchor).attr('div_parent_id'); 
            var v_list_id = $('#hdn_delete_file_list_id').val() || '';
            if(v_list_id.trim().length > 0)
            {
                 v_list_id += '|' + file_id;
            }
            else
            {
                v_list_id = file_id;
            }
            $('#FILE_' + div_parent_id).remove();
            $('#hdn_delete_file_list_id').val(v_list_id);
        }
    }
    
    
    function btn_dsp_plaintext_form_struct_onclick()
    {
        var url = '<?php echo $this->get_controller_url(); ?>dsp_plaintext_form_struct/' + QS + 'sel_record_type=<?php echo $v_code; ?>';
        url += '&pop_win=1';

        showPopWin(url, 1000, 550);
    }
    
   
    
    //Setup EDITOR
    jQuery(function() {
        editor = jQuery("#txta_trinh_tu_thuc_hien").cleditor();
        editor = jQuery("#txta_ho_so").cleditor();
        editor = jQuery("#txta_co_quan_thuc_hien").cleditor();
        editor = jQuery("#txta_ket_qua").cleditor();
        editor = jQuery("#txta_can_cu_phap_ly").cleditor();
    });
</script>
<?php require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer.php');