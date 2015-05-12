 
    <div class="span4" id="side-bar-right">
        <div id="box-search">
            <div class="span12">
                <div  class="content-widgets light-gray">
                    <div class="widget-head blue">
                        <h3>Tra cứu</h3>
                    </div>
                    <!--End .widget-head blue-->
                        
                    <div id="box-search-content" class="widget-container">
                        <form class="form-horizontal" name="frmMain" id="frmMain" action="<?php echo $this->get_controller_url(); ?>dsp_search" method="post"  >
                            <div class="control-group">
                                
                                <label class="control-label span3">Mã hồ sơ &nbsp;</label>
                                <div class="controls" style="margin-left: 65px">                             
                                    <input class="span7" type="text" style="" name="txt_record_no" onchange="$(this).val($(this).val().toUpperCase());" id="txt_record_no" maxlength="100" onkeypress=" txt_filter_onkeypress(this.form.btn_filter, event)" value="">
                                    <button type="submit" class="btn btn-primary" onclick=""> Xác nhận <i class="icon-search"></i></button>
                                </div>
                            </div>
                            <!--End #txt_code-->
                                
                                
                            <!--End btn submit-->
                        </form>
                        <!--End #frmMain-->
                    </div>
                </div>
            </div>
        </div>
        <!--end box-search-->
        
