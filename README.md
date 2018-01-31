# database

composer address:
 composer require jeanku/database:dev-master


set config at entrance file(index.php):
    \Jeanku\Database\DatabaseManager::make(WEBPATH . '/config/database.php');

database.php:
      array(
          'default' => 'database1',
          'connections' => (
              'database1' => array(
                  'driver' => 'mysql',
                  'host' => '0.0.0.0',
                  'port' => '3306',
                  'database' => 'database',
                  'username' => 'root',
                  'password' => 'root',
                  'charset' => 'utf8',
                  'collation' => 'utf8_unicode_ci',
                  'prefix' => '',
                  'strict' => false,
                  'engine' => null,
              ),
              'database2' => array(
                    'driver' => 'mysql',
                    'host' => '0.0.0.0',
                    'port' => '3306',
                    'database' => 'database2',
                    'username' => 'root',
                    'password' => 'root',
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix' => '',
                    'strict' => false,
                    'engine' => null,
              )
          )
      );
