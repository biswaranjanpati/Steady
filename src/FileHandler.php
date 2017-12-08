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
    
    function writePost($postData) {
        // Create output directories for post
        $outputPath = $this->siteConfig["build_path"] . '/posts/';
        $postPath = $outputPath . $postData["slug"] . '/';
        
        if(!is_dir($outputPath)){
            mkdir($outputPath, 0755);
        }
        if(!is_dir($postPath)){
            mkdir($postPath, 0755);
        }
        
        // Write post to file
        $fileName = "index.html";
        $fh = fopen($postPath . $fileName, 'w') or die("can't open file");
        fwrite($fh, $postData["html"]);
        fclose($fh);
        
        // Copy all other resources from src to output
        $files = glob($this->siteConfig['post_path'] . '/' . $postData["slug"] . '/*');
        foreach ($files as $filePath) {
            $baseName = basename($filePath);
            if ($baseName != "post.md") {
                copy($filePath, $postPath . $baseName);
            }
        }
    }
}
?>