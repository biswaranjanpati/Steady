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
            $this->logger->error("config.ini file not found");
        }
        
        $this->logger->message("Loading config: " . $file);
        
        $config = parse_ini_file($file, true);
        
        foreach ($config[$config["env"]] as $key => $value) {
            switch($key) {
                case "page_path":
                case "template_path":
                    if(!is_readable($value)) {
                        $this->logger->error("Path unreadable: " . $key);
                    }
                    break;
                case "build_path":
                    if(!is_writable($value)) {
                        $this->logger->error("Path unwritable: " . $key);
                    }
                    break;
            }
        }
        
        return $config;
	}
}
?>