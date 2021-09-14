<?php

/*
 * This class is responsible for generating transaction statistics
 */
namespace App\Utils;

use Generator;

class Transaction 
{
    /**
     * Transactions from file
     *
     * @var Generator
     */
    private Generator $transactions;

    /**
     * ISBN of the book with longest checkout time
     *
     * @var string
     */
    private ?string $bookLongestCheckout;

    /**
     * Total number of books checked out 
     * 
     * @var integer
     */
    private int $bookTotalCheckout = 0;

    /**
     * Person ID of person with the largest checkouts
     *
     * @var integer
     */
    private ?int $personLargestCheckout = null;

    /**
     * Person ID of person  has most checkouts
     *
     * @var integer
     */
    private ?int $personMostCheckout = null;

    public function __construct(Generator $transactions = null) {
        if($transactions) $this->transactions = $transactions;
    }

    /**
     * Set transactions
     *
     * @param Generator $transactions
     * @return void
     */
    public function setTransactions(Generator $transactions) : void
    {
        $this->transactions = $transactions;
    }

    /**
     * Generate statistics
     *
     * @return self
     */
    public function generateStatistics() {

        $persons = [
            'most_checkout' => [],
            'largest_book' => [],
        ];

        $times = [];

        $time_logs = [];

        foreach ($this->transactions as $trx) {

            # 1 - which book was checked out the longest time in total (summed up over all transactions)
            if ($trx['action'] == 'check-out') {
                // check-out 
                $times[$trx['isbn']] = $trx['timestamp'];
            }else{
                // checked-in
                if ( isset($times[$trx['isbn']]) ) {
                    // check-in timestamp - check-out timestamp
                    $time = strtotime($trx['timestamp']) - strtotime($times[$trx['isbn']]);  

                    // accumulate the time for this isbn
                    if(!isset($time_logs[$trx['isbn']])){
                        $time_logs[$trx['isbn']] = $time;
                    }else{
                        $time_logs[$trx['isbn']] += $time;
                    }
                }
            }

            # 2 - how many books are checked out at this moment
            if ($trx['action'] == 'check-out') $this->bookTotalCheckout++;    

            # 3 - who currently has the largest number of books (which person_id)
            if ($trx['action'] == 'check-out') {
                if (!isset($persons['largest_book'][$trx['person']])) {
                    $persons['largest_book'][$trx['person']] = 1;
                } else {
                    $persons['largest_book'][$trx['person']] ++;
                }
            }else{
                // if book is returned we decreament the total check-out for that person
                if (isset($persons['largest_book'][$trx['person']])) {
                    $persons['largest_book'][$trx['person']] --;
                }
            }
 
            # 4 - which person has the most checkouts (which person_id)
            if ($trx['action'] == 'check-out') {
                if (!isset($persons['most_checkout'][$trx['person']])) {
                    $persons['most_checkout'][$trx['person']] = 1;
                } else {
                    $persons['most_checkout'][$trx['person']] ++;
                }
            }
        }

        // set person ID person with the largest checkouts at moment
        $this->personLargestCheckout = array_keys(
            $persons['largest_book'], max($persons['largest_book'])
        )[0];

        // set person ID person with the most checkouts
        $this->personMostCheckout = array_keys(
            $persons['most_checkout'], max($persons['most_checkout'])
        )[0];

        // get the ISBN of book longest checkout
        $this->bookLongestCheckout = array_keys($time_logs, max($time_logs))[0];
        
        return $this;
    }

    /**
     * Return statistics
     *
     * @return array
     */
    public function getStatistics() : array {
        return [
            'book_longest_checkout'   => $this->bookLongestCheckout, 
            'book_total_checkout'     => $this->bookTotalCheckout,
            'person_largest_checkout' => $this->personLargestCheckout,
            'person_most_checkout'    => $this->personMostCheckout
        ];
    }
}