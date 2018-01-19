<?php
namespace Steady;

class Page {
	function __construct($siteConfig, $logger) {
        $this->siteConfig = $siteConfig;
        $this->logger = $logger;
	}
    
    /*
        Loads a page from it's file.
    */
    public function loadPage($page, $parentFolder) {
        $this->logger->message("Parsing page: " . basename($page));
        
        $FH = new FileHandler($this->siteConfig, $this->logger);
        $data = $FH->loadFileFromPath($page);
        
        list($rawMetadata, $rawContent) = $this->splitDocument($data);
        
        $this->metadata = $this->parsePageMetaData($rawMetadata);
        if (!isset($this->metadata["slug"])) {
            $this->metadata["slug"] = pathinfo($page, PATHINFO_FILENAME);
        }
        $this->metadata["parentFolder"] = $parentFolder;
        
        $parser = new \Michelf\MarkdownExtra;
        
        // Function to convert relative URLs to absolute ones.
        $parser->url_filter_func = function ($url) {
            $absoluteUrl = preg_match('/.{3,5}:/', $url);
            if (!$absoluteUrl) {
                // TODO: join_paths replaces // with / so it messes up http://. fix it 
                #$url = FileHandler::join_paths($this->siteConfig["base_url"], $url);
                $url = $this->siteConfig["base_url"] . '/' . $url;
            }
            return $url;
        };
        
        $this->content = $parser->transform($rawContent);
    }
    
    /*
        Splits a page into metadata and content parts
    */
    private function splitDocument($data) {
        $pattern = '/\s+={3,}\s+/';
        return preg_split($pattern, $data, 2);
    }
    
    /*
        Returns an associative array of page metadata
    */
    private function parsePageMetaData($rawMetadata) {

        $array = array();
        $lines = explode("\n", $rawMetadata);

        foreach ($lines as $line) {
            list($key, $value) = explode(":", $line, 2);
            $array[strtolower(trim($key))] = trim($value);
        }
        
        $date = \DateTime::createFromFormat($this->siteConfig["date_format"], $array["date"]);
        
        if (strtolower($date->format($this->siteConfig["date_format"])) !== strtolower($array["date"])) {
            $this->logger->info("Expected format: " . date($this->siteConfig["date_format"]));
            $this->logger->info("Received format: " . $array["date"]);
            $this->logger->error("Date format does not match format specified in configuration");
        }
        
        $timestamp = $date->format('U');
		$array["timestamp"] = $timestamp;
		
        // Handle tags array
        if (isset($array['tags'])) {
            $tagArray = array();
            $tags = explode(",", $array['tags']);
            $tags = array_map('trim', $tags);
			
            $array['tags'] = $tags;
			
        }
        
        return $array;
    }
	
    /*
        Returns the HTML of the page after it's passed the template engine.
    */
	public function compilePageTemplate() {
        $extraVars = array();
        foreach (func_get_args() as $arg) {
            if ($arg !== '') { $extraVars[] = $arg; }
        }
        
        $tpl = "post.tpl";
        if (isset($this->metadata['template'])) {
            $tpl = $this->metadata['template'];
        }
        
        
        $vars = array(
            "meta" => $this->metadata,
            "content" => $this->content,
            "posts" => $extraVars[0]
        );
        
		
		$Template = new Template($this->siteConfig, $this->logger);
		$html = $Template->compileTemplate($tpl, $vars);
        
		$this->compiledTpl = $html;
    }
}
?>