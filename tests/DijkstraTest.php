<?php

use \PHPUnit\Framework\TestCase;
include './src/Dijkstra.php';


class DijkstraTest extends TestCase
{

    protected static $graph;
    protected static $dijkstra;


    /**
     * Set the queue for all the tests.
     * The PriorityQueue class is needed for testing, as well as the test graph.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        static::$graph = unserialize('a:6:{i:2944;a:3:{i:3948;s:3:"945";i:4907;s:3:"980";i:5950;s:3:"850";}i:3948;a:4:{i:5950;s:4:"1328";i:9583;s:3:"510";i:6068;s:3:"772";i:2944;s:3:"945";}i:4907;a:2:{i:2944;s:3:"980";i:9583;s:4:"1152";}i:5950;a:2:{i:6068;s:4:"1272";i:2944;s:3:"850";}i:6068;a:1:{i:3948;s:3:"772";}i:9583;a:3:{i:3948;s:3:"510";i:6068;s:3:"885";i:2944;s:4:"1445";}}');
    }


    /**
     * Create the Dijkstra object with the given graph and source
     *
     * @return void
     */
    public function test_creation_of_dijkstra()
    {
        static::$dijkstra = new Dijkstra(static::$graph, 5950, true);
        $this->assertInstanceOf('Dijkstra', static::$dijkstra);
    }


    /**
     * Test with known results in the test graph.
     *
     * @return void
     */
    public function test_shortest_path_to()
    {
        $shortest_path = static::$dijkstra->shortestPathTo(9583);
        $this->assertCount(4, $shortest_path, "Test result doesn't contain 4 elements.");
        $this->assertEquals(5950, $shortest_path[0]['node_identifier']);
        $this->assertEquals(9583, $shortest_path[3]['node_identifier']);
        $this->assertEquals(2305, $shortest_path[3]['accumulated_weight']);

        $shortest_path = static::$dijkstra->shortestPathTo(4907);
        $this->assertCount(3, $shortest_path, "Test result doesn't contain 4 elements.");
        $this->assertEquals(5950, $shortest_path[0]['node_identifier']);
        $this->assertEquals(4907, $shortest_path[2]['node_identifier']);
        $this->assertEquals(1830, $shortest_path[2]['accumulated_weight']);
    }


    /**
     * Eliminate the static variables.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        static::$graph    = null;
        static::$dijkstra = null;
    }


}