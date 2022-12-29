## Examples

Select chunked traversable result object:

Use this if you need to iterate over a lot of rows and are worried about
memory constraints.

```php
$result = $connection->select('users')
    ->columns('id', 'email')
    ->result(['count' => 500]); // 500 rows at a time

foreach ($result as $row) {
    echo $row['id'] . "\n";
    echo $row['email'] . "\n";

    // Note that deleting from the same table can cause rows to be skipped
    // because of future chunk limits not knowing rows were removed.
    $connection->delete('user')
        ->where(['id' => $row['id']])
        ->execute();
}
```

Select a standard result object:

This will return all matching rows.

```php
$result = $connection->select('users')
    ->columns('id', 'email')
    ->execute();

while ($row = $connection->fetch($result)) {
    // Deleting in a standard result loop is fine.
    $connection->delete('user')
        ->where(['id' => $row['id']])
        ->execute();
}
```

Select a single row:
```php
$user = $connection->select('users')
    ->where(['id' => $userId])
    ->row();
```

Select a single value:

```php
$email = $connection->select('users')
    ->columns('email')
    ->where(['id' => $userId])
    ->value();
```
