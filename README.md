puush_server
============

emulated puush api

user page views/account management needs to be added. until this is done, auth works by creating an account on first login attempt with those details.

currently requires mod rewrite for viewing puushes

## install
* extract to a directory that puush can access
* edit install.php to your configuration
* either let it create a .htaccess or enable mod rewrite and comment the code that creates a .htaccess
* delete install.php if it works