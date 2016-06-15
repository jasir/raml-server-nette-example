<?php

require __DIR__ . '/../vendor/autoload.php';

//zero router - is this api request?

define('APPLICATION_PATH', __DIR__ . "/..");

\Tracy\Debugger::enable(true);

$uri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$options = [
	'server' => 'http://raml-server-nette-example.127.0.0.1.xip.io',
	'apiUriPart' => 'api',
	'ramlDir' => APPLICATION_PATH . '/raml',
	'ramlUriPart' => 'raml',
	'controllerNameSpace' => 'App\\Api'
];

// cache

$fileStorage = new \Nette\Caching\Storages\FileStorage(APPLICATION_PATH . '/temp');
$cache = new \Nette\Caching\Cache($fileStorage);

$router = new \RamlServer\ZeroRouter($options, $uri);
$router->setCache($cache);

//if not api url, serve some other content - typically, nette router gets in now

if ($router->isApiRequest()) {
	$router->serveApi();
	exit();
}

//or serve the raml files

if ($router->isRamlRequest()) {
	$router->serveRamlFiles();
	exit();
}

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

return $container;
