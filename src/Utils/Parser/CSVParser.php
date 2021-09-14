<?php

/*
 * This file responsible for parsing a CSV file
 */
namespace App\Utils\Parser;

use Generator;

class CSVParser implements ParserInterface {

    /**
     * Get the file and return generator.
     */
    public function parse(string $filePath) : Generator {
        $counter = 0;

        // open the file
        $import = fopen($filePath, 'r');

        while (($values = fgetcsv($import)) !== false) {
            $counter++;

            // get the header
            if ($counter == 1) {
                $keys = $values;
                continue;
            }

            $row = array_combine($keys, $values);
            yield $row;
        }

        return $row;
    }
}