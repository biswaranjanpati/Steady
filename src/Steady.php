<?php
namespace Steady;

class Steady {
    
	function __construct($configPath) {
		$this->logger = new Logger();
        
        $this->logger->message("Welcome to Steady");
        
        $CFG = new Configuration($configPath, $this->logger);
        $this->siteConfig = $CFG->siteConfig;
        $this->env = $CFG->env;
        
		$this->loadAllContent();
		
        $this->deepRefresh();

	}
    
    function loadAllContent() {
        // Parse all the posts and pages defined in config.ini content section
        foreach($this->siteConfig["content"] as $contentDir) {
            $this->loadContent($contentDir);
        }
        
        // Get only the posts to be able to pass them on to the templates
        $posts = [];
        foreach($this->pages as $Page) {
            if($Page->metadata["parentFolder"] == "posts") {
                $posts[] = $Page;
            }
        }
        
        // compile template for all pages
        foreach($this->pages as $Page) {
            $Page->compilePageTemplate($posts);
        }
    }
    
    /*
        Loads all content from the content array in configuration and sorts them by newest first.
    */
	function loadContent($folder) {
        $path = FileHandler::join_paths($this->siteConfig["projectPath"], $folder);
        
		$contentFiles = glob( $path . '/*.{md,html}', GLOB_BRACE); // Only get html and md files
        
		foreach ($contentFiles as $file) {
			$Page = new Page($this->siteConfig, $this->logger);
			$Page->loadPage($file, $folder);
			
			$this->pages[] = $Page;
		}
        
		// Sort by newest first
        usort($this->pages, function($a, $b) {
            return $b->metadata['timestamp'] - $a->metadata['timestamp']; 
        });
	}
	
    /*
        Writes all pages including index and archive to output directory.
    */
    function deepRefresh() {
       
        foreach ($this->pages as $Page) {
            $FH = new FileHandler($this->siteConfig, $this->logger);
            $FH->writeSinglePage($Page);
        }
        
        $FH->copyAssets();
        
        $this->logger->message("Done.");
        $this->logger->message("Output directory: _site");
    }
}
?>