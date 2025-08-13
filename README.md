> Testing ssr

> Local run ssr

```bash
npm run ssr
```

> Run

```bash
php artisan queue:work --tries=3 --verbose
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
