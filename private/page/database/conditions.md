Conditons offer more granularity over the rows affected by a query.

## Examples

```php
$result = $connection->select('users')
    ->columns('id', 'email')
    ->getWhere()
        ->orOpen()
        ->compare('email', 'name@example.com')
        ->compare('username', 'pyncer')
        ->orClose()
        ->compare('enabled', true)
    ->getQuery()
    ->execute();
```

There are multiple other conditional methods that can be used. See the
[conditions interface](https://github.com/pyncerrc/pyncer-database/blob/main/src/Record/ConditionsInterface.php)
for more information.

```php
$result = $connection->select('content')
    ->getWhere()
        ->not()->inArray('category_id', [1, 2, 3])
        ->inList('tags', 'php')
        ->compare('enabled', true)
    ->getQuery()
    ->execute();
```
