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
		$objFeed->handle_content_type();
				
		foreach($objFeed->get_items() as $intKey => $objFeedItem) {
			$arrArticle = array(
				"title"			=> $objFeedItem->get_title(),
				"source_url"    => $objFeedItem->get_permalink(),
				"content"		=> $objFeedItem->get_content(),
				"datestamp"		=> strtotime($objFeedItem->get_date("Y-m-d H:i:s")),
				"author"		=> $objFeedItem->get_author()
			);

			$arrArticles[] = array(
				"Article" => $arrArticle,
			);
		}

		return $arrArticles;
	}
}

?>