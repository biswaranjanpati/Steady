<?php
namespace Steady;

class Page {
    
	function __construct($siteConfig, $logger) {
        $this->siteConfig = $siteConfig;
        $this->logger = $logger;
	}
    
    function loadPage($pageDir) {
        $FH = new FileHandler($this->siteConfig, $this->logger);
        $data = $FH->loadFileFromPath($this->siteConfig['page_path'] . '/' . $pageDir . '/page.md');
        
        list($rawMetadata, $rawContent) = $this->splitDocument($data);
        
        $this->metadata = $this->parsePageMetaData($rawMetadata);
        $this->metadata["slug"] = $pageDir;
        
        $parser = new \Michelf\MarkdownExtra;
        $this->content = $parser->transform($rawContent);
    }
    
    /*
        Splits a page into metadata and content parts
    */
    function splitDocument($data) {
        $pattern = '/\s+={3,}\s+/';
        return preg_split($pattern, $data, 2);
    }
    
    /*
        Returns an associative array of page metadata
    */
    function parsePageMetaData($rawMetadata) {

        $array = array();
        $lines = explode("\n", $rawMetadata);

        /*
            TODO: verify template exists
            TODO: verify date is in right format
        */
        
        foreach ($lines as $line) {
            list($key, $value) = explode(":", $line, 2);
            $array[strtolower(trim($key))] = trim($value);
        }
        
        // Handle tags array
        if (isset($array['tags'])) {
            $tagArray = array();
            $tags = explode(",", $array['tags']);
            
            $array['tags'] = $tags;
        }
        
        return $array;
    }
}
?>