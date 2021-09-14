<?php

/*
 * This class is responsible for generating JSON file
 */
namespace App\Utils\Output;

class JSONFileMaker implements FileMakerInterface {

    /**
     * Generate a JSON file
     *
     * @param array $data
     * @param string $path
     * @param string $filename
     * @return string full path of the generated file.
     */
    public function generate(array $data, string $path, string $filename) : string {

        $fullPath = $path . '/' . $filename . '.json';

        // write file out
        $fp = fopen($fullPath, 'w');
        fwrite($fp, json_encode($data));
        fclose($fp);

        return $fullPath;
    }
}