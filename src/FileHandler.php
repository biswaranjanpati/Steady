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
        
        $outputPath = FileHandler::join_paths($this->siteConfig["projectPath"], "_site", $Page->metadata["parentFolder"]);
        $pagePath = FileHandler::join_paths($outputPath, $metadata["slug"] . ".html");
        
        if(!is_dir($outputPath)){
            mkdir($outputPath, 0755, true);
        }
        
        // Write page to file
        file_put_contents($pagePath, $Page->compiledTpl);
    }

    /*
        Copy all assets folders defined in config.ini
    */
    public function copyAssets() {
        $assets = $this->siteConfig["assets"];
        
        foreach ($assets as $asset) {
            $this->logger->message("Copying assets in: " . $asset);
            $srcPath =  FileHandler::join_paths($this->siteConfig["projectPath"], $asset);
            $outputPath = FileHandler::join_paths($this->siteConfig["projectPath"], "_site", $asset);
            
            self::recursiveRemove($outputPath);
            
            if (is_dir($srcPath)) {
                if(!is_dir($outputPath)){
                    mkdir($outputPath, 0755, true);
                }
                
                // Copy all resources from src to output
                $files = glob($srcPath . '/*');
                
                // TODO: implement recursive copy
                foreach ($files as $filePath) {
                    if(is_file($filePath)) {
                        $baseName = basename($filePath);
                        copy($filePath, FileHandler::join_paths($outputPath, $baseName));
                    }
                }
            } else if(is_file($srcPath)) {
                copy($srcPath, $outputPath);
            } else {
                $this->logger->info("Asset not found: " . $asset);
            }
        }
    }

    /*
        Recursive delete function that supports files and directories
        https://stackoverflow.com/a/13440766/219118
    */
    private static function recursiveRemove($path) {
        if(!is_dir($path)) {
            if (is_file($path)) {
                unlink($path);
            }
            return 0;
        }
        $structure = glob(rtrim($path, "/").'/*');
        
        if (is_array($structure)) {
            foreach($structure as $file) {
                if (is_dir($file)) self::recursiveRemove($file);
                elseif (is_file($file)) unlink($file);
            }
        }
        rmdir($path);
    }
    
    
    /*
        Combines paths
        https://stackoverflow.com/a/15575293
    */
    public static function join_paths() {
        $paths = array();

        foreach (func_get_args() as $arg) {
            if ($arg !== '') { $paths[] = $arg; }
        }

        return preg_replace('#/+#','/',join('/', $paths));
    }
}
?>