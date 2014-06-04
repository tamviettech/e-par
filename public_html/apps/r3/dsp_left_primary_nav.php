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
    $arr_other_role = array(strtolower(_CONST_TRA_CUU_ROLE),strtolower(_CONST_BAO_CAO_ROLE));
?>
<!--include js record-->
<script src="<?php echo SITE_ROOT . 'apps/r3/modules/record/record_views/js_record.js'; ?>"></script>

<div class="left-secondary-nav tab-content">
    <div class="tab-pane active bs-docs-sidebar" id="roles">
<!--        <h4 class="side-head">Hồ sơ</h4>-->
        <ul class="accordion-nav bs-docs-sidenav affix" style="width: 220px;">
            <?php /*
            <!--giai quyet thu tuc hanh chính-->
            <?php
            $class_active = (!in_array(strtolower($this->active_role), $arr_other_role))?'active':'';
            ?>
            <li class="<?php echo $class_active;?>">
                <a href="<?php echo SITE_ROOT . build_url('r3/record/') ; ?>">
                    <i class=" icon-list-alt"></i>
                    Giải quyết TTHC
                </a>
            </li>
            <!--tra cuu-->
            <?php 
            //kiem tra quyen
            if (check_permission(_CONST_TRA_CUU_ROLE, 'r3')):
                $class_active = (strtolower($this->active_role) == strtolower(_CONST_TRA_CUU_ROLE))?'active':'';
            ?>
            <li class="<?php echo $class_active;?>">
               
                <a href="<?php echo SITE_ROOT . build_url('r3/record/ho_so/tra_cuu'); ?>">
                    <i class=" icon-list-alt"></i>
                    Tra cứu
                </a>
            </li>
            <?php endif;?>
            <!--Bao cao-->
            <?php 
            //kiem tra quyen
             if (check_permission(_CONST_BAO_CAO_ROLE, 'r3')):
                $class_active = (strtolower($this->active_role) == strtolower(_CONST_BAO_CAO_ROLE))?'active':'';
            ?>
            <li class="<?php echo $class_active;?>">
                <a href="<?php echo SITE_ROOT . build_url('r3/record/ho_so/bao_cao'); ?>">
                    <i class=" icon-list-alt"></i>
                    Báo cáo
                </a>
            </li>
            <?php endif;?>
            <li>
                <a href="<?php echo SITE_ROOT . build_url('r3/record/ho_so/theo_doi'); ?>">
                    <i class=" icon-list-alt"></i>
                    Theo dõi
                </a>
            </li>*/?>
            
            <?php if (isset($this->arr_roles)): ?>
                <?php foreach ($this->arr_roles as $key => $val): ?>
                    <?php if (check_permission($key, 'r3')): ?>
                        <?php $v_class       = (strtolower($this->active_role) == strtolower($key) && ( !isset($_GET['url']) OR $_GET['url'] != "r3/record/liveboard") ) ? ' class="active_role active"' : ''; ?>
                        <li <?php echo $v_class; ?> data-role="<?php echo $key; ?>" data-menu="1" style="width: 100%;;">
                            <a href="<?php echo $this->controller_url . 'ho_so/' . strtolower($key); ?>">
                                <i class="icon-list-alt"></i>
                                <?php echo $val; ?>
                                <?php
                                $arr_not_count = array(
                                    _CONST_TRA_CUU_ROLE
                                    , _CONST_TRA_CUU_LIEN_THONG_ROLE
                                    , _CONST_BAO_CAO_ROLE
                                    , _CONST_Y_KIEN_LANH_DAO_ROLE
                                    , _CONST_TRA_CUU_LIEN_THONG_ROLE
                                    , _CONST_TRA_CUU_TAI_XA_ROLE
                                    
                                    );
                                ?>
                                
                                <?php if (!in_array(strtoupper($key), $arr_not_count)): ?>
                                    <i style="font-style: normal" class="count">(0)</i>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                        <?php $v_class    = (isset($_GET['url']) && $_GET['url'] == "r3/record/liveboard") ? ' class="active_role active"' : ''; ?>
                        <!--Theo dõi trực tuyến-->
                        <li <?php echo $v_class; ?> style="width: 100%;;" >
                             <a href="<?php echo $this->controller_url.'liveboard';?>"><i class="icon-list-alt"></i> Theo dõi trực tuyến</a>
                        </li>
                        <!--Tài liệu lưu trữ-->
                         <?php $v_class    = (isset($_GET['url']) && $_GET['url'] == "r3/media") ? ' class="active_role active"' : ''; ?>
                        <li <?php echo $v_class; ?> style="width: 100%;;" >
                             <a href="<?php echo SITE_ROOT . build_url('r3/media');?>">
                                 <i class="icon-list-alt"></i> 
                                 Tài liệu lưu trữ
                             </a>
                        </li>
                <script>
                    function get_role_notice() {
                        get_notice(SITE_ROOT+"<?php echo build_url('r3/notice/main') . '/' . $this->active_role; ?>");
                    }
                    
                    jQuery(document).ready(function() {
                        <?php if (!in_array(strtoupper($this->active_role), $arr_not_count)): ?>
                                get_role_notice();
                                setInterval(get_role_notice, <?php echo _CONST_GET_NEW_RECORD_NOTICE_INTERVAL; ?>);
                        <?php endif; ?>
                        count_processing_record_per_role();
                        setInterval(count_processing_record_per_role, <?php echo _CONST_GET_NEW_RECORD_NOTICE_INTERVAL; ?>);
                    });

                    function count_processing_record_per_role()
                    {
                        q = 'li[data-menu="1"]';
                        jQuery(q).each(function(index) {
                            var v_role = $(this).attr('data-role');
                            if (v_role.toUpperCase() != '<?php echo _CONST_TRA_CUU_ROLE; ?>' && v_role.toUpperCase() != '<?php echo _CONST_BAO_CAO_ROLE; ?>' && v_role.toUpperCase() != '<?php echo _CONST_Y_KIEN_LANH_DAO_ROLE; ?>')
                            {
                                var v_url = '<?php echo $this->controller_url; ?>' + 'count_processing_record_by_role/' + v_role + '/' + QS + 't=' + getTime();
                                jQuery.ajax({
                                    cache: false,
                                    url: v_url,
                                    dataType: 'json',
                                    success: function(data) {
                                        count = data.count;
                                        role = data.role;
                                        rq = 'li[data-role="' + role + '"] i[class="count"]';
                                        count = '('+ count +')';
                                        jQuery(rq).html(count);
                                    }
                                });
                            }
                        });
                    }
                </script>
            <?php endif; ?>
               
        </ul>
    </div> <!-- /#role -->
</div>