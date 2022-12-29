## Example

```php
use Pyncer\Database\Value;

$connection->createTable('user')
    ->serial('id') // An auto incrementing primary id
    ->dateTime('insert_date_time')->default(Value::NOW)
    ->dateTime('update_date_time')->null()->autoUpdate()
    ->string('username', 50)->index()
    ->string('email')->index()
    ->string('password')
    ->bool('enabled')->default(false)->index()
    ->execute();
```
