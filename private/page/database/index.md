The [pyncer/database](https://github.com/pyncerrc/pyncer-database) package
provides a standard interface for working with databases.

## Installation

Install via [Composer](https://getcomposer.org):

```bash
$ composer require pyncer/database
```

## Example

```php
use Pyncer\Database\Driver;

// Configure the database driver and get a connection.
$connection = (new Driver(
    'MySql',
    'localhost'
    'database'
    'username'
    'password'
    'prefix_' // Optional table prefix
))->getConnection();

// Select all admins from the users table
// except for admin@example.com.
$result = $connection->select('users')
    ->columns('id', 'name', 'email')
    ->where([
        'group' => 'admin',
        '!email' => 'admin@example.com'
    ])
    ->result();

foreach ($result as $row) {
    echo $row['id'] . "\n";
    echo $row['name'] . "\n";
    echo $row['email'] . "\n";
}
```
