The [pyncer/data](https://github.com/pyncerrc/pyncer-data) package is used to
provide a PHP object representation of your database.

## Installation

Install via [Composer](https://getcomposer.org):

```bash
$ composer require pyncer/data
```

## Models

Models represent a row of data from your database.

```php
$userModel = new UserModel([
    'name' => 'Jacob Smith',
    'email' => 'jacob@example.com'
]);
```

## Mappers

Mappers are used to map data from your database to your Models.

```php
$userMapper = new UserMapper($connection);
$userMapper->insert($userModel);
```

## MapperQueries

Mapper queries are used to filter the data returned from your database.

```php
$userMapper = new UserMapper($connection);

$userMapperQuery = new UserMapperQuery();
$userMapperQuery->setFilter(new FiltersQueryParam(
    'enabled eq true and deleted eq false'
));

$userModel = $userMaper->selectById($userId, $userMapperQuery);
```

## Validators

Validators are used to validate data from untrusted sources.

## Formatters

Formatters convert data from one format to another such as a JSON string
to an array.

## Trees

Trees are used to deal with data sets that reference itself such as a nested
category tree.
