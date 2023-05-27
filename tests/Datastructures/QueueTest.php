<?php

namespace Datastructures;

use Appkit\Datastructures\Queue;
use PHPUnit\Framework\TestCase;

class QueueTest extends TestCase
{
    public function testAddAndGet(): void
    {
        $queue = new Queue();

        // Add items to the queue
        $queue->add('Item 1');
        $queue->add('Item 2');
        $queue->add('Item 3');

        // Retrieve items from the queue and assert the order
        $this->assertEquals('Item 1', $queue->get());
        $this->assertEquals('Item 2', $queue->get());
        $this->assertEquals('Item 3', $queue->get());
    }

    public function testIsEmpty(): void
    {
        $queue = new Queue();

        // The queue should be empty initially
        $this->assertTrue($queue->isEmpty());

        // Add an item to the queue
        $queue->add('Item');

        // The queue should not be empty after adding an item
        $this->assertFalse($queue->isEmpty());
    }

    public function testCount(): void
    {
        $queue = new Queue();

        // The queue should be empty initially
        $this->assertEquals(0, $queue->count());

        // Add items to the queue
        $queue->add('Item 1');
        $queue->add('Item 2');

        // The queue should have 2 items
        $this->assertEquals(2, $queue->count());

        // Remove an item from the queue
        $queue->get();

        // The queue should have 1 item after removing an item
        $this->assertEquals(1, $queue->count());
    }
}

