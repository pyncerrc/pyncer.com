<?php
namespace Pyncer\Docs;

use Dotenv\Dotenv;
use Dotenv\Repository\RepositoryBuilder;
use Pyncer\App\App as PyncerApp;
use Pyncer\Dotenv\ConstAdapter;

class App extends PyncerApp
{
    protected function initialize(): void
    {
        parent::initialize();

        $repository = RepositoryBuilder::createWithNoAdapters()
            ->addAdapter(new ConstAdapter('Pyncer\\Docs'))
            ->immutable()
            ->make();

        $dotenv = Dotenv::create($repository, getcwd());
        $dotenv->load();
    }
}
