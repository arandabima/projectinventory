#!/usr/bin/env sh
set -e

if [ ! -f .env ]; then
    cp .env.example .env
fi

set_env_value() {
    key="$1"
    value="$(printenv "$key" || true)"

    if [ -z "$value" ]; then
        return
    fi

    escaped_value="$(printf '%s' "$value" | sed 's/[\/&]/\\&/g')"

    if grep -q "^${key}=" .env; then
        sed -i "s/^${key}=.*/${key}=${escaped_value}/" .env
    else
        printf '\n%s=%s\n' "$key" "$value" >> .env
    fi
}

for key in \
    APP_ENV APP_KEY APP_DEBUG APP_URL APP_TIMEZONE \
    DB_CONNECTION DB_URL DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD DB_SSLMODE DB_CHANNEL_BINDING \
    SESSION_DRIVER QUEUE_CONNECTION CACHE_STORE \
    GOOGLE_CLIENT_ID GOOGLE_CLIENT_SECRET GOOGLE_REDIRECT_URI \
    RUN_MIGRATIONS LOW_STOCK_NOTIFICATION INVENTORY_SERVICE \
    ADMIN_NAME ADMIN_USERNAME ADMIN_EMAIL ADMIN_PASSWORD
do
    set_env_value "$key"
done

php artisan config:clear >/dev/null 2>&1 || true

if [ -z "${APP_KEY:-}" ] && ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
    php artisan inventory:ensure-admin
fi

exec "$@"
