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
            $postData = $this->processSinglePost($postDir);
            
            $FH = new FileHandler($this->siteConfig, $this->logger);
            $FH->writePost($postData);
        }
    }
    
    function processSinglePost($postDir) {
        $Post = new Post($this->siteConfig, $this->logger);
        $Post->loadPost(basename($postDir));
        
        $tpl = "post";
        if (isset($Post->metadata['template'])) {
            $tpl = $Post->metadata['template'];
        }

        $vars = array(
            "meta" => $Post->metadata,
            "content" => $Post->content
        );
        $html = $this->compileTemplate($tpl, $vars);
        
        $ret = array(
            "slug" => $Post->metadata['slug'],
            "date" => $Post->metadata['date'],
            "html" => $html
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