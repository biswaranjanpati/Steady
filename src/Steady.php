<?php
namespace Steady;

class Steady {
    
	function __construct() {
		$this->logger = new Logger();
        
        $this->logger->info("Welcome to Steady");
        
        $CFG = new Configuration($this->logger);
        $this->siteConfig = $CFG->siteConfig;
        $this->env = $CFG->env;
        
		$this->loadAllPages();
		
        $this->deepRefresh();

	}
    
	function loadAllPages() {
		$dirs = glob($this->siteConfig['page_path'] . '/*', GLOB_ONLYDIR);
		
		foreach ($dirs as $pageDir) {
			$Page = new Page($this->siteConfig, $this->logger);
			$Page->loadPage(basename($pageDir));
			
			$this->pages[] = $Page;
		}
		
		// Sort by newest first
        usort($this->pages, function($a, $b) {
            return $a->metadata['timestamp'] - $b->metadata['timestamp']; 
        });
	}
	
    /*
        Full refresh of all pages
    */
    function deepRefresh() {
       
        foreach ($this->pages as $Page) {
            $FH = new FileHandler($this->siteConfig, $this->logger);
            $FH->writeSinglePage($Page);
        }
        
        $archiveHtml = $this->buildArchive();
        $FH = new FileHandler($this->siteConfig, $this->logger);
		$FH->writeSiteFiles("archive", $archiveHtml);
		
    }
    
    function buildArchive() {
		
        $vars = array();
        foreach($this->pages as $Page) {
			$vars["posts"][] = $Page->metadata;
        }

		$Template = new Template($this->siteConfig, $this->logger);
		$archiveHtml = $Template->compileTemplate("archive", $vars);
        
        return $archiveHtml;
    }

}
?>