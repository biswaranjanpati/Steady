<?php
namespace Steady;

class Template {
	function __construct($siteConfig, $logger) {
        $this->siteConfig = $siteConfig;
        $this->logger = $logger;
        
        $loader = new \Twig_Loader_Filesystem($this->siteConfig['template_path'] . '/');
        $this->twig = new \Twig_Environment($loader, array(
            'cache' => false
        ));
        $this->twig->addExtension(new TwigTruncatePExtension());

    }
    
    /*
        Renders the template given the name and variables to put into it.
    */
    public function compileTemplate($template, $vars) {
        
        $template = $template . ".twig";
        $expectedPath = $this->siteConfig['template_path'] . '/' . $template;
        if (!file_exists($expectedPath)) {
            $this->logger->error("Template does not exist.");
            $this->logger->info("Template file must exist at " . $expectedPath);
            exit(0);
        }
        return $this->twig->render($template, $vars);
    }

    //TODO: make a custom extension that allows truncate by p tag
    // https://stackoverflow.com/questions/19491989/limit-string-twig

}
?>