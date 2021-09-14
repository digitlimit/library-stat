<?php

/*
 * This class is responsible running stat process
 */
namespace App\Utils;

use App\Utils\Parser\CSVParser;
use App\Utils\Parser\XMLParser;
use App\Utils\Parser\Parser;
use App\Utils\Output\FileMaker;
use App\Utils\Output\JSONFileMaker;
use App\Utils\Output\TextFileMaker;
use App\Utils\Transaction;

class Stat
{
    /**
     * Default list of allowed file types
     *
     * @var array
     */
    private array $allowedExtensions = ['csv', 'xml'];

    /**
     * Default text titles for TextFileMaker
     *
     * @var array
     */
    private array $textTitles = [];

    /**
     * Set allowed file types
     *
     * @param array $allowedExtensions
     * @return self
     */
    public function setAllowedExtensions(array $allowedExtensions): self {
        $this->allowedExtensions = $allowedExtensions;
        return $this;
    }

    /**
     * Set text titles for TextFileMaker
     *
     * @param array $textTitles
     * @return self
     */
    public function setTextTitles(array $textTitles): self {
        $this->textTitles = $textTitles;
        return $this;
    }
    
    /**
     * Run statistic process
     *
     * @return boolean
     */
    public function run(string $filePath, string $outputDir): bool
    {
         // file info
         $pathInfo = pathinfo($filePath);
         $filename = $pathInfo['filename'];
         $ext = strtolower($pathInfo['extension']);

         // check if the file is allowedExtensions
         if(!in_array($ext, $this->allowedExtensions)) return false;

         // parse file
         if($ext == 'csv'){
             $parser = new Parser($filePath, new CSVParser());
         }else{
             $parser = new Parser($filePath, new XMLParser('record'));
         }
         
         // generate statistics
         $transactions = $parser->fetch();
         $transaction = new Transaction($transactions);
         $stat = $transaction->generateStatistics()->getStatistics();

         // output a json file
         $maker = new FileMaker($outputDir, new JSONFileMaker());
         $maker->make($stat, $filename);

         // output text file as well
         $maker = new FileMaker($outputDir, new TextFileMaker($this->textTitles));
         $maker->make($stat, $filename);

        return true;
    }
}