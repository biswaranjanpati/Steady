<?php
namespace Steady;

class Post {
    
	function __construct($siteConfig, $logger) {
        $this->siteConfig = $siteConfig;
        $this->logger = $logger;
	}
    
    function loadPost($postPath) {
        $FH = new FileHandler($this->logger);
        $data = $FH->loadFileFromPath($this->siteConfig['post_path'] . '/' . $postPath . '/post.md');
        
        list($rawMetadata, $rawContent) = $this->splitDocument($data);
        
        $this->metadata = $this->parsePostMetaData($rawMetadata);
        $this->content = $rawContent;
    }
    
    /*
        Splits a post into metadata and content parts
    */
    function splitDocument($data) {
        $pattern = '/\s+={3,}\s+/';
        return preg_split($pattern, $data, 2);
    }
    
    /*
        Returns an associative array of post metadata
    */
    function parsePostMetaData($rawMetadata) {

        $array = array();
        $lines = explode("\n", $rawMetadata);

        foreach ($lines as $line) {
            list($key, $value) = explode(":", $line);
            $array[trim($key)] = trim($value);
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