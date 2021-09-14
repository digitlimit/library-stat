<?php

/*
 * Interface to be implemented by file makers
 */
namespace App\Utils\Output;

interface FileMakerInterface 
{

    /**
     * Generate a file
     *
     * @param array $data
     * @param string $path
     * @param string $filename
     * @return string full path of the generated file.
     */
    public function generate(array $data, string $path, string $filename) : string;
}
