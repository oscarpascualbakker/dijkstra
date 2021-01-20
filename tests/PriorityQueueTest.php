<?php

use \PHPUnit\Framework\TestCase;
include './src/EmptyQueueException.php';


class PriorityQueueTest extends TestCase
{

    protected static $queue;


    /**
     * Set the queue for all the tests.
     * The PriorityQueue class is needed for testing.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        static::$queue = new PriorityQueue;
    }


    public function test_new_queue_is_empty()
    {
        $this->assertTrue(static::$queue->isEmpty());
    }


    public function test_an_item_is_added_to_the_queue()
    {
        static::$queue->push("Test item", 1);
        $this->assertEquals(1, static::$queue->count());
    }


    public function test_an_item_is_removed_from_the_queue()
    {
        $item = static::$queue->pop();

        $this->assertEquals("Test item", $item);
        $this->assertEquals(0, static::$queue->count());
        $this->assertTrue(static::$queue->isEmpty());
    }


    /**
     * Add several elements with different priorities to the [empty] queue.
     * The queue should extract those with higher priority.
     * Elements are strategically added in order to have a heap like this:
     *                1
     *          6            12
     *      24     7     13      18
     *    25  98
     *
     * @return void
     */
    public function test_add_elements_and_pop_by_priority()
    {
        static::$queue->push("Test item 1", 13);
        static::$queue->push("Test item 2", 18);
        static::$queue->push("Test item 3", 25);
        static::$queue->push("Test item 4", 98);
        static::$queue->push("Test item 5", 24);
        static::$queue->push("Test item 6", 1);
        static::$queue->push("Test item 7", 6);
        static::$queue->push("Test item 8", 12);
        static::$queue->push("Test item 9", 7);
        $this->assertEquals(9, static::$queue->count());

        //And now, pop 3 elements and see if they are "Test item 6",
        // "Test item 7" and "Test item 9".
        $item1 = static::$queue->pop();
        $this->assertEquals("Test item 6", $item1);

        $item2 = static::$queue->pop();
        $this->assertEquals("Test item 7", $item2);

        $item3 = static::$queue->pop();
        $this->assertEquals("Test item 9", $item3);
    }


    /**
     * Get an element and change it's priority to 1.  It should move up to the first place.
     *
     * @return void
     */
    public function test_modify_priority_of_element()
    {
        static::$queue->change_priority("Test item 1", 1);

        //And now, if we pop the highest priority element it should be "Element 1".
        $item = static::$queue->pop();
        $this->assertEquals("Test item 1", $item);
    }


    /**
     * Test the capture of an exception.
     * In the previous test there were 6 elements left in the queue.
     *
     * @return void
     */
    public function test_exception_thrown_when_popping_on_empty_queue()
    {
        $this->assertEquals(5, static::$queue->count());
        $this->assertFalse(static::$queue->isEmpty());

        $this->expectException(EmptyQueueException::class);
        $this->expectExceptionMessage("Queue is empty");

        static::$queue->purge();
        $item = static::$queue->pop();                   //  <- Exception!!!

        $this->assertEquals(0, static::$queue->count());
        $this->assertTrue(static::$queue->isEmpty());
    }


    /**
     * Eliminate the queue.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        static::$queue = null;
    }


}