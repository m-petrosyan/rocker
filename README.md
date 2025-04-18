> Testing ssr

```bash
npm run test ssr
```

> Run

```bash
sudo -u www-data php artisan queue:work --tries=3 --verbose
```

> Local run ssr

```bash
npm run ssr
```

> Diploy

```bash
 git pull && npm run build && pm2 restart rocker
```
