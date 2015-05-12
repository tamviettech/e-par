<?xml version="1.0"?>
<menus>
    <menu>
		<owner>
			<text><![CDATA[Người sử dụng]]></text>
			<url>cores/user</url>
			<icon>icon-user</icon>
			<active_app_name></active_app_name>
			<active_module_name>user</active_module_name>
			<req>QUAN_TRI_NGUOI_SU_DUNG</req>
		</owner>
    </menu>
    <menu>
		<owner>
			<text><![CDATA[Ngày làm việc/ngày nghỉ]]></text>
			<url>cores/calendar</url>
			<icon> icon-calendar</icon>
			<active_app_name></active_app_name>
			<active_module_name>calendar</active_module_name>
			<req>QUAN_TRI_NGAY_LAM_VIEC_NGAY_NGHI</req>
		</owner>
    </menu>
	<menu>
		<owner>
			<text><![CDATA[Danh mục]]></text>
			<url></url>
			<icon></icon>
			<active_app_name></active_app_name>
			<active_module_name></active_module_name>
			<req>========DEPEND_ON_ITEM_REQ_BELOW======</req>
		</owner>
		<sub_menu>
			<item>
				<text><![CDATA[Quản trị loại danh mục]]></text>
				<url><![CDATA[cores/xlist/]]></url>
				<icon><![CDATA[icon-file-alt]]></icon>
				<active_app_name></active_app_name>
				<active_module_name>xlist</active_module_name>
				<req>QUAN_TRI_LOAI_DANH_MUC</req>
			</item>
			<item>
				<text><![CDATA[Quản trị đối tượng danh mục]]></text>
				<url><![CDATA[cores/xlist/dsp_all_list]]></url>
				<icon><![CDATA[icon-file-alt]]></icon>
				<active_app_name></active_app_name>
				<active_module_name>dsp_all_list</active_module_name>
				<req>QUAN_TRI_DOI_TUONG_DANH_MUC</req>
			</item>
        </sub_menu>
    </menu>
    <menu>
		<owner>
			<text><![CDATA[Ứng dụng]]></text>
			<url>cores/application</url>
			<icon>icon-desktop</icon>
			<active_app_name></active_app_name>
			<active_module_name>application</active_module_name>
			<req>QUAN_TRI_UNG_DUNG</req>
		</owner>
    </menu>
    
    <menu>
		<owner>
			<text><![CDATA[Hệ thống]]></text>
			<url></url>
			<icon>icon-cogs</icon>
			<active_app_name></active_app_name>
			<active_module_name></active_module_name>
			<req>========DEPEND_ON_ITEM_REQ_BELOW======</req>
		</owner>
        <sub_menu>
            <item>
                <text><![CDATA[Sao lưu/khôi phục]]></text>
                <url>cores/backup_restore</url>
                <icon> icon-briefcase</icon>
                <active_app_name></active_app_name>
                <active_module_name>backup_restore</active_module_name>
                <req>QUAN_TRI_SAO_LUU_VA_KHOI_PHUC_DU_LIEU</req>
            </item>
            <item>
                <text><![CDATA[Tham số hệ thống]]></text>
                <url>cores/system_config</url>
                <icon> icon-cogs</icon>
                <active_app_name></active_app_name>
                <active_module_name>system_config</active_module_name>
                <req>QUAN_TRI_CAU_HINH_THAM_SO_HE_THONG</req>
            </item>
        </sub_menu>
    </menu>
</menus>
