<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8"/>
	<xsl:param name="p_site_root" select="0"/>
	<xsl:param name="p_current_date" select="current-date()"/>

	<xsl:template match="/">
		<table border="0" width="100%" class="main_table" cellpadding="1" cellspacing="0">
			<xsl:for-each select="form/line">
					<tr>
					<xsl:attribute name="class">
						<xsl:if test="position() mod 2 = 0">
							xslgridrow
						</xsl:if>
					</xsl:attribute>
						<td style="width:100%">
							<xsl:if test="@label!=''">
                                                            <div class="widget-head blue">
                                                                <h3>
                                                                    <xsl:value-of select="@label"/>
                                                                </h3>
                                                            </div>
<!--								<table class="panel_table" border="0" style="width:100%" cellpadding="0" cellspacing="0">
									<tr class="widget-head blue">
										<td colspan="4">
											<h3 class="@css" >
												<xsl:value-of select="@label"/>
											</h3>
										</td>
									</tr>
								</table>-->
							</xsl:if>
							<table border="0" style="width:100%" cellpadding="0" cellspacing="0">
								<tr>
									<xsl:if test="@cols='1'">
										<xsl:for-each select="item">
											<td width="20%" class="text_color">
												<xsl:if test="@allownull='no'">
													<xsl:value-of select="@label"/>
													<span class="text_color_red">(*)</span>
												</xsl:if>
												<xsl:if test="@allownull='yes'">
													<xsl:value-of select="@label"/>
												</xsl:if>
											</td>
											<td width="80%">
												<xsl:call-template name="find_control">
													<xsl:with-param name="ControlType">
														<xsl:value-of select="@type"/>
													</xsl:with-param>
												</xsl:call-template>
											</td>
										</xsl:for-each>
									</xsl:if>
									<xsl:if test="@cols='2'">
										<xsl:for-each select="item">
											<td width="20%" class="text_color">
												<xsl:if test="@allownull='no'">
													<xsl:value-of select="@label"/>
													<span class="text_color_red">(*)</span>
												</xsl:if>
												<xsl:if test="@allownull='yes'">
													<xsl:value-of select="@label"/>
												</xsl:if>
											</td>
											<td width="30%">
												<xsl:call-template name="find_control">
													<xsl:with-param name="ControlType">
														<xsl:value-of select="@type"/>
													</xsl:with-param>
												</xsl:call-template>
											</td>
										</xsl:for-each>
									</xsl:if>

									<xsl:if test="@cols='0'">
										<td width="20%" class="panel_color"></td>
										<td width="80%" class="panel_color" colspan="3" align="left">
											<xsl:for-each select="item">
												<xsl:call-template name="find_control">
													<xsl:with-param name="ControlType">
														<xsl:value-of select="@type"/>
													</xsl:with-param>
												</xsl:call-template>
											</xsl:for-each>
										</td>
									</xsl:if>
									<xsl:if test="@cols='3'">
                                                                            <xsl:choose>
                                                                                <!--Chia 3 cot 1 hang-->
                                                                                <xsl:when test="@row='3'">
                                                                                   <td width="10%" class="text_color" colspan="1" align="left">
                                                                                   </td>
                                                                                        <xsl:for-each select="item">
                                                                                            <td width="30%" class="text_color" colspan="1" align="left">
                                                                                                <xsl:call-template name="find_control">

                                                                                                        <xsl:with-param name="ControlType">
                                                                                                                <xsl:value-of select="@type"/>

                                                                                                        </xsl:with-param>

                                                                                                </xsl:call-template>
                                                                                                </td>
                                                                                        </xsl:for-each>
                                                                                </xsl:when>
                                                                                
                                                                                <!--Chia 2 cot 1 hang-->
                                                                                <xsl:when test="@row='2'">
                                                                                   <td width="10%" class="text_color" colspan="1" align="left"></td>
                                                                                        <xsl:for-each select="item">
                                                                                            <td width="30%" class="text_color" colspan="1" align="left">
                                                                                                <xsl:call-template name="find_control">

                                                                                                        <xsl:with-param name="ControlType">
                                                                                                                <xsl:value-of select="@type"/>

                                                                                                        </xsl:with-param>

                                                                                                </xsl:call-template>
                                                                                                </td>
                                                                                        </xsl:for-each>
                                                                                        <td width="30%" class="text_color" colspan="1" align="left"></td>
                                                                                </xsl:when>
                                                                                <!--Chia 1 cot 1 hang-->
                                                                                <xsl:when test="@row='2'">
                                                                                   <td width="10%" class="text_color" colspan="1" align="left"></td>
                                                                                        <xsl:for-each select="item">
                                                                                            <td width="30%" class="text_color" colspan="1" align="left">
                                                                                                <xsl:call-template name="find_control">

                                                                                                        <xsl:with-param name="ControlType">
                                                                                                                <xsl:value-of select="@type"/>

                                                                                                        </xsl:with-param>

                                                                                                </xsl:call-template>
                                                                                                </td>
                                                                                        </xsl:for-each>
                                                                                        <td width="30%" class="text_color" colspan="1" align="left"></td>
                                                                                        <td width="30%" class="text_color" colspan="1" align="left"></td>
                                                                                </xsl:when>
                                                                                
                                                                                <xsl:otherwise>
                                                                                    <!--chia 3 hang 1 cot-->
                                                                                    <td width="20%" class="text_color">
                                                                                                <span class="text_color">
                                                                                                        <xsl:value-of select="@label"/>
                                                                                                </span>
                                                                                        </td>
                                                                                        <td width="80%" class="text_color" colspan="3" align="left">
                                                                                                <xsl:for-each select="item">
                                                                                                        <xsl:call-template name="find_control">
                                                                                                                <xsl:with-param name="ControlType">
                                                                                                                        <xsl:value-of select="@type"/>
                                                                                                                </xsl:with-param>
                                                                                                        </xsl:call-template>
                                                                                                </xsl:for-each>
                                                                                        </td>
                                                                                </xsl:otherwise>
                                                                            </xsl:choose>
                                                                        </xsl:if>
								</tr>
							</table>
						</td>
					</tr>
			</xsl:for-each>
		</table>
	</xsl:template>
	
	<!--*********************************************************************************************************-->
	
	<xsl:template name="find_control">
		<xsl:param name="ControlType"/>
		<xsl:choose>
			<xsl:when test="$ControlType = 'TextboxName'">
				<xsl:call-template name="CreateTextboxName"/>
			</xsl:when>
			<xsl:when test="$ControlType = 'TextboxMoney'">
				<xsl:call-template name="CreateTextboxMoney"/>
			</xsl:when>
			<xsl:when test="$ControlType = 'Textbox'">
				<xsl:call-template name="CreateTextbox"/>
			</xsl:when>
			<xsl:when test="$ControlType = 'TextboxDate'">
				<xsl:call-template name="CreateTextboxDate"/>
			</xsl:when>
			<xsl:when test="$ControlType = 'DropDownList'">
				<xsl:call-template name="CreateDropDownList"/>
			</xsl:when>
			<xsl:when test="$ControlType = 'RadioButton'">
				<xsl:call-template name="CreateRadioButton"/>
			</xsl:when>
			<xsl:when test="$ControlType = 'Textarea'">
				<xsl:call-template name="CreateTextArea"/>
			</xsl:when>
			<xsl:when test="$ControlType = 'Button'">
				<xsl:call-template name="CreateButton"/>
			</xsl:when>
			<xsl:when test="$ControlType = 'MultiCheckbox'">
				<xsl:call-template name="CreateMultiCheckbox"/>
			</xsl:when>
			<xsl:when test="$ControlType = 'Checkbox'">
				<xsl:call-template name="CreateCheckbox"/>
			</xsl:when>
			<xsl:when test="$ControlType = 'TextboxArea'">
				<xsl:call-template name="CreateTextboxArea"/>
			</xsl:when>
            <xsl:when test="$ControlType = 'TextboxDocSEQ'">
				<xsl:call-template name="CreateTextboxDocSEQ"/>
			</xsl:when>
                        
            <xsl:when test="$ControlType = 'Password'">
                <xsl:call-template name="CreatePassword"/>
            </xsl:when>
                        
			<xsl:otherwise>This object [<xsl:value-of select="$ControlType"/>] not found</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!--***********************************************************************************************
  Call this template when object is normal text box-->
	<xsl:template name="CreateTextboxArea">
		<xsl:if test="@id='txtTaiLieuKhac'">
			<textarea id="{@id}" cols="{@size}" class="text  valid" style="display:none;" rows="10" value="{@defaul_value}" data-allownull="{@allownull}" data-validate="{@validate}" data-name="{@name}" data-xml="yes" data-doc="{@doc}">&#x20;
			</textarea>
		</xsl:if>
		<xsl:if test="@id!='txtTaiLieuKhac'">
			<textarea id="{@id}" cols="{@size}" class="text  valid" rows="10" value="{@defaul_value}" data-allownull="{@allownull}" data-validate="{@validate}" data-name="{@name}" data-xml="yes" data-doc="{@doc}">&#x20;
			</textarea>
		</xsl:if>
	</xsl:template>
	<!--***********************************************************************************************
  Call this template when object is normal text box has even-->
	<xsl:template name="CreateTextboxMoney">
		<input type="textbox" id="{@id}" class="text  valid" size="{@size}" value="{@defaul_value}" onfocusout="ReadNumberToString('{@id}','lbl_mess_{@id}');" onkeyup="{@Even}" maxlength="15" onKeyDown="return handleEnter(this, event);"
		       data-allownull="{@allownull}" data-validate="{@validate}" data-name="{@name}" data-xml="yes" data-doc="{@doc}"
			   onfocus="this.value=removeCommas(this.value)" />
		<xsl:if test="@id='txtCost'">
			<label style="display:inline !important; padding-left:4px;">
				<input type="checkbox" name="chk_txtCost_full" id="chk_txtCost_full" checked="checked" data-xml="yes" value="1"/><strong>Đã thu đủ</strong>
			</label>
		</xsl:if>	   			   
		<br/>
		<span id="lbl_mess_{@id}" class="{@css}"></span>
	</xsl:template>
	<!--***********************************************************************************************
  Call this template when object is normal text box has even-->
	<xsl:template name="CreateTextboxName">

		<input type="textbox" id="{@id}" class=" text  valid" size="{@size}" value="{@defaul_value}" onkeyup="{@Even}" onKeyDown="return handleEnter(this, event);" data-allownull="{@allownull}" data-validate="{@validate}" data-name="{@name}" data-xml="yes"
		       data-doc="{@doc}"/>
		<br/>
		<span id="lbl_mess_{@id}" class="{@css}"></span>
	</xsl:template>
	<!--***********************************************************************************************
  Call this template when object is normal text box-->
	<xsl:template name="CreateTextbox">
		<input type="textbox" id="{@id}" class=" text  valid" size="{@size}" value="{@defaul_value}" onkeyup="{@Even}" onKeyDown="return handleEnter(this, event);" data-allownull="{@allownull}" data-validate="{@validate}" data-name="{@name}" data-xml="yes"
		       data-doc="{@doc}"/>
		<xsl:if test="@legend != ''">
			<label><xsl:value-of select="@legend" disable-output-escaping="yes" /></label>
		</xsl:if>
	</xsl:template>
        
        <!--***********************************************************************************************
  Call this template when object is Password text box-->
	<xsl:template name="CreatePassword">
		<input type="password" id="{@id}" class=" text  valid" size="{@size}" value="{@defaul_value}" onkeyup="{@Even}" onKeyDown="return handleEnter(this, event);" data-allownull="{@allownull}" data-validate="{@validate}" data-name="{@name}" data-xml="yes" data-doc="{@doc}"/>
	</xsl:template>
	<!--***********************************************************************************************
  Call this template when object is normal text box-->
	<xsl:template name="CreateDropDownList">
		<select id="{@id}" class="ddl" data-allownull="{@allownull}" data-name="{@name}" data-xml="yes" data-doc="{@doc}">
			<xsl:if test="@src_file != ''">
				<xsl:for-each select="document(@src_file)//item">
					<option value="{@value}">
						<xsl:value-of select="@name"/>
					</option>
				</xsl:for-each>
			</xsl:if>
			<xsl:if test="@src_xlist != ''">
				<option value="">-- Chọn <xsl:value-of select="@name"/> --</option>
				<xsl:variable name="v_option_list" select="document(concat($p_site_root,'cores/webservice/arp_data_for_xlist_ddli/',@src_xlist, '/?format=xml'))"/>
				<xsl:for-each select="$v_option_list//item">
					<option value="{@value}">
						<xsl:value-of select="@name"/>
					</option>
				</xsl:for-each>
			</xsl:if>
		</select>
	</xsl:template>
	<!--***********************************************************************************************
  Call this template when object is normal text box-->
	<xsl:template name="CreateRadioButton">
		<input type="radio" id="{@id}" name="{@gioitinh}" value="{@value}" data-allownull="{@allownull}" data-validate="{@validate}" data-name="{@text}" data-xml="yes" data-doc="{@doc}">
			<label for="{@id}">
				<xsl:value-of select="@text"/>
			</label>
		</input>
	</xsl:template>
	<!--***********************************************************************************************
  Call this template when object is normal text box-->
	<xsl:template name="CreateTextboxDate">
		<xsl:if test="@defaul_value!=''">
			<xsl:if test="@defaul_value!= 'current-date()'">
				<input type="textbox" id="{@id}" class=" text  valid" size="{@size}" value="{@defaul_value}" onKeyDown="return handleEnter(this, event);" data-allownull="{@allownull}" data-validate="{@validate}" data-name="{@name}" data-xml="yes"
				       data-doc="{@doc}"/>
			</xsl:if>
			<xsl:if test="@defaul_value = 'current-date()'">
				<input type="textbox" id="{@id}" class=" text  valid" size="{@size}" value="{$p_current_date}" onKeyDown="return handleEnter(this, event);" data-allownull="{@allownull}" data-validate="{@validate}" data-name="{@name}" data-xml="yes"
				       data-doc="{@doc}"/>
			</xsl:if>
		</xsl:if>
		<xsl:if test="@defaul_value=''">
			<input type="textbox" id="{@id}" class=" text  valid" value="Ngày/Tháng/Năm" size="{@size}" onKeyDown="return handleEnter(this, event);" data-allownull="{@allownull}" data-validate="{@validate}" data-name="{@name}" data-xml="yes"
			       data-doc="{@doc}"/>
		</xsl:if>
		<xsl:text disable-output-escaping="yes">&amp;nbsp</xsl:text>
		<img class="btndate" style="cursor:pointer" id="btnDate" src="{$p_site_root}public/images/calendar.gif" onclick="DoCal('{@id}')"/>
	</xsl:template>
	<!--***********************************************************************************************
  Call this template when object is normal text box-->
	<xsl:template name="CreateTextArea">
		<textarea id="{@id}" cols="{@col}" class=" text  valid" rows="{@row}" value="{@defaul_value}" onKeyDown="return handleEnter(this, event);" data-allownull="{@allownull}" data-validate="{@validate}" data-name="{@name}" data-xml="yes"
		          data-doc="{@doc}"/>
	</xsl:template>
	<!--***********************************************************************************************
  Call this template when object is normal text box-->
	<xsl:template name="CreateButton">
		<input type="button" class="ClassButton" value="{@value}" onClick="{@ObjList}"/>
	</xsl:template>

	<!--***********************************************************************************************
  Call this template when object is normal text box-->
	<xsl:template name="CreateMultiCheckbox">
		<div style="overflow: auto; width: 100%; height:120px; padding-left:0px;margin:0px">
			<table>
				<xsl:for-each select="document(@src_file)//item">
					<tr>
						<td>
                                                    <label for="{@item_id}">
                                                        <input type="checkbox" id="{@item_id}" name="Fields" value="{@value}" onKeyDown="return handleEnter(this, event);" data-name="{@text}" data-xml="yes" data-doc="{@doc}"/>
                                                        <xsl:value-of select="@text"/>
                                                    </label>
						</td>
					</tr>
				</xsl:for-each>
			</table>
		</div>
	</xsl:template>

	<!--***********************************************************************************************
  Call this template when object is check box-->
	<xsl:template name="CreateCheckbox">
		<table border="0">
			<tr>
                            <td>
                                <xsl:if test="@id='ckbTaiLieuKhac'">
                                    <label for="{@id}">
                                        <input type="checkbox" id="{@id}" onclick="Textarea('{@id}','{@id}_div');" onKeyDown="return handleEnter(this, event);" data-name="{@title}" data-xml="yes" data-doc="{@doc}"/>
                                        <span style="width: 5px;display:inline-block;"></span>
                                        <xsl:value-of select="@title"/>
                                    </label>
                                </xsl:if>
                                <xsl:if test="@id!='ckbTaiLieuKhac'">
                                    <label for="{@id}">
                                        <xsl:if test="@checked">
                                            <input type="checkbox" id="{@id}" data-name="{@title}" data-xml="yes" data-doc="{@doc}" checked="checked"/>
                                        </xsl:if>
                                        <xsl:if test="not(@checked)">
                                            <input type="checkbox" id="{@id}" data-name="{@title}" data-xml="yes" data-doc="{@doc}"/>
                                        </xsl:if>
                                        <span style="width: 5px;display:inline-block;"></span>
                                        <xsl:value-of select="@title"/>
                                    </label>
                                </xsl:if>
                            </td>
			</tr>
			<tr style="display:none">
                            <td>
                                <div id="{@id}_div" style="display:none;"></div>
                            </td>
			</tr>
		</table>
	</xsl:template>
    
    <xsl:template name="CreateTextboxDocSEQ">		
		<xsl:if test="@defaul_value=''">
			<input type="textbox" id="{@id}" class=" text  valid" value="" size="{@size}" onKeyDown="return handleEnter(this, event);" data-allownull="{@allownull}" data-validate="{@validate}" data-name="{@name}" data-xml="yes" data-doc="{@doc}"/>
		</xsl:if>
		<xsl:text disable-output-escaping="yes">&amp;nbsp</xsl:text>
		<img class="btnSeq" style="cursor:pointer" id="btnSeq" src="{$p_site_root}public/images/next_seq.png" onclick="get_next_doc_seq('{@id}')" width="20px" height="20px" />
	</xsl:template>
</xsl:stylesheet>
<!-- Stylus Studio meta-information - (c) 2004-2009. Progress Software Corporation. All rights reserved.

<metaInformation>
        <scenarios/>
        <MapperMetaTag>
                <MapperInfo srcSchemaPathIsRelative="yes" srcSchemaInterpretAsXML="no" destSchemaPath="" destSchemaRoot="" destSchemaPathIsRelative="yes" destSchemaInterpretAsXML="no"/>
                <MapperBlockPosition></MapperBlockPosition>
                <TemplateContext></TemplateContext>
                <MapperFilter side="source"></MapperFilter>
        </MapperMetaTag>
</metaInformation>
-->