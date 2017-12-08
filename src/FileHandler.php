<?php
namespace Steady;

class FileHandler {
    
	function __construct($siteConfig, $logger) {
        $this->siteConfig = $siteConfig;
        $this->logger = $logger;
    }
    
    function loadFileFromPath($path) {
        $data = file_get_contents($path);
        return $data;
    }
    
    function writePage($pageData) {
        $this->logger->info("Writing page: " . $pageData['slug']);
        
        // Create output directories for page
        $outputPath = $this->siteConfig["build_path"] . '/pages/';
        $pagePath = $outputPath . $pageData["slug"] . '/';
        
        if(!is_dir($outputPath)){
            mkdir($outputPath, 0755);
        }
        if(!is_dir($pagePath)){
            mkdir($pagePath, 0755);
        }
        
        // Write page to file
        $fileName = "index.html";
        $fh = fopen($pagePath . $fileName, 'w') or die("can't open file");
        fwrite($fh, $pageData["html"]);
        fclose($fh);
        
        // Copy all other resources from src to output
        $files = glob($this->siteConfig['page_path'] . '/' . $pageData["slug"] . '/*');
        foreach ($files as $filePath) {
            $baseName = basename($filePath);
            if ($baseName != "page.md") {
                copy($filePath, $pagePath . $baseName);
            }
        }
    }
}
?>