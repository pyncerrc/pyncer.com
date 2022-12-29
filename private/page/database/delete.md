## Example

```php
$connection->delete('user')
    ->where([
        'id' => $userId,
    ])
    ->execute();
```
