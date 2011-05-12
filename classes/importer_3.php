<?php

/**
 * A basic Importer class for extracting articles from RSS data
 * 
 * @author James Griffin
 */
class Importer {
	/**
	 * Takes raw XML data and extracts any articles from it.
	 *
	 * @param String xmlData The raw XML data
	 * @return Array arrArticles
	 */
	public function importArticles($xmlData) {
		$arrArticles = array();
		
		$objFeed = new SimplePie();
		$objFeed->set_raw_data($xmlData);
		$objFeed->init();
				
		foreach($objFeed->get_items() as $intKey => $objFeedItem) {
			$arrArticle = array(
				"title"			=> $this->_convertToUTF8($objFeedItem->get_title()),
				"source_url"    => $this->_convertToUTF8($objFeedItem->get_permalink()),
				"content"		=> $this->_convertToUTF8(strip_tags($objFeedItem->get_content())),
				"datestamp"		=> strtotime($objFeedItem->get_date("Y-m-d H:i:s")),
				"author"		=> $this->_convertToUTF8($objFeedItem->get_author()->get_name())
			);

			$arrArticles[] = array(
				"Article" => $arrArticle,
			);
		}

		return $arrArticles;
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