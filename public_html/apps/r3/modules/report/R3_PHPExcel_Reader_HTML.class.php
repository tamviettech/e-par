<?php
//include thu vien excel
require(SERVER_ROOT.'libs/excel/PHPExcel/IOFactory.php');
require(SERVER_ROOT.'libs/excel/PHPExcel/Writer/Excel5.php');

//rewrite for PHPExcel/Reader/HTML.php');
/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
	require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}

/**
 * PHPExcel_Reader_HTML
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class R3_PHPExcel_Reader_HTML extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
	/**
	 * Input encoding
	 *
	 * @var string
	 */
	private $_inputEncoding	= 'ANSI';

	/**
	 * Sheet index to read
	 *
	 * @var int
	 */
	private $_sheetIndex 	= 0;

	/**
	 * Formats
	 *
	 * @var array
	 */
	private $_formats = array( 'h1' => array( 'font' => array( 'bold' => true,
															   'size' => 24,
															 ),
											),	//	Bold, 24pt
							   'h2' => array( 'font' => array( 'bold' => true,
															   'size' => 18,
															 ),
											),	//	Bold, 18pt
							   'h3' => array( 'font' => array( 'bold' => true,
															   'size' => 13.5,
															 ),
											),	//	Bold, 13.5pt
							   'h4' => array( 'font' => array( 'bold' => true,
															   'size' => 12,
															 ),
											),	//	Bold, 12pt
							   'h5' => array( 'font' => array( 'bold' => true,
															   'size' => 10,
															 ),
											),	//	Bold, 10pt
							   'h6' => array( 'font' => array( 'bold' => true,
															   'size' => 7.5,
															 ),
											),	//	Bold, 7.5pt
							   'a'  => array( 'font' => array( 'underline' => true,
															   'color' => array( 'argb' => PHPExcel_Style_Color::COLOR_BLUE,
															                   ),
															 ),
											),	//	Blue underlined
							   'hr' => array( 'borders' => array( 'bottom' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN,
																					 'color' => array( PHPExcel_Style_Color::COLOR_BLACK,
																					                 ),
																				   ),
																),
											),	//	Bottom border
							 );


    
	/**
	 * Create a new PHPExcel_Reader_HTML
	 */
	public function __construct() {
		$this->_readFilter 	= new PHPExcel_Reader_DefaultReadFilter();
	}

	/**
	 * Validate that the current file is an HTML file
	 *
	 * @return boolean
	 */
	protected function _isValidFormat()
	{
		//	Reading 2048 bytes should be enough to validate that the format is HTML
		$data = fread($this->_fileHandle, 2048);
		if ((strpos($data, '<') !== FALSE) &&
			(strlen($data) !== strlen(strip_tags($data)))) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Loads PHPExcel from file
	 *
	 * @param 	string 		$pFilename
	 * @return 	PHPExcel
	 * @throws 	PHPExcel_Reader_Exception
	 */
	public function load($pFilename)
	{
		// Create new PHPExcel
		$objPHPExcel = new PHPExcel();

		// Load into this instance
		return $this->loadIntoExisting($pFilename, $objPHPExcel);
	}

	/**
	 * Set input encoding
	 *
	 * @param string $pValue Input encoding
	 */
	public function setInputEncoding($pValue = 'ANSI')
	{
		$this->_inputEncoding = $pValue;
		return $this;
	}

	/**
	 * Get input encoding
	 *
	 * @return string
	 */
	public function getInputEncoding()
	{
		return $this->_inputEncoding;
	}

	//	Data Array used for testing only, should write to PHPExcel object on completion of tests
	private $_dataArray = array();

	private $_tableLevel = 0;
	private $_nestedColumn = array('A');

	private function _setTableStartColumn($column) {
		if ($this->_tableLevel == 0)
			$column = 'A';
		++$this->_tableLevel;
		$this->_nestedColumn[$this->_tableLevel] = $column;

		return $this->_nestedColumn[$this->_tableLevel];
	}

	private function _getTableStartColumn() {
		return $this->_nestedColumn[$this->_tableLevel];
	}

	private function _releaseTableStartColumn() {
		--$this->_tableLevel;
		return array_pop($this->_nestedColumn);
	}

	private function _flushCell($sheet,$column,$row,&$cellContent) {
		if (is_string($cellContent)) {
			//	Simple String content
			if (trim($cellContent) > '') {
				//	Only actually write it if there's content in the string
//				echo 'FLUSH CELL: ' , $column , $row , ' => ' , $cellContent , '<br />';
				//	Write to worksheet to be done here...
				//	... we return the cell so we can mess about with styles more easily
				$cell = $sheet->setCellValue($column.$row,$cellContent,true);
				$this->_dataArray[$row][$column] = $cellContent;
			}
		} else {
			//	We have a Rich Text run
			//	TODO
			$this->_dataArray[$row][$column] = 'RICH TEXT: ' . $cellContent;
		}
		$cellContent = (string) '';
	}

	private function _processDomElement(DOMNode $element, $sheet, &$row, &$column, &$cellContent){
		foreach($element->childNodes as $child){
			if ($child instanceof DOMText) {
				$domText = preg_replace('/\s+/',' ',trim($child->nodeValue));
				if (is_string($cellContent)) {
					//	simply append the text if the cell content is a plain text string
					$cellContent .= $domText;
				} else {
					//	but if we have a rich text run instead, we need to append it correctly
					//	TODO
				}
			} elseif($child instanceof DOMElement) {
//				echo '<b>DOM ELEMENT: </b>' , strtoupper($child->nodeName) , '<br />';

				$attributeArray = array();
				foreach($child->attributes as $attribute) {
//					echo '<b>ATTRIBUTE: </b>' , $attribute->name , ' => ' , $attribute->value , '<br />';
					$attributeArray[$attribute->name] = $attribute->value;
				}

				switch($child->nodeName) {
					case 'meta' :
						foreach($attributeArray as $attributeName => $attributeValue) {
							switch($attributeName) {
								case 'content':
									//	TODO
									//	Extract character set, so we can convert to UTF-8 if required
									break;
							}
						}
						$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
						break;
					case 'title' :
						$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
						$sheet->setTitle($cellContent);
						$cellContent = '';
						break;
					case 'span'  :
					case 'div'   :
					case 'font'  :
					case 'i'     :
					case 'em'    :
					case 'strong':
					case 'b'     :
//						echo 'STYLING, SPAN OR DIV<br />';
						if ($cellContent > '')
							$cellContent .= ' ';
						$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
						if ($cellContent > '')
							$cellContent .= ' ';
//						echo 'END OF STYLING, SPAN OR DIV<br />';
                        $sheet->getStyle($column.$row)->getFont()->setBold(true);
                        
						break;
					case 'hr' :
						$this->_flushCell($sheet,$column,$row,$cellContent);
						++$row;
						if (isset($this->_formats[$child->nodeName])) {
							$sheet->getStyle($column.$row)->applyFromArray($this->_formats[$child->nodeName]);
						} else {
							$cellContent = '----------';
							$this->_flushCell($sheet,$column,$row,$cellContent);
						}
						++$row;
					case 'br' :
						if ($this->_tableLevel > 0) {
							//	If we're inside a table, replace with a \n
							$cellContent .= "\n";
						} else {
							//	Otherwise flush our existing content and move the row cursor on
							$this->_flushCell($sheet,$column,$row,$cellContent);
							++$row;
						}
//						echo 'HARD LINE BREAK: ' , '<br />';
						break;
					case 'a'  :
//						echo 'START OF HYPERLINK: ' , '<br />';
						foreach($attributeArray as $attributeName => $attributeValue) {
							switch($attributeName) {
								case 'href':
//									echo 'Link to ' , $attributeValue , '<br />';
									$sheet->getCell($column.$row)->getHyperlink()->setUrl($attributeValue);
									if (isset($this->_formats[$child->nodeName])) {
										$sheet->getStyle($column.$row)->applyFromArray($this->_formats[$child->nodeName]);
									}
									break;
							}
						}
						$cellContent .= ' ';
						$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
//						echo 'END OF HYPERLINK:' , '<br />';
						break;
					case 'h1' :
					case 'h2' :
					case 'h3' :
					case 'h4' :
					case 'h5' :
					case 'h6' :
					case 'ol' :
					case 'ul' :
					case 'p'  :
						if ($this->_tableLevel > 0) {
							//	If we're inside a table, replace with a \n
							$cellContent .= "\n";
//							echo 'LIST ENTRY: ' , '<br />';
							$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
//							echo 'END OF LIST ENTRY:' , '<br />';
						} else {
							if ($cellContent > '') {
								$this->_flushCell($sheet,$column,$row,$cellContent);
								$row += 2;
							}
//							echo 'START OF PARAGRAPH: ' , '<br />';
							$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
//							echo 'END OF PARAGRAPH:' , '<br />';
							$this->_flushCell($sheet,$column,$row,$cellContent);

							if (isset($this->_formats[$child->nodeName])) {
								$sheet->getStyle($column.$row)->applyFromArray($this->_formats[$child->nodeName]);
							}

							$row += 2;
							$column = 'A';
						}
						break;
					case 'li'  :
						if ($this->_tableLevel > 0) {
							//	If we're inside a table, replace with a \n
							$cellContent .= "\n";
//							echo 'LIST ENTRY: ' , '<br />';
							$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
//							echo 'END OF LIST ENTRY:' , '<br />';
						} else {
							if ($cellContent > '') {
								$this->_flushCell($sheet,$column,$row,$cellContent);
							}
							++$row;
//							echo 'LIST ENTRY: ' , '<br />';
							$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
//							echo 'END OF LIST ENTRY:' , '<br />';
							$this->_flushCell($sheet,$column,$row,$cellContent);
							$column = 'A';
						}
						break;
					case 'table' :
						$this->_flushCell($sheet,$column,$row,$cellContent);
						$column = $this->_setTableStartColumn($column);
//						echo 'START OF TABLE LEVEL ' , $this->_tableLevel , '<br />';
						if ($this->_tableLevel > 1)
							--$row;
						$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
//						echo 'END OF TABLE LEVEL ' , $this->_tableLevel , '<br />';
						$column = $this->_releaseTableStartColumn();
						if ($this->_tableLevel > 1) {
							++$column;
						} else {
							++$row;
						}
						break;
					case 'thead' :
					case 'tbody' :
						$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
						break;
					case 'tr' :
						++$row;
						$column = $this->_getTableStartColumn();
						$cellContent = '';
                        //neu co dieu kien cot bat dau (excel-column)
                        $new_col = isset($attributeArray['excel-column']) ? $attributeArray['excel-column'] : '';
                        if((int)$new_col > 0)
                        {
                            $column = $this->arr_excel_column[$new_col];
                        }
                        
//						echo 'START OF TABLE ' , $this->_tableLevel , ' ROW<br />';
						$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
//						echo 'END OF TABLE ' , $this->_tableLevel , ' ROW<br />';
						break;
					case 'th' :
                        $this->_processDomElement($child,$sheet,$row,$column,$cellContent);
                        $this->_flushCell($sheet,$column,$row,$cellContent);
                        //align
                        $align = isset($attributeArray['align']) ? $attributeArray['align'] : '';
                        //can giua dong
                        $sheet->getStyle($column.$row.':'.$column.$row)
                                        ->getAlignment()
                                        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        switch ($align) {
                            case 'center':
                                $sheet->getStyle($column.$row)
                                    ->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                break;
                            
                            case 'right':
                                $sheet->getStyle($column.$row)
                                    ->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                break;

                            default:
                                $sheet->getStyle($column.$row)
                                    ->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                break;
                        } //end align
                        
                        $sheet->getStyle($column.$row)->getAlignment()->setWrapText(true);
                        $sheet->getRowDimension($row)->setRowHeight(40);
                                
                        //rowspan
                        $rowspan = isset($attributeArray['rowspan']) ? $attributeArray['rowspan'] : '';
                        if((int)$rowspan > 0)//thuc hien hop dong
                        {
                            $sheet->mergeCells($column.$row.':'.$column.($row + $rowspan - 1));
                            //can giua dong
                            $sheet->getStyle($column.$row.':'.$column.($row + $rowspan - 1))
                                        ->getAlignment()
                                        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        }
                        
                        //colspan
                        $colspan = isset($attributeArray['colspan']) ? $attributeArray['colspan'] : '';
                        if((int)$colspan > 0)//thuc hien hop cot
                        {
                            $column_index = array_search($column, $this->arr_excel_column); //lay index cua arra excel column
                            $column_index = $column_index + $colspan - 1;//cong them index de lay ten column
                            $sheet->mergeCells($column.$row.':'.$this->arr_excel_column[$column_index].$row);
                            //can giua dong
                            $sheet->getStyle($column.$row.':'.$this->arr_excel_column[$column_index].$row)
                                        ->getAlignment()
                                        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $column = $this->arr_excel_column[$column_index];
                        }
                        
                        ++$column;
                        break;
					case 'td' :
//						echo 'START OF TABLE ' , $this->_tableLevel , ' CELL<br />';
						$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
//						echo 'END OF TABLE ' , $this->_tableLevel , ' CELL<br />';
						$this->_flushCell($sheet,$column,$row,$cellContent);
                        //can giua dong
						$sheet->getStyle($column.$row.':'.$column.$row)
                                        ->getAlignment()
                                        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        
                        //LienND set column width
//                        $w = isset($attributeArray['width']) ? $attributeArray['width'] : '';
//                        if(!empty($w) && (int)$w > 0)
//                        {
//                            $sheet->getColumnDimension($column)->setWidth($w/5 + 15);
//                        }
                        
                        
                        //align
                        $align = isset($attributeArray['align']) ? $attributeArray['align'] : '';
                        switch ($align) {
                            case 'center':
                                $sheet->getStyle($column.$row)
                                    ->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                break;
                            
                            case 'right':
                                $sheet->getStyle($column.$row)
                                    ->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                break;

                            default:
                                $sheet->getStyle($column.$row)
                                    ->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                break;
                        } //end align
                        
                        $sheet->getStyle($column.$row)->getAlignment()->setWrapText(true);
                        $sheet->getRowDimension($row)->setRowHeight(30);
                        
                        if (strtolower($child->nodeName) === 'th')
                        {   
                            $sheet->getStyle($column.$row)->getFont()->setBold(true);
                        }
                        
                        //class
                        $v_class = isset($attributeArray['class']) ? $attributeArray['class'] : '';
                        if ($v_class == 'group_name')
                        {
                            $sheet->getStyle($column.$row)->getFont()->setBold(true);
//                            $sheet->getStyle($column.$row)->getFont()->setSize(14);
                        }
                        //excel merge (ko thay doi thu tu)
                        $merge = isset($attributeArray['excel-merge']) ? $attributeArray['excel-merge'] : '';
                        if((int)$merge > 0) //thuc hien merge
                        {
                            //$mysheet->mergeCells('A1:G3');
                            $column_index = array_search($column, $this->arr_excel_column); //lay index cua arra excel column
                            $column_index = $column_index + $merge - 1;//cong them index de lay ten column
                            $sheet->mergeCells($column.$row.':'.$this->arr_excel_column[$column_index].$row);
                            $column = $this->arr_excel_column[$column_index];
                        }
                        else
                        {
                            //rowspan
                            $rowspan = isset($attributeArray['rowspan']) ? $attributeArray['rowspan'] : '';
                            if((int)$rowspan > 0)//thuc hien hop dong
                            {
                                $sheet->mergeCells($column.$row.':'.$column.($row + $rowspan - 1));
                                //can giua dong
                                $sheet->getStyle($column.$row.':'.$column.($row + $rowspan - 1))
                                            ->getAlignment()
                                            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            }

                            //colspan
                            $colspan = isset($attributeArray['colspan']) ? $attributeArray['colspan'] : '';
                            if((int)$colspan > 0)//thuc hien hop cot
                            {
                                $column_index = array_search($column, $this->arr_excel_column); //lay index cua arra excel column
                                $column_index = $column_index + $colspan - 1;//cong them index de lay ten column
                                $sheet->mergeCells($column.$row.':'.$this->arr_excel_column[$column_index].$row);
                                //can giua dong
                                $sheet->getStyle($column.$row.':'.$this->arr_excel_column[$column_index].$row)
                                            ->getAlignment()
                                            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                $column = $this->arr_excel_column[$column_index];
                            }
                        }
                        //set heigt cua column
                        $height = isset($attributeArray['excel-height']) ? $attributeArray['excel-height'] : '';
                        if((int)$height > 0) //thuc hien set height
                        {
                            $sheet->getRowDimension($row)->setRowHeight($height/5 + 15);
                        }
                        ++$column;
						break; //th //td
                        
					case 'body' :
//						$row = 9;
						$column = 'A';
						$content = '';
						$this->_tableLevel = 0;
						$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
						break;
					default:
						$this->_processDomElement($child,$sheet,$row,$column,$cellContent);
				}
			}
		}
	}

	/**
	 * Loads PHPExcel from file into PHPExcel instance
	 *
	 * @param 	string 		$pFilename
	 * @param	PHPExcel	$objPHPExcel
	 * @return 	PHPExcel
	 * @throws 	PHPExcel_Reader_Exception
	 */
	public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel,$row = 10)
	{
		// Open file to validate
		$this->_openFile($pFilename);
		if (!$this->_isValidFormat()) {
			fclose ($this->_fileHandle);
			throw new PHPExcel_Reader_Exception($pFilename . " is an Invalid HTML file.");
		}
		//	Close after validating
		fclose ($this->_fileHandle);

		// Create new PHPExcel
		while ($objPHPExcel->getSheetCount() <= $this->_sheetIndex) {
			$objPHPExcel->createSheet();
		}
		$objPHPExcel->setActiveSheetIndex( $this->_sheetIndex );

		//	Create a new DOM object
        //libxml_use_internal_errors(true);
		$dom = new domDocument;
        //	Reload the HTML file into the DOM object
		$loaded = $dom->loadHTMLFile($pFilename);//, PHPExcel_Settings::getLibXmlLoaderOptions());
		
		if ($loaded === FALSE) {
			throw new PHPExcel_Reader_Exception('Failed to load ',$pFilename,' as a DOM Document');
		}

		//	Discard white space
		$dom->preserveWhiteSpace = false;

        //tao bien de truyen dia chi
		$v_row = $row;
		$column = 'B';
		$content = '';
		$this->_processDomElement($dom,$objPHPExcel->getActiveSheet(),$v_row,$column,$content);

//		echo '<hr />';
//		var_dump($this->_dataArray);

		// Return
		return $objPHPExcel;
	}

	/**
	 * Get sheet index
	 *
	 * @return int
	 */
	public function getSheetIndex() {
		return $this->_sheetIndex;
	}

	/**
	 * Set sheet index
	 *
	 * @param	int		$pValue		Sheet index
	 * @return PHPExcel_Reader_HTML
	 */
	public function setSheetIndex($pValue = 0) {
		$this->_sheetIndex = $pValue;
		return $this;
	}

    
    //=========================================================================================================================
    //Dinh nghia style cho cac cell trong bao cao Excel
    public $styleArrayForData = array(
            'borders' => array(
                'left'        => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)
                ,'right'      => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)
                ,'bottom'     => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)
                ,'top'        => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                ,'vertical'   => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                ,'horizontal' => array('style' => PHPExcel_Style_Border::BORDER_HAIR)
            )
        );
                
    public $styleArrayForHeader = array(
            'font' => array(
                'bold' => true,
            )
            ,'alignment' => array(
                //'horizontal'    => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'      => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
            ,'borders' => array(
                'left'      => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)
                ,'right'    => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)
                ,'bottom'   => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                ,'top'      => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)
                ,'inside'   => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                ,'vertical'   => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            )
        );
    
    public $arr_excel_column = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','X','Y','Z');
}