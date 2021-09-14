# Library Console Application
This console application generates the following statistical data and outputs json/text files in a directory:

- which person has the most checkouts (which person_id)
- which book was checked out the longest time in total (summed up over all
transactions)
- how many books are checked out at this moment
- who currently has the largest number of books (which person_id)

## System requirements
This application was development and tested in the following environment.

- Ubuntu 20.04 LTS Server
- PHP 8.0.10 
- Symfony 5 (Technical requirements - https://symfony.com/doc/current/setup.html)
- Composer

## How to setup this project
- Extract the compressed directory to a directory in a server running PHP
- Run `composer install` at the root of the project
- Ensure the tests/logs and logs directory at the root of the project is writable

## Usage ( from the console)php bin/console stat:generate --help
You can generate statistics for a single file or multiple files in a directory

#### Generating statistics for multiple files
- Copy files transaction files to /library/logs/input
- Then run `php bin/console stat:generate` which will generate statistics for each of the files

#### Generating statistics for a single file
```
php bin/console stat:generate /home/emeka/projects/library/logs/input/2021-02.csv
```

You can specify output directory
```
php bin/console stat:generate /home/emeka/projects/library/logs/input/2021-02.csv --output=/home/embah/projects/library/logs
```

## Find more usage by running help
- Run `php bin/console stat:generate --help`

NB: Ensure output directory is writable

## Run test

`php ./vendor/bin/phpunit`

