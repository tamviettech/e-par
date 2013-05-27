<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="libs/xslt_libs.xsl"/>
	<xsl:output method="html" encoding="UTF-8"/>
	<xsl:output method="html"/>

	<xsl:variable name="c_panel_color" select="000000"/>

	<xsl:param name="p_site_root" select="0"/>
	<xsl:param name="p_current_date" select="current-date()"/>

	<xsl:variable name="v_record_type_code" select="//static_data/record_type_code" />
	<xsl:variable name="v_xml_form_struct_full_path" select="//static_data/xml_form_struct_full_path" />
	<xsl:variable name="v_xml_form_struct_dom" select="document($v_xml_form_struct_full_path)"/>
	<xsl:variable name="v_unit_info" select="document('public/xml/xml_unit_info.xml')"/>

	<xsl:variable name="v_record_no" select="//static_data/record_no"/>

	<xsl:template match="/">
		<html>
			<head>
				<title>In phiếu biên nhận <xsl:value-of select="//item[@id='txtName']/value" disable-output-escaping="yes"/></title>
				<link rel="stylesheet" href="{$p_site_root}public/css/reset.css" type="text/css" media="screen" />
        		<link rel="stylesheet" href="{$p_site_root}public/css/text.css" type="text/css" media="screen" />
				<link rel="stylesheet" href="{$p_site_root}public/css/printer.css" type="text/css" media="all" />
				<script src="{$p_site_root}public/js/jquery/jquery.min.js" type="text/javascript"></script>
			</head>
			<body>
				<div>
					<div class="print-button">
			            <input type="button" value="In trang" class="print" onclick="window.print(); return false;" />
			            <input type="button" value="Đóng cửa sổ" class="close" onclick="window.parent.hidePopWin();return false;" />
			        </div>
					<br/>

					<xsl:call-template name="create_handover_info">
						<xsl:with-param name="distribute">(Liên 1: Lưu)</xsl:with-param>
					</xsl:call-template>

					<h4 style="page-break-before: always; height: 0; line-height: 0"></h4>

					<xsl:call-template name="create_handover_info">
						<xsl:with-param name="distribute">(Liên 2: Giao cho công dân)</xsl:with-param>
					</xsl:call-template>
				</div>
			</body>
		</html>
	</xsl:template>

	<xsl:template name="create_handover_info">
		<xsl:param name="distribute"/>
		<!-- header-->
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="header">
			<tr>
				<td class="unit_full_name"><xsl:value-of select="$v_unit_info/unit/full_name" /></td>
				<td align="center">
					<span style="font-size: 12px">
						<strong>CỘNG HOÀ XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong>
					</span>
				</td>
			</tr>
			<tr>
				<td align="center">
					<strong>
						<u style="font-size: 13px">BỘ PHẬN MỘT CỬA - TIẾP NHẬN HỒ SƠ VÀ TRẢ KẾT QUẢ</u>
					</strong>
				</td>
				<td align="center">
					<strong>
						<u style="font-size: 10px">Độc lập - Tự do - Hạnh phúc</u>
					</strong>
				</td>
			</tr>
			<tr>
	            <td colspan="2" class="report-title">
	                <span class="title-1">GIẤY BIÊN NHẬN HỒ SƠ</span><br/>
					<span><strong>Mã hồ sơ: </strong><xsl:value-of select="$v_record_no" disable-output-escaping="yes"/></span><br/>
	                <span class="title-2"><xsl:value-of select="$distribute"/></span>
	            </td>
	        </tr>			
		</table>
		<!-- /header-->

		<!-- noi  dung-->
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<strong>Họ và tên người nộp hồ sơ:</strong>
				</td>
				<td>
					<xsl:value-of select="//item[@id='txtName']/value" disable-output-escaping="yes"/>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Địa chỉ thường trú:</strong>
				</td>
				<td>
					<xsl:value-of select="//item[@id='txtDiaChi']/value" disable-output-escaping="yes"/>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Số điện thoại:</strong>
				</td>
				<td>
					<xsl:value-of select="//static_data/return_phone_number" disable-output-escaping="yes"/>
				</td>
			</tr>
			<!--
			<tr>
				<td>
					<strong>Địa chỉ:</strong>
				</td>
				<td>
					<xsl:value-of select="//item[@id='txtPlaceAdd']/value" disable-output-escaping="yes"/>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Ngày hẹn trả TBNVTC:</strong>
				</td>
				<td>
					<xsl:value-of select="//item[@id='txtHenTraNVTC']/value" disable-output-escaping="yes"/>
				</td>
			</tr>
			-->
			<tr>
				<td>
					<strong>Lệ phí phải nộp (đồng):</strong>
				</td>
				<td>
					<xsl:value-of select="//item[@id='txtCost']/value" disable-output-escaping="yes"/>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Về việc:</strong>
				</td>
				<td>
					<xsl:value-of select="//static_data/record_type_name" disable-output-escaping="yes"/>
				</td>
			</tr>
			<tr>
				<td style=" width: 200px">
					<strong>Hồ sơ gồm có:</strong>
				</td>
				<td></td>
			</tr>
			<tr>
				<td colspan="2" style="padding-left: 30px;">
					<ul class="doc">
						<xsl:for-each select="//item[@doc='true' and value='true']">
							<xsl:variable name="v_item_id" select="@id"/>
							<li>
								<xsl:value-of select="$v_xml_form_struct_dom/form/line/item[@id=$v_item_id]/@title"/>
							</li>
						</xsl:for-each>
					</ul>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Tài liệu khác:</strong>
				</td>
				<td>
					<xsl:if test="//item[@id='ckbTaiLieuKhac']/value='true'">
						<xsl:call-template name="break">
							<xsl:with-param name="text" select="//item[@id='txtTaiLieuKhac']/value"/>
						</xsl:call-template>
					</xsl:if>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-left: 30px">
					<br/>
				</td>
			</tr>
			<tr>
				<td>
					<strong style="padding-right: 20px">Ngày giờ tiếp nhận:</strong>
				</td>
				<td>
					<xsl:value-of select="//static_data/receive_date" disable-output-escaping="yes" />
				</td>
			</tr>
			<tr>
				<td>
					<strong>Ngày hẹn trả kết quả::</strong>
				</td>
				<td>
					<xsl:value-of select="//static_data/return_date" disable-output-escaping="yes" />
				</td>
			</tr>
		</table>

		<!-- footer-->
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="padding: 20px; text-align: left">
					<strong>Số điện thoại của bộ phận “Một cửa”: <xsl:value-of select="$v_unit_info/unit/phone_number" /></strong>
					<br/>
					<strong>
						<u>Chú ý:</u> Công dân đến lấy kết quả mang theo phiếu hẹn, CMTND, lệ phí và giấy uỷ quyền (nếu có)</strong>
				</td>
			</tr>
			<tr>
				<td style="text-align: center;">
					<strong>
						<i>Để tra cứu thông tin hồ sơ, công dân đưa vào máy quét mã vạch</i>
					</strong>
					<br/>
					<div class="barcode">
						<img border="0" src="{$p_site_root}barcode.php?bc={$v_record_no}" alt="barcode" title="Barcode image"/>
					</div>
				</td>
			</tr>
		</table>
		<p></p>
		<br/>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="width: 30%">
					<strong>NGƯỜI NỘP HỒ SƠ</strong>
				</td>
				<td style="width: 30%">
					<strong>DUYỆT HỒ SƠ</strong>
				</td>
				<td style="width: 30%">
					<strong>CÁN BỘ TIẾP NHẬN</strong>
				</td>
			</tr>
		</table>
		<p></p>
	</xsl:template>
</xsl:stylesheet>