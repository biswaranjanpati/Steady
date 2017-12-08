<?php
namespace Steady;

class Configuration {
	function __construct($logger) {
        $this->logger = $logger;
        $config = $this->loadConfigFile("config.ini");

        $this->env = $config['env'];
        $this->siteConfig = $config[$this->env];
	}
    
	function loadConfigFile($file = "config.ini") {
        if (!file_exists($file)) {
            throw new \Exception('Config file does not exist!');
        }
        
        $this->logger->info("Loading config: " . $file);
        
        $config = parse_ini_file($file, true);
        
        /*
        TODO: verify all paths exist and are writable
        */
        
        return $config;
	}
}
?>