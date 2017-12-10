<?php
namespace Steady;

class Logger {
    
	function __construct() {
		$this->message("Logger Started");
	}
    
    function error($message) {
        print("Error: " . $message);
        print("\n");
        exit(1);
    }
    
    function info($message) {
        print("Info: " . $message);
        print("\n");
    }
    
    function message($message) {
        print($message);
        print("\n");
    }
    
    function log_array($array) {
        print_r($array);
    }
}
?>