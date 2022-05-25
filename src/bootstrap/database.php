<?php
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$options = data_get(config('database.connections'), config('database.default'));
$capsule->addConnection($options);
$capsule->bootEloquent();
$capsule->setAsGlobal();