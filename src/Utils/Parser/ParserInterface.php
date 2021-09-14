<?php

/*
 * Interface to be implemented by parsers
 */
namespace App\Utils\Parser;

use Generator;

interface ParserInterface 
{
    /**
     * Get the file and return generator.
     */
    public function parse(string $filePath) : Generator;
}