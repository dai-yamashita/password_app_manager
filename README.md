# Password app manager
A basic password app manager built using Codeigniter.    

### Installation
$ git clone --depth=1 git@github.com:sudogem/password_app_manager.git      

### Requirements
| Stacks         | Supported versions           |
| ---------------|------------------------------|
| PHP            | 5.5.38-Win32-VC11-x64<br>    |
| MySQL          | mysql  Ver 14.14 Distrib 5.7.19, for Linux (x86_64) using  EditLine wrapper      |
| PostgreSQL     | psql (PostgreSQL) 9.6.3, Heroku Postgres(9.6.1)   |

Install phinx library using Composer:   
For Linux:    
$ curl -sS https://getcomposer.org/installer | sudo php — –install-dir=/usr/local/bin –filename=composer     <-- install Composer if it doesnt exist yet     
$ composer install         <-- it will read the composer.json and install phinx library and its dependencies on /vendor    
$ php vendor/bin/phinx     <-- lets check if phinx was installed    

For Windows:    
Download and run [Composer-Setup](https://getcomposer.org/Composer-Setup.exe)     

### DB Migration
##### Executing migrate command
(In Windows) $ vendor/bin/phinx migrate -e development    
(In Linux) $ php vendor/bin/phinx migrate -e development    

##### Executing seeds data
(In Windows) $ vendor/bin/phinx seed:run -v      
(In Linux) $ php vendor/bin/phinx seed:run -v      

If you want to create another database table, in root folder of the app, use the "phinx create" command and it will create a migration script inside the db/migrations folder:    
(In Windows) $ vendor/bin/phinx create UserGroups     
(In Linux) $ php vendor/bin/phinx create UserGroups    

#### Technology stacks
* Codeigniter - php framework (http://codeigniter.com)
* phinx - php db migration (http://phinx.org)
* MySql - db (http://mysql.com)

#### Screenshots
![Homepage](/screenshots/homepage.png)   

#### Developer
password_app_manager &copy;2016 Arman Ortega. Released under the MIT License.    
