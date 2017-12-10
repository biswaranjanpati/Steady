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
    public function loadPage($pageDir) {
        $this->logger->message("Parsing page: " . $pageDir);
        
        $FH = new FileHandler($this->siteConfig, $this->logger);
        $data = $FH->loadFileFromPath($this->siteConfig['page_path'] . '/' . $pageDir . '/page.md');
        
        list($rawMetadata, $rawContent) = $this->splitDocument($data);
        
        $this->metadata = $this->parsePageMetaData($rawMetadata);
        $this->metadata["slug"] = $pageDir;
        
        $parser = new \Michelf\MarkdownExtra;
        // Function to convert relative URLs to absolute ones.
        $parser->url_filter_func = function ($url) {
            $absoluteUrl = strpos($url, "http://") === 0 || strpos($url, "https://") === 0;
            if (!$absoluteUrl) {
                $baseUrl = $this->siteConfig["base_url"];
                $url = $baseUrl . '/pages/' . $this->metadata["slug"] . '/' . $url; //[base_path]/pages/[page_folder]/[resource]
            }
            return $url;
        };
        //var_dump(get_defined_vars ());
        $this->content = $parser->transform($rawContent);
		
		$this->compilePageTemplate();
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
            $this->logger->info("Recieved format: " . $array["date"]);
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
	private function compilePageTemplate() {        
        $tpl = "post";
        if (isset($this->metadata['template'])) {
            $tpl = $this->metadata['template'];
        }

        $vars = array(
            "meta" => $this->metadata,
            "content" => $this->content
        );
		
		$Template = new Template($this->siteConfig, $this->logger);
		$html = $Template->compileTemplate($tpl, $vars);
        
		$this->compiledTpl = $html;
    }
}
?>