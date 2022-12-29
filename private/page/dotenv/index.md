The [pyncer/dotenv](https://github.com/pyncerrc/pyncer-dotenv) package
implements a [phpdotenv](https://github.com/vlucas/phpdotenv) adaptor to
read and write .env values to constants.

## Installation

Install via [Composer](https://getcomposer.org):

```bash
$ composer require pyncer/dotenv
```

## Example

```php
use Dotenv\Dotenv;
use Dotenv\Repository\RepositoryBuilder;
use Pyncer\Dotenv\ConstAdapter;

// ...

$repository = RepositoryBuilder::createWithNoAdapters()
    ->addAdapter(new ConstAdapter('Vendor\\Namespace'))
    ->immutable()
    ->make();

$dotenv = Dotenv::create($repository, getcwd());
$dotenv->load();

// ...

echo \Vendor\Namespace\MY_ENV_VARIABLE;
```
