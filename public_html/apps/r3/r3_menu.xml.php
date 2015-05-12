<?xml version = "1.0" ?>
<menus>
    <menu>
        <owner>
            <text><![CDATA[Xử lý hồ sơ]]></text>
            <url><![CDATA[r3/record]]></url>
            <icon><![CDATA[icon-file-alt]]></icon>
            <active_app_name></active_app_name>
            <active_module_name>record</active_module_name>
            <req></req>
        </owner>
    </menu>
    <menu>
        <owner>
            <text><![CDATA[Báo cáo]]></text>
            <url><![CDATA[r3/report/]]></url>
            <icon><![CDATA[icon-BAO_CAO]]></icon>
            <active_app_name></active_app_name>
            <active_module_name>report</active_module_name>
            <req>BAO_CAO</req>
        </owner>
    </menu>
    <menu>
        <owner>
            <text><![CDATA[Tài liệu lưu trữ]]></text>
            <url><![CDATA[r3/media]]></url>
            <icon><![CDATA[icon-hdd]]></icon>
            <active_app_name></active_app_name>
            <active_module_name>media</active_module_name>
            <req></req>
        </owner>
    </menu>
    <menu>
        <owner>
            <text><![CDATA[Chat - Hỗ trợ]]></text>
            <url><![CDATA[r3/advchat]]></url>
            <icon><![CDATA[icon-comment-alt]]></icon>
            <active_app_name></active_app_name>
            <active_module_name>advchat</active_module_name>
            <req></req>
        </owner>
    </menu>
    <menu>
        <owner>
            <text><![CDATA[Quản trị HS]]></text>
            <url></url>
            <icon><![CDATA[icon-cogs]]></icon>
            <active_app_name></active_app_name>
            <active_module_name></active_module_name>
            <req>========DEPEND_ON_ITEM_REQ_BELOW======</req>
        </owner>
        <sub_menu>
            <item>
                <text><![CDATA[Quản trị danh mục loại hồ sơ]]></text>
                <url><![CDATA[r3/record_type]]></url>
                <icon><![CDATA[icon-file-alt]]></icon>
                <active_app_name></active_app_name>
                <active_module_name>record_type</active_module_name>
                <req>QUAN_TRI_DANH_MUC_LOAI_HO_SO</req>
            </item>
            <item>
                <text>Quản trị quy trình xử lý hồ sơ</text>
                <url><![CDATA[r3/workflow]]></url>
                <icon><![CDATA[icon-sitemap]]></icon>
                <active_app_name></active_app_name>
                <active_module_name>workflow</active_module_name>
                <req>QUAN_TRI_QUY_TRINH_XU_LY_HO_SO</req>
            </item>
            <item>
                <text>Quản trị quy luật cản lọc hồ sơ</text>
                <url><![CDATA[r3/blacklist]]></url>
                <icon><![CDATA[icon-lock]]></icon>
                <active_app_name></active_app_name>
                <active_module_name>blacklist</active_module_name>
                <req>QUAN_TRI_LUAT_CAN_LOC_HO_SO</req>
            </item>
            <item>
                <text>Theo dõi hoạt động người dùng</text>
                <url><![CDATA[r3/logistic]]></url>
                <icon><![CDATA[icon-eye-open]]></icon>
                <active_app_name></active_app_name>
                <active_module_name>logistic</active_module_name>
                <req>THEO_DOI_NGUOI_DUNG</req>
            </item>
        </sub_menu>
    </menu>
<!--    <menu>
        <owner>
            <text>Tra cứu đánh giá cán bộ</text>
            <url><![CDATA[r3/cadre_evaluation/dsp_all_report]]></url>
            <icon>icon-thumbs-up</icon>
            <active_app_name></active_app_name>
            <active_module_name>cadre_evaluation</active_module_name>
            <req>TRA_CUU_DANH_GIA_CAN_BO</req>
        </owner>
    </menu>-->
    <profile>
        <item>
            <text>Bảng ánh xạ thủ tục</text>
            <url><![CDATA[r3/mapping]]></url>
            <icon><![CDATA[icon-table]]></icon>
            <active_app_name></active_app_name>
            <active_module_name>mapping</active_module_name>
            <req></req>
        </item>
        <item>
            <text>Hướng dẫn sử dụng</text>
            <url><![CDATA[http://e-par.vn]]></url>
            <icon><![CDATA[icon-table]]></icon>
            <active_app_name></active_app_name>
            <active_module_name></active_module_name>
            <req></req>
        </item>
    </profile>
</menus>
