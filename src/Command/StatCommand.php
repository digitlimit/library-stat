<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

use App\Utils\Stat;

class StatCommand extends Command
{
    protected static $defaultName = 'stat:generate';
    protected static $defaultDescription = 'Generate library transaction statistics from file/directorys';
    
    protected $inputDir;
    protected $outputDir;

    /**
     * Allowed extensions for files to be processed
     *
     * @var array
     */
    protected array $allowedExtensions = [
        'csv',
        'xml'
    ];

    /**
     * Titles for TextFileMaker output
     *
     * @var array
     */
    protected array $titles = [
        'book_longest_checkout'   => 'ISBN of the book with longest checkout time', 
        'book_total_checkout'     => 'Total number of books checked out ',
        'person_largest_checkout' => 'Person ID of person with the largest checkouts now',
        'person_most_checkout'    => 'Person ID of person has most checkouts'
    ];

    public function __construct(KernelInterface $kernel) {
        parent::__construct();

        $this->inputDir = $kernel->getProjectDir() . '/logs/input';
        $this->outputDir = $kernel->getProjectDir() . '/logs/output';
    }

    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('input', InputArgument::OPTIONAL, 'Absolute path to the input directory/file')
            ->addOption('output', null, InputOption::VALUE_OPTIONAL, 'An optional absolute path for output directory')
            // ->addOption('queue', null, InputOption::VALUE_NONE, 'An option that put the import in the queue')
        ;
    }

    /**
     * Execute the console command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // memory the application has used before this funtion is called
        $io->success(round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB');

        // get paths
        $filePath = $this->getInputFilePath($input);
        $inputDir = $this->getInputPath($input);
        $outputDir = $this->getOutputPath($input);

        if( $filePath ) {

            // run input from a file

            if (!file_exists($filePath)) {
                $io->error('Input file does not exist');
                return Command::FAILURE;
            }

            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
            if (!in_array($ext, $this->allowedExtensions)) {
                $io->error("$ext file is not allowed");
                return Command::FAILURE;
            }

            $io->success('Processing '. $filePath);
            
            $stat = (new Stat())
            ->setAllowedExtensions($this->allowedExtensions)
            ->setTextTitles($this->titles)
            ->run($filePath, $outputDir);

            // feedback
            if ($stat) {
                $io->success('Done!');
            }else{
                $io->error('An error occurred!');
            }
            
        }else{
            
            // run input from directory

            if (!is_dir($inputDir)) {
                $io->error('Input directory does not exist');
                return Command::FAILURE;
            }

            if ( $files = array_diff(scandir($inputDir), array('..', '.')) ) {

                foreach($files as $file) {

                    $filePath = $inputDir . '/' . $file;

                    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                    if (!in_array($ext, $this->allowedExtensions)) {
                        $io->error("$ext file is not allowed");
                        return Command::FAILURE;
                    }
            
                    // feedback
                    $io->success('Processing '. $filePath);
            
                    $stat = (new Stat())
                    ->setAllowedExtensions($this->allowedExtensions)
                    ->setTextTitles($this->titles)
                    ->run($filePath, $outputDir);
        
                    // feedback
                    if ($stat) {
                        $io->success('Done!');
                    }else{
                        $io->error('An error occurred!');
                    }
                }

            }
        }
      
        $io->success("Memory Used: " . round(memory_get_usage() / 1024 / 1024, 2) . ' MB');
        return Command::SUCCESS;
    }

    /**
     * A helper for getting input directory/file
     *
     * @param InputInterface $input
     * @return string
     */
    public function getInputPath(InputInterface $input) {

        if( $input->getArgument('input') !== null ) {
           return $input->getArgument('input');
        }

        return $this->inputDir;
    }

    /**
     * A helper for getting input file path
     *
     * @param InputInterface $input
     * @return string|bool
     */
    public function getInputFilePath(InputInterface $input) : string|bool {

        $ext = pathinfo($input->getArgument('input'), PATHINFO_EXTENSION);

        if($ext) return $input->getArgument('input');

        return false;
    }

    /**
     * A helper for getting output directory
     *
     * @param InputInterface $input
     * @return string
     */
    public function getOutputPath(InputInterface $input) : string {
      
        if( $input->getOption('output') ) {
           return $input->getOption('output');
        }

        return $this->outputDir;
    }
}
