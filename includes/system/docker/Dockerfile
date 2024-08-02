FROM esoftplay/nginx-php:php74

RUN mkdir -p /var/www/html/master
WORKDIR /var/www/html/master

# Add auto_prepend_file.php
RUN rm -f /etc/auto_prepend_file.php
COPY includes/system/docker/auto_prepend_file.php /etc/auto_prepend_file.php

# Build Website
COPY . /var/www/html/master
RUN cd /var/www/html/master && git remote set-url origin https://github.com/esoftplay/master.git
RUN echo "\n[program:esoftplay]\ncommand=/bin/bash -c 'cd /var/www/html/master && git pull'\nautostart=true\npriority=15" >> /etc/supervisord.conf
COPY includes/system/docker/nginx.conf /etc/php/7.4/fpm/pool.d/www.conf
COPY includes/system/docker/php.conf /etc/php/7.4/fpm/php-fpm.conf
COPY includes/system/docker/sites.conf  /etc/nginx/conf.d/sites.conf
COPY includes/system/docker/bashrc  /root/.bashrc

# Prepare framework
RUN chmod -R 777 images
RUN rm -rf images/cache
RUN rm -f /usr/bin/edit && \
    mv includes/system/docker/rsub /usr/bin/edit && \
    chmod +x /usr/bin/edit
# Partial Composer Installation
RUN cd /var/www/html/master/modules/images && composer update && composer install


# RUN esoftplay async
# RUN cd /opt && git clone https://github.com/esoftplay/async.git && cd /opt/async && git pull && mkdir -p logs
COPY includes/system/docker/esoftplay_async  /etc/init.d/esoftplay_async
RUN chmod +x /etc/init.d/esoftplay_async && update-rc.d esoftplay_async defaults
RUN echo "\n[program:esoftplay_async]\ncommand=/bin/bash -c '/etc/init.d/esoftplay_async start' &\nautostart=true\nautorestart=true\npriority=25\nstartretries=5" >> /etc/supervisord.conf

# SET TimeZone
RUN cp /usr/share/zoneinfo/Asia/Jakarta /etc/localtime
RUN echo 'Asia/Jakarta' > /etc/timezone

# ADD empty cronjob
COPY includes/system/docker/cronjob /etc/cron.d/cronjob
RUN crontab -l | { cat /etc/cron.d/cronjob; } | crontab
RUN touch /var/log/cron.log
RUN echo "\n[program:cronjob]\ncommand=/bin/bash -c '/etc/init.d/cron start'\nautostart=true\nautorestart=true\npriority=25\nstartretries=5" >> /etc/supervisord.conf
