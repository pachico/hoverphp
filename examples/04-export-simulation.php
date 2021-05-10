<?php

use Pachico\HoverPHP\Client;
use Pachico\HoverPHP\Entity as H;
use Pachico\HoverPHP\Entity\Matcher;

require __DIR__ . '/../vendor/autoload.php';

// This is a working example. Start hoverfly with docker-compose by typing `make up` in root folder

// Let's create our client and point it to Hoverfly's location
$hClient = Client::new('http://localhost:8888');

// Enable capture mode
$hClient->setMode(Client::MODE_CAPTURE);

// Set the simulation in hoverfly
$hClient->setSimulation(
    H\Simulation::new()->withPair(
        H\Request::new()->withDestinationMatcher(Matcher::GLOB, '*'),
        H\Response::new(200, 'My awesome result')
    )
);

// Export it to JSON string
$jsonSimulation = $hClient->exportSimulation();
