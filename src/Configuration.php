<?php
namespace Steady;

class Configuration {
	function __construct($configPath, $logger) {
        $this->logger = $logger;
        
        $projectPath = dirname(realpath($configPath));
        $this->logger->message("Project Path: " . $projectPath);
        
        $config = $this->loadConfigFile($projectPath);
        
        $this->env = $config['env'];
        $this->siteConfig = $config[$this->env];
        $this->siteConfig["projectPath"] = $projectPath;
	}
    
	function loadConfigFile($projectPath) {
        $file = FileHandler::join_paths($projectPath, 'config.ini');
        
        if (substr_compare($file, "config.ini", -10) !== 0) {
            $this->logger->error("Config file must be named config.ini");
        }

        if (!file_exists($file)) {
            $this->logger->error("Config file not found: " . $file);
        }
        
        
        $this->logger->message("Loading config.ini");
        
        $config = parse_ini_file($file, true);
        
        foreach ($config[$config["env"]] as $key => $value) {
            switch($key) {
                case "page_path":
                case "template_path":
                    $path = FileHandler::join_paths($projectPath, $value);
                    if(!is_readable($path)) {
                        $this->logger->message("Checking path: " . $path);
                        $this->logger->error("Path unreadable: " . $key);
                    }
                    break;
                case "build_path":
                    $path = FileHandler::join_paths($projectPath, $value);
                    if(!is_writable($path)) {
                        $this->logger->message("Checking path: " . $path);
                        $this->logger->error("Path unreadable: " . $key);
                    }
                    break;
            }
        }
        
        return $config;
	}
}
?>