<?php
namespace Steady;

class FileHandler {
    
	function __construct($siteConfig, $logger) {
        $this->siteConfig = $siteConfig;
        $this->logger = $logger;
    }
    
    public function loadFileFromPath($path) {
        $data = file_get_contents($path);
        return $data;
    }
    
    /*
    Compile a page and write the output to files.
    Creates a directory for the page and copies all resources included in source dir.
    */
    public function writeSinglePage($Page) {
        $metadata = $Page->metadata;
        
        // Create output directories for page
        $outputPath = $this->siteConfig["build_path"] . '/pages/';
        $pagePath = $outputPath . $metadata["slug"] . '/';
        
        if(!is_dir($outputPath)){
            mkdir($outputPath, 0755);
        }
        if(!is_dir($pagePath)){
            mkdir($pagePath, 0755);
        }
        
        // Write page to file
        $fileName = "index.html";
        file_put_contents($pagePath . $fileName, $Page->compiledTpl);

        // Copy all other resources from src to output
        $files = glob($this->siteConfig['page_path'] . '/' . $metadata["slug"] . '/*');
        foreach ($files as $filePath) {
            $baseName = basename($filePath);
            if ($baseName != "page.md") {
                copy($filePath, $pagePath . $baseName);
            }
        }
    }
	
	/*
		archive
		index
		rss
	*/
	public function writeSiteFiles($fileName, $html) {
		$outputPath = $this->siteConfig["build_path"] . '/';
		$filePath = $outputPath . $fileName . ".html";
		
        file_put_contents($filePath, $html);
	}
}
?>