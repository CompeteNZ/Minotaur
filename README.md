# Minotaur

# Fresh Install
After you have cloned Minotaur into your web root you will need to:
- copy .env file to laravel root directory
- run composer install
- run php artisan key:generate
- update web config document root to /public
- update web config to enable .htaccess and rewrites

# Create new user:
- run php artisan tinker
- run \Minotaur\User::create(['name' => 'Compete', 'email' => 'pete@davisonline.co.nz', 'password' => bcrypt('Password123')]);
