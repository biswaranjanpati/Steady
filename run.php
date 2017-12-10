<?php
require "vendor/autoload.php";

use Steady\Steady;
use Rain\Tpl;
use Michelf\MarkdownExtra;

$configPath = "config.ini";

if (defined('STDIN')) {
    if (count($argv) > 1) {
        $configPath = $argv[1];
    }
} else {
    if (isset($_GET["config"])) {
        $configPath = $_GET['type'];
    }
}

$sb = new Steady($configPath);
?>