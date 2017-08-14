<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the "Database Connection"
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the "default" group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

// $active_group = "production";
$active_group = "postgres_dev";
$active_record = TRUE;

## Note: Make sure that we configure our Environment variables(System variables in Windows)
## Else it will choose the default variables.
## For Postgres DB
$db['postgres_dev']['hostname'] = isset($_ENV["PHINX_DB_HOST"]) ? $_ENV["PHINX_DB_HOST"]: "localhost";
$db['postgres_dev']['username'] = isset($_ENV["PHINX_DB_USER"]) ? $_ENV["PHINX_DB_USER"]: "root";
$db['postgres_dev']['password'] = isset($_ENV["PHINX_DB_PASS"]) ? $_ENV["PHINX_DB_PASS"]: "webdevel";
$db['postgres_dev']['database'] = isset($_ENV["PHINX_DB_DBNAME"]) ? $_ENV["PHINX_DB_DBNAME"]: "passwordmanager_dev";
$db['postgres_dev']['port'] = isset($_ENV["PHINX_DB_PORT"]) ? $_ENV["PHINX_DB_PORT"]: "3306";
$db['postgres_dev']['dbdriver'] = "postgre";
$db['postgres_dev']['dbprefix'] = "";
$db['postgres_dev']['pconnect'] = TRUE;
$db['postgres_dev']['db_debug'] = TRUE;
$db['postgres_dev']['cache_on'] = FALSE;
$db['postgres_dev']['cachedir'] = "";
$db['postgres_dev']['char_set'] = "utf8";

## For MySQL DB
$db['mysql_dev']['hostname'] = isset($_ENV["PHINX_MYDB_HOST"]) ? $_ENV["PHINX_MYDB_HOST"]: "localhost";
$db['mysql_dev']['username'] = isset($_ENV["PHINX_MYDB_USER"]) ? $_ENV["PHINX_MYDB_USER"]: "root";
$db['mysql_dev']['password'] = isset($_ENV["PHINX_MYDB_PASS"]) ? $_ENV["PHINX_MYDB_PASS"]: "webdevel";
$db['mysql_dev']['database'] = isset($_ENV["PHINX_MYDB_DBNAME"]) ? $_ENV["PHINX_MYDB_DBNAME"]: "passwordmanager_dev";
$db['mysql_dev']['port'] = isset($_ENV["PHINX_MYDB_PORT"]) ? $_ENV["PHINX_MYDB_PORT"]: "3306";
$db['mysql_dev']['dbdriver'] = "mysql";
$db['mysql_dev']['dbprefix'] = "";
$db['mysql_dev']['pconnect'] = TRUE;
$db['mysql_dev']['db_debug'] = TRUE;
$db['mysql_dev']['cache_on'] = FALSE;
$db['mysql_dev']['cachedir'] = "";
$db['mysql_dev']['char_set'] = "utf8";
$db['mysql_dev']['dbcollat'] = "utf8_general_ci";

## Deploy to Heroku Postgres add-ons
$db['production']['hostname'] = isset($_ENV["HEROKU_POSTGRES_DB_HOSTNAME"]) ? $_ENV["HEROKU_POSTGRES_DB_HOSTNAME"]: "localhost";
$db['production']['username'] = isset($_ENV["HEROKU_POSTGRES_DB_USERNAME"]) ? $_ENV["HEROKU_POSTGRES_DB_USERNAME"]: "root";
$db['production']['password'] = isset($_ENV["HEROKU_POSTGRES_DB_PASSWORD"]) ? $_ENV["HEROKU_POSTGRES_DB_PASSWORD"]: "webdevel";
$db['production']['database'] = isset($_ENV["HEROKU_POSTGRES_DB_DBNAME"]) ? $_ENV["HEROKU_POSTGRES_DB_DBNAME"]: "passwordmanager_prod";
$db['production']['port'] = isset($_ENV["HEROKU_POSTGRES_DB_PORT"]) ? $_ENV["HEROKU_POSTGRES_DB_PORT"]: "3306";
$db['production']['dbdriver'] = "postgre";
// $db['production']['pconnect'] = TRUE;
// $db['production']['db_debug'] = TRUE;
// $db['production']['cache_on'] = FALSE;
// $db['production']['cachedir'] = "";
// $db['production']['char_set'] = "utf8";
// $db['production']['dbcollat'] = "utf8_general_ci";


/* End of file database.php */
/* Location: ./system/application/config/database.php */
