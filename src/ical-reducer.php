<?php

namespace ICalReducer;

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application('ical reducer');
$application->addCommands([new ReduceCommand()]);
$application->setDefaultCommand('reduce');
$application->run();
