<?php
namespace Steady;

class TwigTruncatePExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('TruncateP', array($this, 'truncateP')),
        );
    }

    public function truncateP($text, $pLen = 2, $endStr = '')
    {
        $dom = new \DOMDocument();
        $dom->loadHTML((mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8')));
        $paragraphs = $dom->getElementsByTagName('p');
        
        
        
        $count = min($pLen, $paragraphs->length);
        $retStr = "";
        for ($i = 0; $i < $count; $i++) {
            #var_dump($paragraphs->item($i));
            #echo $dom->saveXML($paragraphs->item($i)) . 'sep';
            $retStr .= $dom->saveXML($paragraphs->item($i));
        }
        
        return $retStr . $endStr;
    }
}
?>