# Minotaur

# Fresh Install
After you have cloned Minotaur into your web root you will need to:
- copy .env.example to .env and update config
- run composer install
- run php artisan key:generate
- update web config document root to /public
- update web config to enable .htaccess and/or rewrites

# When new classes are introduced (e.g. class not found/exist errors)
- run composer dump-autoload

# Setup database and seed users/monitors:
- run php artisan migrate --seed

# Edit this file on github!
