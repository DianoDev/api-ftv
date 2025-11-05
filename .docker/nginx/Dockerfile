FROM nginxinc/nginx-unprivileged:1.29-alpine3.22

USER nginx

COPY public /var/www/html/public

COPY .docker/nginx/site.conf /etc/nginx/conf.d/default.conf
COPY .docker/nginx/nginx.conf /etc/nginx/nginx.conf

WORKDIR /var/www/html

EXPOSE 80
