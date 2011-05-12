<?php

require_once("../../libs/simplepie.php");
require_once("../../classes/importer_3.php");

class ImporterTest extends PHPUnit_Framework_TestCase {
	protected $arrArticles;
	
	/**
	 * Initialises the fixtures and the import object to be tested
	 */
	public function setUp() {
		$xmlData = file_get_contents("../fixtures/rss.xml");

		$objImporter = new Importer();
		$this->arrArticles = $objImporter->importArticles($xmlData);
	}
	
	/**
	 * Tests the import function to ensure that all entities returned are UTF-8
	 */
	public function testElementEncoding() {
		foreach($this->arrArticles as $arrArticle) {
			foreach($arrArticle['Article'] as $strKey => $strValue) {
				$this->assertTrue(mb_check_encoding($strValue, "UTF-8"));
			}
		}
	}
	
	/**
	 * Ensures that each article has a valid author, content, source_url and date
	 */
	public function testValidElements() {
		foreach($this->arrArticles as $arrArticle) {
			$this->assertArrayHasKey("Article", $arrArticle);
			$this->assertTrue(is_array($arrArticle['Article']));
			
			$this->assertArrayHasKey("author", $arrArticle['Article']);
			$this->assertArrayHasKey("datestamp", $arrArticle['Article']);
			$this->assertArrayHasKey("source_url", $arrArticle['Article']);
			$this->assertArrayHasKey("title", $arrArticle['Article']);
			$this->assertArrayHasKey("content", $arrArticle['Article']);
			
			$this->assertNotEmpty($arrArticle['Article']['author']);
			$this->assertNotEmpty($arrArticle['Article']['title']);
			$this->assertNotEmpty($arrArticle['Article']['source_url']);
			$this->assertNotEmpty($arrArticle['Article']['content']);
			$this->assertNotEmpty($arrArticle['Article']['datestamp']);
						
			$this->assertTrue(is_string($arrArticle['Article']['author']));
			$this->assertTrue(is_string($arrArticle['Article']['title']));
			$this->assertTrue(is_string($arrArticle['Article']['source_url']));
			$this->assertTrue(is_string($arrArticle['Article']['content']));
			$this->assertTrue(is_numeric($arrArticle['Article']['datestamp']));
		}
	}
	
	/**
	 * Ensure that the content has had all HTML stripped out
	 */
	public function testHtmlStripped() {
		foreach($this->arrArticles as $arrArticle) {
			$this->assertEquals(strip_tags($arrArticle['Article']['content']), $arrArticle['Article']['content']);
		}
	}
}

?>