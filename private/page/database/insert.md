## Example

```php
$connection->insert('user')
    ->values([
        'email' => 'name@example.com',
        'password' => '<password_hash>',
    ])
    ->execute();

$userId = $connection->insertId();
```
