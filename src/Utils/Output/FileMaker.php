<?php

/*
 * This file is responsible for making file
 */
namespace App\Utils\Output;

class FileMaker {
    
    /**
     * Path to output (directory)
     *
     * @var string
     */
    private string $path;

    /**
     * File maker Interface
     *
     * @var FileMakerInterface
     */
    private FileMakerInterface $maker;

    public function __construct(string $path = null, FileMakerInterface $maker = null){
        $this->path = $path;
        if($maker) $this->maker = $maker;
    }

    /**
     * Set the file path.
     * 
     * @param string $path Absolute path to the file
     */
    public function setPath(string $path) : void {
        $this->path = $path;
    }

    /**
     * Set maker
     *
     * @param FileMakerInterface $maker
     * @return void
     */
    public function setMaker(FileMakerInterface $maker)
    {
        $this->maker = $maker;
    }

    /**
     * Parse and return data
     *
     * @return string - full path of geneated file
     */
    public function make(array $data, string $filename) : string
    {
        return $this->maker->generate($data, $this->path, $filename);
    }
}