<?php

/**
 * A basic Importer class for extracting articles from RSS data
 * 
 * @author James Griffin
 */
class Importer {
	const INT_STATE_IGNORE = -1;
	const INT_STATE_ARTICLE = 1;
	const INT_STATE_AUTHOR = 2;
	const INT_STATE_SOURCEURL = 3;
	const INT_STATE_CONTENT = 4;
	const INT_STATE_DATE = 5;
	const INT_STATE_TITLE = 6;
	
	/**
	 * Takes raw XML data and extracts any articles from it.
	 *
	 * @param String xmlData The raw XML data
	 * @return Array arrArticles
	 */
	public function importArticles($xmlData) {
		$arrArticles = array();
		$arrArticle = array();
		$arrTagNames = array(
			$this::INT_STATE_TITLE => array('name' => 'title', 'output_key' => 'title'),
			$this::INT_STATE_SOURCEURL => array('name' => 'link', 'output_key' => 'source_url'),
			$this::INT_STATE_AUTHOR => array('name' => 'dc:creator', 'output_key' => 'author'),
			$this::INT_STATE_CONTENT => array('name' => 'content', 'output_key' => 'content'),
			$this::INT_STATE_DATE => array('name' => 'pubDate', 'output_key' => 'datestamp')
		);
		$blnInCdata = false;
		$strCurrentStringData = '';
		$intState = -1;
		$i = 0;
		while($i < strlen($xmlData)) {
			switch($xmlData[$i]) {
				// Look for opening tags
				case '<':
					if($blnInCdata) {
						if($intState != $this::INT_STATE_IGNORE) {
							$strCurrentStringData .= $xmlData[$i];
						}
						$i++;
						break;
					}
				
					// Ignore <? and <![CDATA[ tags
					if($xmlData[$i+1] == '?') {
						$i += 2;
					} else if(substr($xmlData, $i, 9) == '<![CDATA[') {
						$i += 9;
						$blnInCdata = true;
					} else {
						// Look at actual tags
						if(substr($xmlData, $i + 1, 4) == 'item') {
							$intState = $this::INT_STATE_ARTICLE;
							$strCurrentStringData = '';
						} else if($xmlData[$i+1] == '/') {
							if(substr($xmlData, $i+2, 4) == 'item') {
								$arrArticles[] = array("Article" => $arrArticle);
							} else if($intState != $this::INT_STATE_IGNORE){
								$arrArticle[$arrTagNames[$intState]['output_key']] = $strCurrentStringData;
							}
							$intState = $this::INT_STATE_IGNORE;
						} else {
							foreach($arrTagNames as $intTagState => $arrTag) {
								//echo "Looking for tag name: " . $arrTag['name'] . " in string " . substr($xmlData, $i, 10) . "\n";
								if(substr($xmlData, $i+1, strlen($arrTag['name'])) == $arrTag['name']) {
									$intState = $intTagState;
									//echo "Found tag " . $arrTag['name'] . "\n";
									$strCurrentStringData = '';
									break;
								}
							}
						}
							
						// Skip ahead to the end of the tag
						$i += $this->_findCloseOfTag($xmlData, $i) + 1;
					}
					break;
				
				// Look for closing CDATA tags
				case ']':
					if($xmlData[$i+1] == ']' && $xmlData[$i+2] == '>') {
						$i += 3;
						$blnInCdata = false;
						break;
					}
				
				// Handle all other characters
				default:
					if($intState != $this::INT_STATE_IGNORE) {
						$strCurrentStringData .= $xmlData[$i];
					}
					$i++;
					break;
			}
		}

		foreach($arrArticles as $intKey => $arrArticle) {
			$arrArticles[$intKey]['Article']['content'] = strip_tags($arrArticle['Article']['content']);
			$arrArticles[$intKey]['Article']['datestamp'] = strtotime($arrArticle['Article']['datestamp']);
		}

		return $arrArticles;	
	}
	
	protected function _findCloseOfTag($xmlData, $intStart) {
		$k = 0; 
		while($xmlData[$intStart + $k] != '>') { $k++; }
		return $k;
	}
	
	/**
	 * Converts a string to UTF-8 encoding
	 *
	 * @param String strContent The content to convert
	 */
	protected function _convertToUTF8($strContent) {
	    $strContent = str_replace("and#", "&#", $strContent);
	    $strContent = html_entity_decode($strContent);
	    $mxdEncoding = mb_detect_encoding($strContent);
		
		if($mxdEncoding === false) {
	    	return mb_convert_encoding($strContent, "UTF-8");
		} else {
	    	return mb_convert_encoding($strContent, "UTF-8", $mxdEncoding);
		}
	}
}

?>