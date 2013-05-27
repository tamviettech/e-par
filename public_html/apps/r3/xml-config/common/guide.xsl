<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="libs/xslt_libs.xsl"/>
	<xsl:output method="html" encoding="UTF-8"/>

	<xsl:variable name="c_panel_color" select="000000"/>

	<xsl:param name="p_site_root" select="0"/>
	<xsl:param name="p_current_date" select="current-date()"/>

	<xsl:variable name="v_record_type_code" select="//static_data/record_type_code"/>
	<xsl:variable name="v_xml_form_struct_full_path" select="//static_data/xml_form_struct_full_path"/>
	<xsl:variable name="v_xml_form_struct_dom" select="document($v_xml_form_struct_full_path)"/>
	<xsl:variable name="v_unit_info" select="document('public/xml/xml_unit_info.xml')"/>

	<xsl:variable name="v_record_no" select="//static_data/record_no"/>

	<xsl:template match="/">
		<html>
			<head>
				<title>In phiếu hướng dẫn <xsl:value-of select="//item[@id='txtName']/value" disable-output-escaping="yes"/></title>
				<link rel="stylesheet" href="{$p_site_root}public/css/reset.css" type="text/css" media="screen"/>
				<link rel="stylesheet" href="{$p_site_root}public/css/text.css" type="text/css" media="screen"/>
				<link rel="stylesheet" href="{$p_site_root}public/css/printer.css" type="text/css" media="all"/>
				<script src="{$p_site_root}public/js/jquery/jquery.min.js" type="text/javascript"></script>
			</head>
			<body>
				<div>
					<div class="print-button">
						<input type="button" value="In trang" class="print" onclick="window.print(); return false;"/>
						<input type="button" value="Đóng cửa sổ" class="close" onclick="window.parent.hidePopWin();return false;"/>
					</div>
					<br/>

					<!-- header-->
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="header">
						<tr>
							<td class="unit_full_name">
								<xsl:value-of select="$v_unit_info/unit/full_name"/>
							</td>
							<td align="center">
								<span style="font-size: 12px">
									<strong>CỘNG HOÀ XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong>
								</span>
							</td>
						</tr>
						
						<tr>
							<td align="center">
								<strong>
									<u style="font-size: 13px">BỘ PHẬN TIẾP NHẬN VÀ TRẢ HỒ SƠ</u>
								</strong>
							</td>
							<td align="center">
								<strong>
									<u style="font-size: 10px">Độc lập - Tự do - Hạnh phúc</u>
								</strong>
							</td>
						</tr>
						<tr>
							<td colspan="100" style="text-align:right;padding-right:100px;font-style:italic;padding-top:10px;">
								<xsl:value-of select="$v_unit_info/unit/name"/>, ngày <xsl:value-of select="$p_current_date" />
							</td>
						</tr>
						<tr>
							<td colspan="2" class="report-title">
								<span class="title-1">GIẤY HƯỚNG DẪN THỰC HIỆN THỦ TỤC HÀNH CHÍNH</span>
								<br/>
								<span>
									<strong>Thủ tục: <xsl:value-of select="//static_data/record_type_name" disable-output-escaping="yes"/></strong>
								</span>
								<br/>								
							</td>
						</tr>
					</table>
					<!-- /header-->

					<!-- noi  dung-->
					<table border="1" cellpadding="4" cellspacing="0" width="100%" class="guide-table">
						<colgroup>
							<col width="5%"/>
							<col width="45%"/>
							<col width="50%"/>
						</colgroup>
						<tr>
							<th>STT</th>
							<th>Tài liệu, giấy tờ kèm theo đơn</th>
							<th>Hướng dẫn</th>
						</tr>
						<xsl:for-each select="$v_xml_form_struct_dom//item[@doc='yes' or @doc='1' or @doc='y']">
							<tr>
								<td><xsl:value-of select="position()"/></td>
								<td><xsl:value-of select="@title"/></td>
								<td>
									<textarea class="note-writer" rows="3"></textarea>
								</td>
							</tr>
						</xsl:for-each>
					</table>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>