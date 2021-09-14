<?php

/*
 * This class is responsible for parsing the file
 */
namespace App\Utils\Parser;

use Generator;

class Parser {
    
    /**
     * Path to the XML file.
     *
     * @var string
     */
    private string $filePath;

    /**
     * Parse interface
     *
     * @var ParserInterface
     */
    private ParserInterface $parser;

    public function __construct(string $filePath = null, ParserInterface $parser = null){
        $this->filePath = $filePath;
        if($parser) $this->parser = $parser;
    }

    /**
     * Set the file path.
     * 
     * @param string $filePath Absolute path to the file
     */
    public function setPath(string $filePath) : void {
        $this->filePath = $filePath;
    }

    /**
     * Undocumented function
     *
     * @param ParserInterface $parser
     * @return void
     */
    public function setParser(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parse and return data
     *
     * @return array
     */
    public function fetch() : Generator
    {
        return $this->parser->parse($this->filePath);
    }
}