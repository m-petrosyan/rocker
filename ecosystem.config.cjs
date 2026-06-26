module.exports = {
  apps: [
    {
      name: 'horizon',
      script: 'artisan',
      args: 'horizon',
      interpreter: 'php',
      cwd: __dirname,
      watch: false,
      autorestart: true,
      max_restarts: 10,
      restart_delay: 3000,
      log_date_format: 'HH:mm:ss',
      error_file: 'storage/logs/pm2-horizon-error.log',
      out_file: 'storage/logs/pm2-horizon-out.log',
      env: {
        APP_ENV: 'local',
      },
    },
  ],
};
