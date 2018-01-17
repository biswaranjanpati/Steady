<?php
namespace Steady;

class Steady {
    
	function __construct($configPath) {
		$this->logger = new Logger();
        
        $this->logger->message("Welcome to Steady");
        
        $CFG = new Configuration($configPath, $this->logger);
        $this->siteConfig = $CFG->siteConfig;
        $this->env = $CFG->env;
        
		$this->loadAllPages();
		
        $this->deepRefresh();

	}
    
    /*
        Loads all pages from the page_path and sorts them by newest first.
    */
	function loadAllPages() {
		$dirs = glob($this->siteConfig['page_path'] . '/*', GLOB_ONLYDIR);
		
		foreach ($dirs as $pageDir) {
			$Page = new Page($this->siteConfig, $this->logger);
			$Page->loadPage(basename($pageDir));
			
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
        
        $this->buildStaticPages();
        
        $FH->copyTemplateResources();
        
        $this->logger->message("Done.");
        $this->logger->message("Output directory: " . $this->siteConfig["build_path"]);
    }
    
    /*
        Builds static pages specified in config.ini
    */
    function buildStaticPages() {
        $vars = array();
        foreach($this->pages as $Page) {
			$vars["posts"][] = $Page;
        }
        
        foreach($this->siteConfig["staticPages"] as $pageName) {
            $Template = new Template($this->siteConfig, $this->logger);
            $pageHTML = $Template->compileTemplate($pageName, $vars);

            $FH = new FileHandler($this->siteConfig, $this->logger);
            $FH->writeSiteFiles($pageName, $pageHTML);   
        }
    }

}
?>