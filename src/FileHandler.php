<?php
namespace Steady;

class FileHandler {
    
	function __construct($logger) {
        $this->logger = $logger;   
    }
    
    function loadFileFromPath($path) {
        $data = file_get_contents($path);
        return $data;
    }
}
?>