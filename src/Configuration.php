<?php
namespace Steady;

class Configuration {
	function __construct($configPath, $logger) {
        $this->logger = $logger;
        $config = $this->loadConfigFile($configPath);

        $this->env = $config['env'];
        $this->siteConfig = $config[$this->env];
        $this->siteConfig["staticPages"] = $config["staticPages"]["site_page"];
	}
    
	function loadConfigFile($file) {
        
        if (substr_compare($file, "config.ini", -10) !== 0) {
            $this->logger->error("Config file must be named config.ini");
        }

        if (!file_exists($file)) {
            $this->logger->error("Config file not found: " . $file);
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