<!--        <div id="side-bar box-category">
            <div class="span12">
                <div  class="content-widgets light-gray">
                    <div class="widget-head blue">
                        <h3>Chuyên mục</h3>
                    </div>
                    End .widget-head blue
                        
                    <div id="box-catgory-content" class="widget-container">
                        <ul>
                            <?php
                                $arr_all_category = isset($arr_all_category) ? $arr_all_category : array();
                                for($i = 0; $i < count($arr_all_category); $i++):;
                                    $v_cat_id           = $arr_all_category[$i]['PK_CATEGORY'];
                                    $v_cat_parent_id    = $arr_all_category[$i]['FK_PARENT'];
                                    $v_cat_name         = $arr_all_category[$i]['C_NAME'];
                                    $v_cat_slug         = $arr_all_category[$i]['C_SLUG'];
                                    $v_internal_order   = $arr_all_category[$i]['C_INTERNAL_ORDER'];
                                    $v_level_index      =  strlen($v_internal_order) / 3 - 1; 
                                    $v_url              = build_url_category($v_cat_slug,$v_cat_id) ;
                                    
                                    $v_current_class = '';
                                    $v_current_index = 0;
                                    if(isset($_GET['category_id']) == TRUE && $_GET['category_id'] == $v_cat_id)
                                    {
                                        $v_current_class = ' current';
                                        if ($v_level_index == 0)
                                        {
                                            $v_selected_menu = $v_current_index;
                                        }
                                    }
                                    else
                                    {
                                        $v_current_class = '';
                                    }

                                    if (isset($arr_all_category[$i - 1]['C_INTERNAL_ORDER']))
                                    {
                                        $v_internal_order_pre = $arr_all_category[$i - 1]['C_INTERNAL_ORDER'];
                                    }
                                    else
                                    {
                                        $v_internal_order_pre = '000';
                                    }

                                    if ($v_level_index == 0)
                                    {
                                        $v_current_index++;
                                    }
                                    $v_level_pre = strlen($v_internal_order_pre) / 3 - 1;
                                ?>
                        
                            <?php if ($v_level_pre == $v_level_index && $i != 0): ?>
                            </li>
                            <?php elseif ($v_level_pre < $v_level_index): ?>
                                <ul>
                            <?php
                                elseif ($v_level_pre > $v_level_index):
                                    echo "</li>";
                                    for ($n = 0; $n < ($v_level_pre - $v_level_index); $n++)
                                    {
                                        echo "</ul>";
                                        echo "</li>";
                                    }
                            ?>
                            <?php endif; ?>
                            
                            <li class="cat-<?php echo $v_cat_id ?><?php echo $v_current_class; ?>">
                                <a href="<?php echo $v_url; ?>" title="<?php echo $v_cat_name; ?>">
                                    <?php echo $v_cat_name; ?> 
                                </a>
                            <?php endfor; ?>
                        </ul>
                        
                    </div>
                </div>
            </div>
        </div>-->

        <!--End #box-category-->
        
         <div class="side-bar" id="box-charts">
            <div class="span12">
                <div  class="content-widgets light-gray">
                    <a href="<?php echo $this->get_controller_url()?>dsp_statistic">
                    <div class="widget-head blue">
                        <h3><img src="<?php echo SITE_ROOT?>apps/public_service/images/statistics-16x16.png" height="30px" width="30px" /> &nbsp;Biểu đồ thống kê</h3>
                    </div>
                </a>
                    <!--End .widget-head blue-->
                </div>
            </div>
        
        </div>
        <!--End #box-category-->
        
        <div clas="side-bar" id="poll">
            <div class="span12">
                <div id="box-poll" class="content-widgets light-gray">
                <div class="widget-head blue">
                    <h3>Thăm dò ý kiến</h3>
                </div>
                    <!--End .widget-head blue-->
                    <div id="box-poll-content" class="widget-container">
                        
                        <?php if (!empty($arr_single_poll)): ?>
                            <?php
                            $v_poll_name = $arr_single_poll['C_NAME'];
                            $v_poll_id   = $arr_single_poll['PK_POLL'];
                            $ck_begin    = $arr_single_poll['CK_BEGIN_DATE'];
                            $ck_end      = $arr_single_poll['CK_END_DATE'];
                            $v_disable   = Cookie::get('WIDGET_POLL_' . $v_poll_id) ? 'disabled' : ''; 
                        ?>

                            <p class='widget-content-title'><?php echo $v_poll_name ?></p>

                            <form>
                                <input type='hidden' id="hdn_poll_id" name='hdn_poll_id' value='<?php echo $v_poll_id ?>'/>
                                <input type='hidden' name='hdn_answer_id' value=''/>
                                <?php 
                                    $arr_all_opt = isset($arr_all_opt) ? $arr_all_opt :array();
                                    $n = count($arr_all_opt); 
                                ?>
                                <?php
                                for ($i = 0; $i < $n; $i++):
                                    ?>
                                    <?php
                                    $item = $arr_all_opt[$i];
                                    $v_opt_val = $item['PK_POLL_DETAIL'];
                                    $v_opt_answer = $item['C_ANSWER'];
                                    ?>
                                    <label>
                                        <input type="radio" name='rad_widget_poll_<?php echo $v_index ?>' value='<?php echo $v_opt_val ?>' onclick="this.form.hdn_answer_id.value=this.value"/>
                                        <?php echo $v_opt_answer ?></br>
                                    </label>

                                <?php endfor; ?>
                                </br>
                                <?php if ($ck_begin < 0 or $ck_end < 0): ?>
                                    <?php echo __('this poll is expired'); ?>
                                <?php else: ?>
                                    <a class="vote" href="javascript:;" onCLick='btn_vote_onclick(this);' >
                                        <span>
                                            <?php echo $v_disable ? __('thank you for voting') : __('vote') ?>
                                        </span>
                                    </a>
                                <?php endif; ?>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <a class="a_poll_result" href='javascript:;' onClick="dsp_poll_result(this)">
                                    <?php echo __('see result') ?>
                                </a>
                            </form>
                        <?php endif; ?>

                    </div>
                </div>
                <!--End box poll-->
            </div>
        </div>
        <div id="widget_poll_modal" name="widget_poll_modal" style="display: none;"></div>
    <script>
            function dsp_poll_result($a_obj)
            {
                $v_id = $($a_obj).parents('form:first').find('#hdn_poll_id').val();
                $url = "<?php echo $this->get_controller_url() ?>" + 'dsp_poll_result/' + $v_id;
                $('#widget_poll_modal').attr('title','<?php echo __('poll result') ?>').html('<iframe src="'+ $url +'" style="width:100%;height:100%;border:none;"></iframe>').dialog({
                    width: 500,
                    height: 300,
                    modal: true
                });
            }
            function btn_vote_onclick($btn_obj)
            {
                aid = $($btn_obj).parents('form:first').find('[name=hdn_answer_id]').val();   
                pid = $($btn_obj).parents('form:first').find('[name=hdn_poll_id]').val();  
                if(!aid || !pid){
                    return;
                }
                url= "<?php echo $this->get_controller_url() ?>" + 'handle_widget_poll/';
                url += '&code=poll';
                url += '&pid=' + pid;
                url += '&aid=' + aid;
                $('#widget_poll_modal').attr('title','<?php echo __('please enter captcha') ?>').html('<iframe src="'+ url +'" style="width:100%;height:100%;border:none;"></iframe>').dialog({
                    width: 500,
                    height: 300,
                    modal: true
                });
            }
            function close_widget_poll_model(){
                $('#widget_poll_modal').dialog('close');
            }
    </script>
    </div>
    <!--end #sider-bar-->
    