<?php
namespace Server\Event;
use Think\Controller;
class WeiXinEvent extends Controller {

	/**
    *xml转成数组
    */
    public function xmlstr_to_array($xmlstr) {
      $doc = new \DOMDocument();
      $doc->loadXML($xmlstr);
      return $this->domnode_to_array($doc->documentElement);
    }

	//数组转xml
    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
             if (is_numeric($val))
             {
                $xml.="<".$key.">".$val."</".$key.">"; 

             }
             else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
        }
        $xml.="</xml>";
        return $xml; 
    }

    public function domnode_to_array($node) {
      $output = array();
      switch ($node->nodeType) {
        case XML_CDATA_SECTION_NODE:
        case XML_TEXT_NODE:
        $output = trim($node->textContent);
        break;
        case XML_ELEMENT_NODE:
          for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
            $child = $node->childNodes->item($i);
            $v = $this->domnode_to_array($child);
            if(isset($child->tagName)) {
               $t = $child->tagName;
               if(!isset($output[$t])) {
                $output[$t] = array();
               }
               $output[$t][] = $v;
            }
            elseif($v) {
              $output = (string) $v;
            }
          }
          if(is_array($output)) {
            if($node->attributes->length) {
              $a = array();
              foreach($node->attributes as $attrName => $attrNode) {
               $a[$attrName] = (string) $attrNode->value;
              }
              $output['@attributes'] = $a;
            }

            foreach ($output as $t => $v) {
              if(is_array($v) && count($v)==1 && $t!='@attributes') {
               $output[$t] = $v[0];
              }
            }
          }
        break;
      }
      return $output;
    }
}