# Stage 1: Build assets with node
FROM node:22 AS frontend-builder
ARG NPM_USERNAME
ARG NPM_PASSWORD

# Force IPv4 priority at the system level for the resolver
RUN echo "precedence ::ffff:0:0/96  100" >> /etc/gai.conf

# Force IPv4 priority for Node.js
ENV NODE_OPTIONS="--dns-result-order=ipv4first"

WORKDIR /app

# Install pnpm
RUN npm install -g pnpm

# Copy package files and .npmrc
COPY package.json pnpm-lock.yaml pnpm-workspace.yaml* .npmrc ./

# Authenticate for private packages (Repoflow)
RUN if [ -n "$NPM_USERNAME" ] && [ -n "$NPM_PASSWORD" ]; then  \
    AUTH=$(node -e "console.log(Buffer.from('$NPM_USERNAME:$NPM_PASSWORD').toString('base64'))");  \
    echo "//lib.external.infra.unxwares.com/api/npm/unxwares/ui-js/:_auth=${AUTH}" >> .npmrc;  \
    echo "//lib.external.infra.unxwares.com/api/npm/unxwares/ui-js/:always-auth=true" >> .npmrc;  \
    echo "//lib.external.infra.unxwares.com/api/npm/unxwares/ui-js/:email=ci@unxwares.com" >> .npmrc;  \
    fi

# Install dependencies
RUN pnpm install --frozen-lockfile

# Copy source files
COPY . .
RUN pnpm run build

# Stage 2: Install PHP dependencies
FROM dunglas/frankenphp:1.4-php8.4 AS php-builder
WORKDIR /app

# Force IPv4 et installe les outils de décompression + git
RUN echo 'Acquire::ForceIPv4 "true";' > /etc/apt/apt.conf.d/99force-ipv4 && 
    echo "precedence ::ffff:0:0/96  100" >> /etc/gai.conf &&  \
    apt-get update && apt-get install -y unzip git --no-install-recommends &&  \
    rm -rf /var/lib/apt/lists/* \

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist

# Stage 3: Final image
FROM dunglas/frankenphp:1.4-php8.4 AS final

# Force IPv4
RUN echo 'Acquire::ForceIPv4 "true";' > /etc/apt/apt.conf.d/99force-ipv4 && 
    echo "precedence ::ffff:0:0/96  100" >> /etc/gai.conf \

# Standard PHP extension dependencies
RUN install-php-extensions 
    pcntl  \
    bcmath  \
    gd  \
    intl  \
    opcache  \
    pdo_pgsql  \
    zip \

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

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose the default port
EXPOSE 8000

# Entrypoint
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
