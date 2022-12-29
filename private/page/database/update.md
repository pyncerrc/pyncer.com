## Example

```php
$connection->update('user')
    ->values([
        'email' => 'name@example.com',
        'password' => '<password_hash>',
    ])
    ->where([
        'id' => $userId,
    ])
    ->execute();
```
