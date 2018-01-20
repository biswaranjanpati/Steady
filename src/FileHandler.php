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
        $folders = $this->siteConfig["assets"];
        
        foreach ($folders as $folder) {
            $this->logger->message("Copying assets in: " . $folder);
            $srcPath =  FileHandler::join_paths($this->siteConfig["projectPath"], $folder);
            $outputPath = FileHandler::join_paths($this->siteConfig["projectPath"], "_site", $folder);
            
            self::recursiveRemove($outputPath);
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
                } else {
                    $this->logger->info("All assets must be defined in config.ini.");
                    $this->logger->info("Ignoring directory: " . $filePath);
                }
            }
        }
    }

    /*
        Delete all files/folders in a directory
        https://stackoverflow.com/a/13440766/219118
    */
    private static function recursiveRemove($dir) {
        if(!is_dir($dir)) return 0;
        $structure = glob(rtrim($dir, "/").'/*');
        
        if (is_array($structure)) {
            foreach($structure as $file) {
                if (is_dir($file)) self::recursiveRemove($file);
                elseif (is_file($file)) unlink($file);
            }
        }
        rmdir($dir);
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