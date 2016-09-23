<?php

require __DIR__ . '/../vendor/autoload.php';

//zero router - is this api request?

define('APPLICATION_PATH', __DIR__ . '/..');

\Tracy\Debugger::enable();

$uri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$options = [
	'server' => 'http://raml-server-nette-example.127.0.0.1.xip.io',
	'apiUriPart' => 'api',
	'ramlDir' => APPLICATION_PATH . '/raml',
	'ramlUriPart' => 'raml',
	'controllerNameSpace' => 'App\\Api'
];

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();


if (isset($_SERVER['REQUEST_SCHEME'])) {

	$server = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
	$uri = $server . $_SERVER['REQUEST_URI'];

	$options = [
		'server' => $server,
		'apiUriPart' => 'api',
		'ramlDir' => APPLICATION_PATH . '/raml',
		'ramlUriPart' => 'raml',
		'controllerNameSpace' => 'App\\Api'
	];

	// cache

	$fileStorage = new \Nette\Caching\Storages\FileStorage(APPLICATION_PATH . '/temp');
	$cache = new \Nette\Caching\Cache($fileStorage, 'raml');

	$router = new \RamlServer\ZeroRouter($options, $uri);
	$router->setCache($cache);
	$router->addProcessor(new RamlServer\MockProcessorFactory(false));
	$router->addProcessor(new RamlServer\DefaultProcessorFactory());
	$router->addProcessor(new RamlServer\MockProcessorFactory(true));


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
}


$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

return $configurator->createContainer();
