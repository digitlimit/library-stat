<?php

/*
 * This class is responsible for parsing XML file
 */
namespace App\Utils\Parser;

use Generator;
use XMLReader;

class XMLParser implements ParserInterface {

    /**
     * XML node name
     *
     * @var string
     */
    private string $nodeName;

    /**
     * XMLParser constructor.
     */
    public function __construct(string $nodeName) {
        $this->nodeName = $nodeName;
    }

    /**
     * Get the file and return generator.
     */
    public function parse(string $filePath) : Generator 
    {
        $reader = new XMLReader();
        $reader->open($filePath);

        while($reader->read()) {
         
            if($reader->name == $this->nodeName 
                && $reader->nodeType == XMLReader::ELEMENT) { 
                // Gets the current entire object content (string)
                $row = $this->getRow($reader->readOuterXML());
                yield $row;
            }
        }
    }

    protected function getRow($xmlObject) : array {

        $xmlArray = json_decode(json_encode(simplexml_load_string($xmlObject)), true); 
        $row = [];

        foreach ($xmlArray as $key => $value) {
            if (is_array($value) 
                && isset($value['@attributes']) 
                && $value['@attributes']) {
                    $row[$key] = array_values($value['@attributes'])[0];           
            }else{
                $row[$key] = $value;
            }        
        }

        return $row;
    }
}