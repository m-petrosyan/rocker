#!/usr/bin/env bash

# Локальный запуск стека для разработки telegram-webhook:
#   1) ngrok туннель на локальный порт приложения
#   2) обновление APP_URL в .env на свежий ngrok URL
#   3) php artisan telegraph:set-webhook
#   4) php artisan horizon (очередь в foreground)
#
# Использование:
#   ./start.sh                 # порт 80, rocker.loc на nginx
#   PORT=8000 ./start.sh       # для php artisan serve
#   HOST=rocker.loc PORT=80 ./start.sh

set -euo pipefail

cd "$(dirname "$0")"

PORT="${PORT:-80}"
HOST="${HOST:-rocker.loc}"
NGROK_API="http://127.0.0.1:4040/api/tunnels"
ENV_FILE=".env"

NGROK_PID=""
HORIZON_PID=""

cleanup() {
    echo ""
    echo "==> Останавливаю процессы..."
    [[ -n "$HORIZON_PID" ]] && kill "$HORIZON_PID" 2>/dev/null || true
    [[ -n "$NGROK_PID" ]] && kill "$NGROK_PID" 2>/dev/null || true
    wait 2>/dev/null || true
    echo "==> Готово"
}
trap cleanup EXIT INT TERM

if ! command -v ngrok >/dev/null 2>&1; then
    echo "ngrok не найден в PATH" >&2
    exit 1
fi

if pgrep -x ngrok >/dev/null 2>&1; then
    echo "==> ngrok уже запущен, использую его"
else
    echo "==> Запускаю ngrok http --host-header=${HOST} ${PORT}"
    ngrok http --host-header="${HOST}" "${PORT}" --log=stdout > storage/logs/ngrok.log 2>&1 &
    NGROK_PID=$!
fi

echo "==> Жду публичный URL от ngrok..."
NGROK_URL=""
for i in {1..30}; do
    sleep 1
    NGROK_URL=$(curl -s "$NGROK_API" | grep -oE 'https://[a-zA-Z0-9.-]+\.ngrok[a-zA-Z0-9.-]*' | head -n1 || true)
    [[ -n "$NGROK_URL" ]] && break
done

if [[ -z "$NGROK_URL" ]]; then
    echo "Не удалось получить URL от ngrok за 30 секунд" >&2
    exit 1
fi

echo "==> ngrok URL: $NGROK_URL"

echo "==> Обновляю APP_URL в $ENV_FILE"
if grep -qE '^APP_URL=' "$ENV_FILE"; then
    sed -i.bak -E "s|^APP_URL=.*|APP_URL=${NGROK_URL}|" "$ENV_FILE"
else
    echo "APP_URL=${NGROK_URL}" >> "$ENV_FILE"
fi

echo "==> php artisan config:clear"
php artisan config:clear

echo "==> php artisan telegraph:set-webhook"
php artisan telegraph:set-webhook

echo "==> php artisan horizon (Ctrl+C для остановки всего)"
php artisan horizon &
HORIZON_PID=$!

wait "$HORIZON_PID"
