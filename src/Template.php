<?php
namespace Steady;

class Template {
	function __construct($siteConfig, $logger) {
        $this->siteConfig = $siteConfig;
        $this->logger = $logger;
        
        $path = FileHandler::join_paths($this->siteConfig["projectPath"], $this->siteConfig['template_path']);
        
        $loader = new \Twig_Loader_Filesystem($path);
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
        
        $expectedPath = FileHandler::join_paths($this->siteConfig["projectPath"], $this->siteConfig['template_path'], $template);

        if (!file_exists($expectedPath)) {
            $this->logger->info("Template file must exist at " . $expectedPath);
            $this->logger->error("Template [$template] does not exist.");
        }
        
        $vars['BASE_URL'] = $this->siteConfig['base_url'];
        return $this->twig->render($template, $vars);
    }
}
?>