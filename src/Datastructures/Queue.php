<?php

declare(strict_types=1);

namespace Appkit\Datastructures;

use SplDoublyLinkedList;
use SplQueue;

class Queue
{
    /**
     * @var SplQueue The internal queue implementation
     */
    private SplQueue $queue;

    /**
     * Queue constructor.
     */
    public function __construct()
    {
        $this->queue = new SplQueue();
        // Set the iterator mode of the internal queue to FIFO (First In, First Out)
        // and delete the elements when iterating over the queue
        $this->queue->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO | SplDoublyLinkedList::IT_MODE_DELETE);
    }

    /**
     * Adds an item to the queue.
     *
     * @param mixed $item The item to add to the queue
     * @return static Returns the current instance of the Queue class for method chaining
     */
    public function add(mixed $item): static
    {
        $this->queue->enqueue($item);
        return $this;
    }

    /**
     * Retrieves and removes the next item from the queue.
     *
     * @return mixed|null The next item from the queue, or null if the queue is empty
     */
    public function get(): mixed
    {
        if (!$this->isEmpty()) {
            return $this->queue->dequeue();
        }

        return null;
    }

    /**
     * Checks if the queue is empty.
     *
     * @return bool Returns true if the queue is empty, false otherwise
     */
    public function isEmpty(): bool
    {
        return $this->queue->isEmpty();
    }

    /**
     * Retrieves the number of items in the queue.
     *
     * @return int The number of items in the queue
     */
    public function count(): int
    {
        return $this->queue->count();
    }
}
