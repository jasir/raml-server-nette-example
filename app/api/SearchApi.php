<?php

namespace App\Api;

use RamlServer\Controller;

class Executions extends Controller
{
	public function getSearch()
	{
		return $this->request->params();
	}


	public function postFetchExecutions()
	{
//		print_r($this->request);
//		exit();

		return [
			'fucked' => 'yes',
			'postParams' => $this->request->get(),
			'params' => $this->request->params(),
			'body' => $this->request->post('name'),
		];

		return $this->request->params();
	}
}