<?php

require 'PriorityQueue.php';


/**
 * This implementation of the Dijkstra algorithm uses an adjacency list.  This means that it
 * is better for sparse graphs rather than dense.
 *
 * It uses a priority queue but is able to manage directed and undirected graphs.  If it is
 * a directed graph it will shorten the loop, which is great in terms of performance.
 *
 * The graph is passed by reference, so no extra memory is used.  This is good for big graphs.
 *
 * Calculations are done
 *
 * Assumptions:
 *    - The graph has integer nodes.
 *    - The distances are positive integers (To Do: check negative weights).
 *
 * @author Oscar Pascual <oscar.pascual@gmail.com>
 */
class Dijkstra
{

    // The graph, although it is a reference to the original one
    protected $graph;

    // The source.  Needed for returning the shortest path.
    protected $source;

    // Distances from the source node to each other node
    protected $distances;

    // The previous node(s) in the path to the current node
    protected $previous;

    // Nodes which have yet to be processed
    protected $queue;

    // Time of algorithm calculation
    protected $time;

    // Graph connectivity: directed or undirected
    protected $is_directed;

    // Define infinite value
    const INT_MAX = 0x7FFFFFFF;


    /**
     * Create a new Dijkstra object with graph and source node.
     * Graph is a reference to the outside graph as we only need to read its information.
     * If we pass an undirected graph we have room for optimization.  ;-)
     */
    public function __construct(&$graph, $source, $is_directed = false)
    {
        $this->graph       = $graph;
        $this->source      = $source;
        $this->queue       = new PriorityQueue();
        $this->is_directed = $is_directed;

        /**
         * Initialize the 'distances' and 'previous' arrays, and the priority queue.
         */
        $this->queue->push($source, 0);
        foreach($graph as $origin => $vertices) {
            $this->distances[$origin] = self::INT_MAX;
            $this->previous[$origin]  = null;
            if ($origin != $source) {
                $this->queue->push($origin, self::INT_MAX);
            }
        }
        $this->distances[$source] = 0;


        /**
         * And here starts the algorithm!  :-)
         */
        $start = microtime(true);
        while (!$this->queue->isEmpty()) {
            $current = $this->queue->pop();

            $neighborsOfCurrent = $this->getNeighbors($current);

            foreach($neighborsOfCurrent as $neighbour => $distance) {
                $alt = $this->distances[$current] + $distance;      // $distance = length($current, $neighbour)
                if ($alt < $this->distances[$neighbour]) {
                    $this->distances[$neighbour] = $alt;
                    $this->previous[$neighbour] = $current;
                    $this->queue->change_priority($neighbour, $alt);
                }
            }
        }
        $this->time = round(microtime(true) - $start, 4);
    }


    /**
     * Obtain neighbors for one particular node.
     * If the graph is undirected, there is an optimization: eliminate previously visited nodes
     * from the queue.
     *
     * @param Int $origin
     * @return Void
     */
    function getNeighbors($origin)
    {
        // If a graph is directed, simply return all its neighbors.
        if ($this->is_directed) {
            return $this->graph[$origin];
        }

        // If a graph is undirected, then we can eliminate the previously eliminated nodes.
        $allNeighbors   = $this->graph[$origin];
        $validNeighbors = array();

        // Get only non-visited neighbors.
        foreach($allNeighbors as $neighborId => $distance) {
            if ($this->queue->contains($neighborId)) {
                $validNeighbors[$neighborId] = $distance;
            }
        }

        return $validNeighbors;
    }


    /**
     * Returns an array with the shortest path to a specific destination
     * with the following structure:
     * [0] => [source, 0]
     * [1] => [node 1, cost]
     * [2] => [node 2, accumulated cost]
     * ...
     * [n] => [destination, total cost]
     *
     * @param Int $destination
     * @return Array
     */
    public function shortestPathTo($destination)
    {
        $shortest_path = array();

        // Introduce destination into the shortest path array.
        $shortest_path[] = [
            'node_identifier' => $destination,
            'weight' => 0,
            'accumulated_weight' => $this->distances[$destination],
        ];

        // Select previous node and loop until source is found.
        $previous_node = $this->previous[$destination];
        while ($previous_node != $this->source) {
            // Not the source?  Push into the array, but in place [0]
            array_unshift($shortest_path, [
                'node_identifier' => $previous_node,
                'weight' => 0,
                'accumulated_weight' => $this->distances[$previous_node],
            ]);
            // Set node-to-node weight
            $shortest_path[1]['weight'] = $shortest_path[1]['accumulated_weight'] -$shortest_path[0]['accumulated_weight'];

            $previous_node = $this->previous[$previous_node];
        }

        // Source is found.  Introduce into position [0] of the result array.
        array_unshift($shortest_path, [
            'node_identifier' => $this->source,
            'weight' => 0,
            'accumulated_weight' => 0,
        ]);
        $shortest_path[1]['weight'] = $shortest_path[1]['accumulated_weight'] -$shortest_path[0]['accumulated_weight'];


        return $shortest_path;
    }


    /**
     * Return the time spent to run the algorithm (only the loop, without the initialization)
     *
     * @return Float
     */
    public function getAlgorithmTime()
    {
        return $this->time;
    }


    /**
     * Return the 'distances' array.
     *
     * @return Array
     */
    public function getDistances()
    {
        return $this->distances;
    }


    /**
     * Return the 'previous' array.
     *
     * @return Array
     */
    public function getPrevious()
    {
        return $this->previous;
    }


}
