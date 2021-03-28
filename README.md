# Minotaur

# Fresh Install
After you have cloned Minotaur into your web root you will need to:
- copy .env.example to .env and update config
- run composer install
- run php artisan key:generate
- update web config document root to /public
- update web config to enable .htaccess and/or rewrites

# Setup database and seed users/monitors:
- run php artisan migrate --seed

