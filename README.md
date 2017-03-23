# Password app manager
A basic password app manager built using Codeigniter.    

#### Installation  
$ git clone --depth=1 git@github.com:sudogem/password_app_manager.git      


Install phinx library using Composer:   
$ curl -sS https://getcomposer.org/installer | sudo php — –install-dir=/usr/local/bin –filename=composer     <-- install Composer if it doesnt exist yet     
$ composer require robmorgan/phinx    
$ composer install         <-- install phinx library and its dependencies on /vendor    
$ php vendor/bin/phinx     <-- lets check if phinx was installed    

##### DB Migration     
If you want to create another database table, in root folder of the app, run the phinx create command it will create a migration script inside db/migrations folder:    
e.g. FOR LINUX: artheman@ub3c:/var/www/_php/password_app_manager(master)$ php vendor/bin/phinx create UserGroups     
e.g. FOR WINDOWS: $ vendor/bin/phinx create UserGroups    

#### Technology stacks   
* Codeigniter - php framework (http://codeigniter.com)
* phinx - php db migration (http://phinx.org)
* MySql - db (http://mysql.com)

#### Developer    
password_app_manager &copy;2016 Arman Ortega. Released under the MIT License.    
