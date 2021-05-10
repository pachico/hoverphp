<?php

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Pachico\HoverPHP\Client;
use Pachico\HoverPHP\Entity as H;

require __DIR__ . '/../vendor/autoload.php';

// This is a working example. Start hoverfly with docker-compose by typing `make up` in root folder

// Let's create our client and point it to Hoverfly's location
$hClient = Client::new('http://localhost:8888');

// Enable simulation mode
$hClient->setMode(Client::MODE_SIMULATION);

// To create simulations, we can pass PSR7 requests and responses

// Create a request
$psr7Request = new Request(
    'GET',
    'https://packagist.org/statistics.json',
    ['Content-Type' => 'application/json']
);

// Create a response
$psr7Response = new Response(
    200,
    ['Content-Type' => 'application/json'],
    '{"totals":{"downloads":38005616171,"packages":307392,"versions":2837544}}'
);

// Set the simulation in hoverfly
// The request will be matched also with a variation (any method)
$hClient->setSimulation(
    H\Simulation::new()->withPair(
        H\Request::fromPSR7($psr7Request)->withMethodMatcher(H\Matcher::GLOB, '*'),
        H\Response::fromPSR7($psr7Response)
    )
);

// Do your awesome integration testing
// ...
// Delete the simulations

$hClient->deleteSimulation();
