<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
//$app->run();

$mailer = $app->getContainer()->get(Swift_Mailer::class);
$emailQuoteService = new \App\Services\EmailQuoteService($mailer);

$emailQuoteService->AMQPConsumer();