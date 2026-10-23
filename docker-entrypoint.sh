#!/bin/bash
set -e

# Tự động đồng bộ cổng PORT do Render cấp
if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
    sed -i "s/:80/:$PORT/" /etc/apache2/sites-available/000-default.conf
fi

# Đảm bảo phân quyền thư mục storage
mkdir -p /var/www/html/storage/cache /var/www/html/storage/logs /var/www/html/storage/emails
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

exec "$@"
