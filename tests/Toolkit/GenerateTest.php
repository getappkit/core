<?php

namespace Toolkit;

use Appkit\Toolkit\Generate;
use PHPUnit\Framework\TestCase;

class GenerateTest extends TestCase
{

    public function testfibonacci()
    {
        $fibonacci = Generate::fibonacci(10);

        // Test the first 10 numbers in the Fibonacci sequence
        $expected = [0, 1, 1, 2, 3, 5, 8, 13, 21, 34];
        foreach ($expected as $value) {
            $this->assertEquals($value, $fibonacci->current());
            $fibonacci->next();
        }
    }



    public function testPermutations()
    {
        $permutations = Generate::permutations([1, 2, 3]);

        // Test the first 6 permutations
        $expected = [
            [1, 2, 3],
            [1, 3, 2],
            [2, 1, 3],
            [2, 3, 1],
            [3, 1, 2],
            [3, 2, 1],
        ];
        foreach ($expected as $value) {
            $this->assertEquals($value, $permutations->current());
            $permutations->next();
        }

    }

    public function testFactorial()
    {
        $factorial = Generate::factorial(10);

        // Test the first 10 factorials
        $expected = [1, 2, 6, 24, 120, 720, 5040, 40320, 362880, 3628800];
        foreach ($expected as $value) {
            $this->assertEquals($value, $factorial->current());
            $factorial->next();
        }

    }
}
