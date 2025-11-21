> Testing ssr



> Run

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R miqayelubuntupc:www-data storage public
```

> Local run ssr

```bash
npm run ssr
```

> Set commands

```
php artisan app:set-commands
```

> Diploy

```bash
 git pull && npm run build && pm2 restart rocker
```

> Server restart queue

```
sudo supervisorctl restart laravel-worker-rocker-app:*
```

ngrok http http://rocker.loc
php artisan telegraph:set-webhook

sudo nano /etc/hosts // եթե կորել ա էլի
127.0.0.1 rocker.loc

``` bash
 tail -f storage/logs/laravel-2025-11-10.log

 lnav -t storage/logs/laravel-2025-11-08.log
 space // last page
 
 
sudo -u www-data php artisan horizon

sudo -u www-data php artisan queue:listen --tries=3 --verbose
```

> SEO

```
64%
https://www.seobility.net/en/seocheck/check/?url=https%3A%2F%2Frocker.am%2F&mode=standard
```

> sql import

```
mysql -u root -p rocker < db-dumps/mysql-rocker.sql
```