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
            #print_r($Post->metadata);
			
			
			$vars = array(
				"meta" => $Post->metadata,
				"content" => $Post->content
			);
			$html = $this->compileTemplate('post', $vars);
			
			print($html);

        }
    }
	
	function compileTemplate($template, $vars) {
		$config = array(
			 "tpl_dir"       => $this->siteConfig['template_path'] . '/',
			 "cache_dir"     => "vendor/rain/raintpl/cache/"
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