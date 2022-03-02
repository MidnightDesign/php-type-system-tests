#!/usr/bin/env php
<?php

use Midnight\PhpTypeSystemTests\RunTests;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application('PHP Type System Tests');
$app->addCommands(
    [
        new RunTests('run'),
    ]
);
$app->setDefaultCommand('run');
$app->run();
