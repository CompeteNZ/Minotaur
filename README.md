# Minotaur

# Fresh Install
- copy .env file to laravel root directory
- run composer install
- run php artisan key:generate

# Create new user:
- php artisan tinker
- \Minotaur\User::create(['name' => 'Compete', 'email' => 'pete@davisonline.co.nz', 'password' => bcrypt('Password123')]);
