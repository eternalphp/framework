<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Console\input\Argument;
use framework\Console\input\Option;
use framework\Exception\InvalidArgumentException;
use framework\Router\Router;

class RouteList extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('route:list')
            ->setDescription('show route list.');
    }

    public function execute(Input $input, Output $output)
    {
		$data = array();
		$routes = Router::getRoutes();
		foreach($routes as $route){
			$data[] = array(
				'Method' => implode("|",$route->getMethods()),
				'URI' => $route->getFullUri(),
				'Name' => $route->getName(),
				'Action' => $route->isCallback() ? 'Closure' : $route->getNamespacePath() . '@' . $route->getAction(),
				'Middleware' => implode("|",$route->getMiddlewares())
			);
		}
		
		$output->table($data,false);
		
    }
}
