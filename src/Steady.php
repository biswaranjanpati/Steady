<?php
namespace Steady;

class Steady {
    
	function __construct() {
		$this->logger = new Logger();
        
        $this->logger->info("Welcome to Steady");
        
        $CFG = new Configuration($this->logger);
        $this->siteConfig = $CFG->siteConfig;
        $this->env = $CFG->env;
        
        $this->deepRefresh();

	}
    
    /*
        Full refresh of all pages
    */
    function deepRefresh() {
        $dirs = glob($this->siteConfig['page_path'] . '/*', GLOB_ONLYDIR);
        
        $pagesArray = array();
        
        foreach ($dirs as $pageDir) {
            $pageData = $this->processSinglePage($pageDir);
            
            $pagesArray[] = $pageData;

            $FH = new FileHandler($this->siteConfig, $this->logger);
            $FH->writeSinglePage($pageData);
        }
        
        $archive = $this->buildArchive($pagesArray);
        
        #var_dump($archive);
    }
    
    function buildArchive($pagesArray) {
        // Sort by newest first
        usort($pagesArray, function($a, $b) {
            return $a['timestamp'] - $b['timestamp']; 
        });
        
        
        foreach($pagesArray as $page) {
            var_dump($page);
            return 0;
            $pageVars = array(
                "metadata" => $page["page"]->metadata,
                "date" => date("Y-m-d", $page["timestamp"])
                /*
                TODO: don't put date here. convert date to timestamp and put it in metada when reading post.
                Can also remove the date translation elsewhere.
                */
            );
        }
        
        $vars = array(
            "meta" => $Page->metadata,
            "content" => $Page->content
        );
        $archive = $this->compileTemplate($tpl, $vars);
        
        return $archive;
    }
    
    function processSinglePage($pageDir) {
        $Page = new Page($this->siteConfig, $this->logger);
        $Page->loadPage(basename($pageDir));
        
        $tpl = "post";
        if (isset($Page->metadata['template'])) {
            $tpl = $Page->metadata['template'];
        }

        $vars = array(
            "meta" => $Page->metadata,
            "content" => $Page->content
        );
        $html = $this->compileTemplate($tpl, $vars);
        
        $date = \DateTime::createFromFormat($this->siteConfig["date_format"], $Page->metadata['date']);
        $timestamp = $date->format('U');
        
        $ret = array(
            "page" => $Page,
            "timestamp" => $timestamp,
            "compiledTpl" => $html
        );
        
        return $ret;
    }
    
	
	function compileTemplate($template, $vars) {
		$config = array(
			 "tpl_dir"       => $this->siteConfig['template_path'] . '/',
			 "cache_dir"     => "vendor/rain/raintpl/cache/",
             "auto_escape" => false
		);
		
		$TPL = new \Rain\Tpl;
		$TPL::configure($config);
		
		foreach ($vars as $key => $val) {
			$TPL->assign($key, $val);
		}
		
		return $TPL->draw($template, TRUE);
	}
    
    
}
?>