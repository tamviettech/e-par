<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
echo $this->hidden('controller', $this->get_controller_url());
?>
<div  class="group-option" id="page-list-registration">
    <form class="form-horizontal" name="frmMain" id="frmMain" action="" method="post"  >    
    <div  class="span12" >
        <div id="accordion">
           <?php  for($i = 0; $i < sizeof($arr_all_record_type); $i ++): ;?>
            <?php
                $v_list_id      = $arr_all_record_type[$i]['PK_LIST'];
                $v_list_code    = $arr_all_record_type[$i]['C_CODE'];
                $v_list_name    = $arr_all_record_type[$i]['C_NAME'];
                $v_XML_DATA     = $arr_all_record_type[$i]['C_XML_DATA'];
                $arr_all_file   = isset($arr_all_record_type[$i]['all_file']) ? $arr_all_record_type[$i]['all_file']  :array() ;
                if(sizeof($v_XML_DATA) <= 0)
                {
                    continue;
                }
                $dom = simplexml_load_string($v_XML_DATA,'SimpleXMLElement',LIBXML_NOCDATA);
                $x_path = "//item";
                $arr_item = $dom->xpath($x_path);
                $v_count_record_type = sizeof($arr_item);    
                  
           ?>
            <h3 class="title "><strong>Lĩnh vực:</strong> <?php echo $v_list_name;?> <span style="float: right;margin-right: 5px;"><?php echo $v_count_record_type; ?> thủ tục</span></h3>
            <div >
                <table>
                    <colgroup>
                        <col width="5%" />
                        <col width="10%"/>
                        <col width="55%"/>
                        <col width="15%"/>
                        <col width="10%"/>
                    </colgroup>
                     <thead style="cursor: context-menu;background: rgba(250, 250, 250, 0.29);" >
                        <th style=" border: solid 1px #CACACA; height: 30px; color: rgb(94, 86, 86);; font-weight: bold; cursor: context-menu ">STT</th>
                        <th style=" border: solid 1px #CACACA; width:100px;color:  rgb(94, 86, 86);; font-weight: rgb(94, 86, 86);;cursor: context-menu ">Mã thủ tục</th>
                        <th style=" border: solid 1px #CACACA; color:white;text-align: left;padding-left: 20px;color:  rgb(94, 86, 86);; font-weight: bold;cursor: context-menu ">Danh sách các thủ tục</th>
                        <th style=" border: solid 1px #CACACA; color:white;text-align: center;padding-left: 20px;color:  rgb(94, 86, 86);; font-weight: bold;cursor: context-menu ">Tệp đính kèm</th>
                        <th style=" border: solid 1px #CACACA; color:white;text-align: center;padding-left: 20px;color:  rgb(94, 86, 86);; font-weight: bold;cursor: context-menu ">Phạm vi</th>
                    </thead>
                    <?php for($j =0;$j< sizeof($arr_item);$j ++):; ?>
                    <?php
                        $v_record_type_id       = (string) $arr_item[$j]->attributes()->PK_RECORD_TYPE;
                        $v_record_type_code     = (string) $arr_item[$j]->attributes()->C_CODE;
                        $v_record_type_name     = (string) $arr_item[$j][0];
                        $v_record_type_specode  = $arr_item[$j]['C_SCOPE'];                        
                        $v_url                  = $this->get_controller_url() . 'dsp_single_guidance/' . $v_record_type_id;
                        
                    ?>
                    <tr>
                        <td style="text-align:center"><?php echo ($j + 1); ?></td>
                        <td style="text-align:center"><?php echo $v_record_type_code; ?></td>
                        <td>
                            <a href="<?php echo $v_url; ?>"><?php echo $v_record_type_name; ?></a>
                        </td>
                        <td  style="padding-left: 5px;">
                                <?php if(key_exists($v_record_type_id, $arr_all_file)): ?>
                                    <?php   
                                        $arr_all_file_single_record_tyle = isset($arr_all_file[$v_record_type_id]) ? $arr_all_file[$v_record_type_id] :array();  
                                          
                                        foreach ($arr_all_file_single_record_tyle as $single_file):
                                            $v_file_name        = $single_file['file_name'];
                                            $v_name             = $single_file['name'];
                                            $v_file_type        = $single_file['type'];
                                            $arr_all_icon_file  = json_decode(CONTS_ICON_FILE_GUIDANCE, TRUE);
                                            $v_url_icon = FULL_SITE_ROOT."apps/public_service/images/default_tempalte.png";
                                            
                                            if(key_exists($v_file_type, $arr_all_icon_file))
                                            {
                                                $v_url_icon =  FULL_SITE_ROOT.'apps/public_service/images/'.$arr_all_icon_file[$v_file_type];
                                            }
                                            $v_url = $this->get_controller_url().'download?file_name='. md5($v_file_name) .'&record_code=' . $v_record_type_code . '&name='.$v_name;
                                    ?>
                                            <a class="icon-file-dowload" style="padding:5px; padding-top: 3px;padding-bottom: 3px;display: block;float: left" title="<?php echo $v_name;?>" href="<?php echo $v_url;?>"><img src="<?php echo $v_url_icon;?>" width="15px" height="auto" ></a>
                                    <?php  endforeach; ?>
                                <?php  endif; ?>
                        </td>
                        <td>
                            <label class="acction" style="text-align: center"><?php echo $v_record_type_specode; ?></label>
                            <div class="" style="text-align: center;margin-bottom: 5px">
                                <a class="regis" href="<?php echo FULL_SITE_ROOT . 'nop_ho_so/nhap_thong_tin/' . $v_record_type_id; ?>" > Đăng ký</button</a>
                            </div>
                        </td>
                    </tr>
                    <?php endfor; ?>
                </table>
            </div>
            <!----1----->
            <?php  endfor;?>
        </div>
        <!--End .span12-->
    </div>
    </form>
</div>
<div class="clear" ></div>
<br/>
 
<script>
    $(function() {
        $( "#accordion" ).accordion();
    });
</script>