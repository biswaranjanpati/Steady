<?php
namespace Steady;

class Template {
	function __construct($siteConfig, $logger) {
        $this->siteConfig = $siteConfig;
        $this->logger = $logger;
	}
    
	public function compileTemplate($template, $vars) {
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