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
        $this->twig->addExtension(new \Sachleen\Twig\TwigTruncatePExtension());

    }
    
    /*
        Renders the template given the name and variables to put into it.
    */
    public function compileTemplate($template, $vars) {
        
        $template = $template;
        $expectedPath = $this->siteConfig['template_path'] . '/' . $template;
        if (!file_exists($expectedPath)) {
            $this->logger->info("Template file must exist at " . $expectedPath);
            $this->logger->error("Template [$template] does not exist.");
            exit(0);
        }
        
        $vars['BASE_URL'] = $this->siteConfig['base_url'];
        return $this->twig->render($template, $vars);
    }
}
?>