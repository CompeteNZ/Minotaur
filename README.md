# Minotaur

Create new user:

php artisan tinker
\Minotaur\User::create(['name' => 'Compete', 'email' => 'pete@davisonline.co.nz', 'password' => bcrypt('Password123')]);