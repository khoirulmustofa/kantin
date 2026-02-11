FROM kmdocker2021/laravel-runtime:php84-n22

# Set working directory
WORKDIR /var/www/html

# 2. Copy application
COPY . .

# 3. Setup Environment
RUN cp .env.prod .env

# 4. Install PHP Dependencies
RUN composer install --no-scripts --no-dev --optimize-autoloader

# 5. Build Assets (NPM)
# We combine these to keep layers small and remove node_modules after build if not needed 
# (Note: Keep node_modules if your app needs them at runtime for some reason)
RUN npm install && \
    npm run build

# 6. Create Storage Structure & Set Permissions
# Combining these into one RUN command significantly saves disk space
RUN mkdir -p storage/app/public \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache \
             public/uploads && \
    touch storage/logs/worker.log storage/logs/laravel.log && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 storage bootstrap/cache public/uploads && \
    chmod g+s public/uploads