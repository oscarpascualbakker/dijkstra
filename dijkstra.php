<?php
require './src/Dijkstra.php';

/**
 * Require the data for the algorithm.
 * For this I'm going to read from a CSV file with random data.
 */
$filename = 'data.csv';


/**
 * The graph structure holds 'origin', 'destination' and 'weight'. That's all we need for this algorithm.
 * We need it this way:
 * [origin1]
 *     [destination1] => weight
 *     [destination2] => weight
 * ...
 */
$graph = array();


// Open the data file for reading and construct adjacency list.
// IMPORTANT: elements of the CSV are suposed to be separated by semicolon (;).
if (($h = fopen("{$filename}", "r")) !== FALSE) {
    // Each line in the file (except the first one) is converted into an individual array that we call $data
    $i = 0;
    while (($data = fgetcsv($h, 1000, ";")) !== FALSE) {
        if ($i > 0) {
            $originID      = (int)$data[0];
            $destinationID = (int)$data[1];

            $graph[$originID][$destinationID] = $data[2];
        }
        $i++;
    }
    fclose($h);
}


// Source is the origin node we are going to search from.
$source = 5950;


// Start Dijkstra's algorithm with graph and source, indicating it is a directed graph.
$dijkstra = new Dijkstra($graph, $source, true);


// And now, let's show some results...
echo "Time of calculation: ".$dijkstra->getAlgorithmTime()." seconds.\n\n";

// Show the Distances and Previous arrays.
echo "Distances array: \n";
print_r($dijkstra->getDistances());

echo "\nPrevious array: \n";
print_r($dijkstra->getPrevious());


// And show the shortest distance to a couple of Vertices.
$destination    = 9583;
$shortest_path1 = $dijkstra->shortestPathTo($destination);
echo "\nShortest path to 9583: \n";
print_r($shortest_path1);


$destination    = 4907;
$shortest_path2 = $dijkstra->shortestPathTo($destination);
echo "\nShortest path to 4907: \n";
print_r($shortest_path2);
