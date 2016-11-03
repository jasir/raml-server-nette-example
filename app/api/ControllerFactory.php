<?php

namespace App\Api;


use Nette\DI\Container;
use RamlServer\DefaultControllerFactory;
use RamlServer\ZeroRouter;
use Slim\Http\Request;
use Slim\Http\Response;

class ControllerFactory extends DefaultControllerFactory
{
	/**
	 * @var Container
	 */
	private $container;


	/**
	 * ControllerFactory constructor.
	 * @param string $namespace
	 * @param Container $container
	 */
	public function __construct($namespace, Container $container)
	{
		parent::__construct($namespace);
		$this->container = $container;
	}

	/**
	 * @param ZeroRouter $router
	 * @param Request $request
	 * @param Response $response
	 * @param array $routeDefinition
	 * @return null
	 */
	public function create(ZeroRouter $router, Request $request, Response $response, array $routeDefinition)
	{
		$controller = parent::create($router, $request, $response, $routeDefinition);
		if ($controller) {
			$this->container->callInjects($controller);
		}
		return $controller;
	}

}