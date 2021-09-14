<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class StatCommandTest extends KernelTestCase
{
    /**
     * Test execute
     *
     * @return void
     */
    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('stat:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => $kernel->getProjectDir() . '/tests/logs/input/test.csv',
            '--output' => $kernel->getProjectDir() . '/tests/logs/output',
        ]);

        // verify output of the command
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString(
            '[OK] Processing ' . $kernel->getProjectDir() . '/tests/logs/input/test.csv', 
            $output
        );           
    }


    /**
     * Test Generated JSON files
     *
     * @return void
     */
    public function testGeneratedJSONFiles() {

        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('stat:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => $kernel->getProjectDir() . '/tests/logs/dump',
        ]);

        // assert txt and json files were generated
        $this->assertFileExists($json = $kernel->getProjectDir() . '/tests/logs/output/test.json');

        // get json to array
        $json_array = json_decode(file_get_contents($json), true);

        // assert ISBN of the book with longest checkout time
        $this->assertStringContainsString(
            $json_array['book_longest_checkout'], 
            '99-9263-544-1'
        );

        // assert total number of books checked out
        $this->assertStringContainsString($json_array['book_total_checkout'], 3);

        // assert Person ID of person with the largest checkouts currently
        $this->assertStringContainsString($json_array['person_largest_checkout'], 3);

        // assert Person ID of person has most checkouts
        $this->assertStringContainsString($json_array['person_most_checkout'], 3);
    }

    /**
     * Test Generated Text files
     *
     * @return void
     */
    public function testGeneratedTextFiles() {

        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('stat:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => $kernel->getProjectDir() . '/tests/logs/dump',
        ]);
    
        // assert txt and json files were generated
        $this->assertFileExists($txt = $kernel->getProjectDir() . '/tests/logs/output/test.txt');
        $this->assertFileExists($json = $kernel->getProjectDir() . '/tests/logs/output/test.json');

        // get file lines to array
        $txt_array = file($txt, FILE_IGNORE_NEW_LINES);

        // get json to array
        $json_array = json_decode(file_get_contents($json), true);

        // assert ISBN of the book with longest checkout time
        $this->assertStringContainsString(
            $txt_array[0], 
            'ISBN of the book with longest checkout time: 99-9263-544-1'
        );

        // assert total number of books checked out
        $this->assertStringContainsString(
            $txt_array[1], 
            'Total number of books checked out : 3'
        );

        // assert Person ID of person with the largest checkouts currently
        $this->assertStringContainsString(
            $txt_array[2], 
            'Person ID of person with the largest checkouts now: 3'
        );

        // assert Person ID of person has most checkouts
        $this->assertStringContainsString(
            $txt_array[3], 
            'Person ID of person has most checkouts: 3'
        );
    }
}