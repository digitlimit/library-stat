<?php

/*
 * This class is responsible for generating text file
 */
namespace App\Utils\Output;

class TextFileMaker implements FileMakerInterface {

    /**
     * Title mapper
     *
     * @var array
     */
    private array $titles = array();

    public function __construct(array $titles = []) {
        $this->titles = $titles;
    }

    /**
     * Generate a text file
     *
     * @param array $data
     * @param string $path
     * @param string $filename
     * @return string full path of the generated file.
     */
    public function generate(array $data, string $path, string $filename) : string {
        $fullPath = $path . '/' . $filename . '.txt';

        // write to file
        $file = fopen($fullPath, 'w');
        foreach ($data as $key => $value) {
            // use title if it exists
            $title = isset($this->titles[$key]) ? $this->titles[$key] : $key;
            fwrite($file, "$title: $value" . "\n");
        }
        fclose($file);

        return $fullPath;
    }
}