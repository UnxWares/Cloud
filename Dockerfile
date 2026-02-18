# Stage 1: Build assets with pnpm
FROM node:22-alpine AS frontend-builder
ARG GH_ORG_PACKAGES
WORKDIR /app

# Install pnpm
RUN npm install -g pnpm

# Authenticate for private packages if secret is provided
RUN if [ -n "$GH_ORG_PACKAGES" ]; then \
    echo "//npm.pkg.github.com/:_authToken=${GH_ORG_PACKAGES}" > ~/.npmrc; \
    fi

# Copy package files
COPY package.json pnpm-lock.yaml pnpm-workspace.yaml* ./
RUN pnpm install --frozen-lockfile

# Copy source files
COPY . .
RUN pnpm run build

# Stage 2: Install PHP dependencies
FROM dunglas/frankenphp:1.4-php8.4-alpine AS php-builder
WORKDIR /app

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist

# Stage 3: Final image
FROM dunglas/frankenphp:1.4-php8.4-alpine AS final

# Standard PHP extension dependencies
RUN install-php-extensions \
    pcntl \
    bcmath \
    gd \
    intl \
    opcache \
    pdo_pgsql \
    zip

# App working directory
WORKDIR /app

# Copy the application code
COPY . .

# Copy dependencies and built assets from builders
COPY --from=frontend-builder /app/public/build ./public/build
COPY --from=php-builder /app/vendor ./vendor

# Set environment variables
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/conf.d"

# Copy Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

# Set permissions for storage and bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Create storage links
RUN php artisan storage:link

# Expose the default port
EXPOSE 8000

# Entrypoint
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
