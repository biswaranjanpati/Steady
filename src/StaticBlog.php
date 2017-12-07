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
        Full refresh of all posts
    */
    function deepRefresh() {
        $dirs = glob($this->siteConfig['post_path'] . '/*', GLOB_ONLYDIR);
        
        foreach ($dirs as $postDir) {
            $Post = new Post($this->siteConfig, $this->logger);
            $Post->loadPost(basename($postDir));
            print_r($Post->metadata);
        }
    }
    
    
}
?>