# Stage 1: Build assets with node
FROM node:22 AS frontend-builder
ARG NPM_USERNAME
ARG NPM_PASSWORD

RUN echo "precedence ::ffff:0:0/96  100" >> /etc/gai.conf
ENV NODE_OPTIONS="--dns-result-order=ipv4first"

WORKDIR /app
RUN npm install -g pnpm
COPY package.json pnpm-lock.yaml pnpm-workspace.yaml* .npmrc ./

RUN if [ -n "$NPM_USERNAME" ] && [ -n "$NPM_PASSWORD" ]; then \
    AUTH=$(node -e "console.log(Buffer.from('$NPM_USERNAME:$NPM_PASSWORD').toString('base64'))"); \
    echo "//lib.external.infra.unxwares.com/api/npm/unxwares/ui-js/:_auth=${AUTH}" >> .npmrc; \
    echo "//lib.external.infra.unxwares.com/api/npm/unxwares/ui-js/:always-auth=true" >> .npmrc; \
    echo "//lib.external.infra.unxwares.com/api/npm/unxwares/ui-js/:email=ci@unxwares.com" >> .npmrc; \
    fi

RUN pnpm install --frozen-lockfile
COPY . .
RUN pnpm run build

# Stage 2: Install PHP dependencies
FROM dunglas/frankenphp:1.4-php8.4 AS php-builder
WORKDIR /app

RUN echo 'Acquire::ForceIPv4 "true";' > /etc/apt/apt.conf.d/99force-ipv4 && \
    echo "precedence ::ffff:0:0/96  100" >> /etc/gai.conf && \
    apt-get update && apt-get install -y unzip git --no-install-recommends && \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist

# Stage 3: Final image
FROM dunglas/frankenphp:1.4-php8.4 AS final
RUN echo 'Acquire::ForceIPv4 "true";' > /etc/apt/apt.conf.d/99force-ipv4 && \
    echo "precedence ::ffff:0:0/96  100" >> /etc/gai.conf

RUN install-php-extensions \
    pcntl \
    bcmath \
    gd \
    intl \
    opcache \
    pdo_pgsql \
    zip

WORKDIR /app
COPY . .
COPY --from=frontend-builder /app/public/build ./public/build
COPY --from=php-builder /app/vendor ./vendor

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/conf.d"

COPY Caddyfile /etc/caddy/Caddyfile
RUN chown -R www-data:www-data storage bootstrap/cache
RUN php artisan storage:link
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
