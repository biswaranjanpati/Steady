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
            return $a->metadata['timestamp'] - $b->metadata['timestamp']; 
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
        
        $this->logger->message("Writing page: archive.html");
        $archiveHtml = $this->buildArchivePage();
        $FH = new FileHandler($this->siteConfig, $this->logger);
		$FH->writeSiteFiles("archive", $archiveHtml);
		
        $this->logger->message("Writing page: index.html");
        $indexHtml = $this->buildIndexPage();
		$FH->writeSiteFiles("index", $indexHtml);
        
        $FH->copyTemplateResources();
        
        $this->logger->message("Done.");
        $this->logger->message("Output directory: " . $this->siteConfig["build_path"]);
    }
    
    /*
        returns the html for index.html
    */
    function buildIndexPage() {
        $vars = array();
        foreach($this->pages as $Page) {
			$vars["posts"][] = $Page;
        }

		$Template = new Template($this->siteConfig, $this->logger);
		$indexHtml = $Template->compileTemplate("index", $vars);
        
        return $indexHtml;
    }
    
    /*
        returns the html for archive.html
    */
    function buildArchivePage() {
		
        $vars = array();
        foreach($this->pages as $Page) {
			$vars["posts"][] = $Page;
        }

		$Template = new Template($this->siteConfig, $this->logger);
		$archiveHtml = $Template->compileTemplate("archive", $vars);
        
        return $archiveHtml;
    }

}
?>