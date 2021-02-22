# Minotaur

# Fresh Install
After you have cloned Minotaur you will need to:
- copy .env file to laravel root directory
- run composer install
- run php artisan key:generate
- update virtual host directory setting to enable .htaccess and rewrites as below example
    <Directory /var/www/html/minotaur/public>
   	    Options Indexes FollowSymLinks
    	AllowOverride All
    	Require all granted
    </Directory>

# Create new user:
- run php artisan tinker
- run \Minotaur\User::create(['name' => 'Compete', 'email' => 'pete@davisonline.co.nz', 'password' => bcrypt('Password123')]);
