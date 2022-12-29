## Example

```php
$connection->alterTable('user')
    ->datetime('last_access_date_time')->null()->after('update_date_time')
    ->dropColumn('username')
    ->execute();
```
