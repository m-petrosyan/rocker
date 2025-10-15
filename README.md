> Testing ssr



> Run

```bash
sudo -u www-data php artisan queue:listen --tries=3 --verbose


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


> SEO

```
64%
https://www.seobility.net/en/seocheck/check/?url=https%3A%2F%2Frocker.am%2F&mode=standard
```

> deploy

```
backup 
enum types change to string
rollback steps

```

> sql import

```
mysql -u root -p rocker < mysql-rocker.sql
```