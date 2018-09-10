<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Register database
$dbopts = parse_url(getenv('DATABASE_URL'));
$app->register(new Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider('pdo'),
               array(
                'pdo.server' => array(
                   'driver'   => 'pgsql',
                   'user' => $dbopts["user"],
                   'password' => $dbopts["pass"],
                   'host' => $dbopts["host"],
                   'port' => $dbopts["port"],
                   'dbname' => ltrim($dbopts["path"],'/')
                   )
               )
);

// Our web handlers

// Default route
$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

// 'getlocations' route
$app->get('/locations/{id}', function($id) use($app) {
  $query = 'select * from locations';
  
  if($id){
  	$query = $query . 'where id = ' . escape($id);
  }
   	
  $st = $app['pdo']->prepare($query);
  $st->execute();

  $locations = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['name']);
    $locations[] = $row;
  }

  return json_encode($locations);
});

// 'getlocations' by type route
$app->get('/locations/type/{type}', function($type) use($app) {
  $query = 'select * from locations';
  
  if($type){
  	$query = $query . 'where type = \'' . escape($type) . '\'';
  }
   	
  $st = $app['pdo']->prepare($query);
  $st->execute();

  $locations = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['name']);
    $locations[] = $row;
  }

  return json_encode($locations);
});

$app->run();
