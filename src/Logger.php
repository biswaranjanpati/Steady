<?php
namespace Steady;

class Logger {
    
	function __construct() {
		$this->info("Logger Started");
	}
    
    function error($message) {
        print("Error: " . $message);
        print("\n");
    }
    
    function info($message) {
        print($message);
        print("\n");
    }
    
    function log_array($array) {
        print_r($array);
    }
}
?>