FROM wyveo/nginx-php-fpm:php74

RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
RUN apt-get update && apt-get install debsuryorg-archive-keyring net-tools curl rsync libmcrypt-dev openssh-client vim git cron dtach -y
RUN cd /opt && git clone https://github.com/esoftplay/tools.git
RUN echo "\n[program:esoftplay_tools]\ncommand=/bin/bash -c 'cd /opt/tools && git pull'\nautostart=true\npriority=20" >> /etc/supervisord.conf

RUN mkdir -p /var/www/html/master
WORKDIR /var/www/html/master

# ADD ioncube module
COPY includes/system/docker/ioncube_loader_lin_7.4.so /usr/lib/php/20190902/ioncube.so
RUN echo "zend_extension = ioncube.so" > /etc/php/7.4/mods-available/ioncube.ini && \
    ln -s /etc/php/7.4/mods-available/ioncube.ini /etc/php/7.4/fpm/conf.d/10-ioncube.ini && \
    ln -s /etc/php/7.4/mods-available/ioncube.ini /etc/php/7.4/cli/conf.d/10-ioncube.ini

# ADD mcrypt module
COPY includes/system/docker/mcrypt.so /usr/lib/php/20190902/
RUN echo "extension = mcrypt.so" > /etc/php/7.4/mods-available/mcrypt.ini && \
    ln -s /etc/php/7.4/mods-available/mcrypt.ini /etc/php/7.4/fpm/conf.d/20-mcrypt.ini && \
    ln -s /etc/php/7.4/mods-available/mcrypt.ini /etc/php/7.4/cli/conf.d/20-mcrypt.ini

# Customize PHP.ini
RUN cp /etc/php/7.4/fpm/php.ini /etc/php/7.4/fpm/php.ini.orig
RUN cat /etc/php/7.4/fpm/php.ini.orig | sed \
     -e 's/short_open_tag = Off/short_open_tag = On/g' \
     -e 's/; max_input_vars = 1000/max_input_vars = 1000000/g' \
     -e 's/default_socket_timeout = 60/; default_socket_timeout = 60/g' \
     -e 's/auto_prepend_file = \/var\/www\/html\/master\/includes\/system\/docker\/auto_prepend_file.php/auto_prepend_file =/g' \
     -e 's/auto_prepend_file =/auto_prepend_file = \/var\/www\/html\/master\/includes\/system\/docker\/auto_prepend_file.php/g' \
     -e 's/html_errors = On/html_errors = Off/g' > /etc/php/7.4/fpm/php.ini

RUN cp /etc/php/7.4/cli/php.ini /etc/php/7.4/cli/php.ini.orig
RUN cat /etc/php/7.4/cli/php.ini.orig | sed \
     -e 's/short_open_tag = Off/short_open_tag = On/g' \
     -e 's/; max_input_vars = 1000/max_input_vars = 1000000/g' \
     -e 's/default_socket_timeout = 60/; default_socket_timeout = 60/g' \
     -e 's/auto_prepend_file = \/var\/www\/html\/master\/includes\/system\/docker\/auto_prepend_file.php/auto_prepend_file =/g' \
     -e 's/auto_prepend_file =/auto_prepend_file = \/var\/www\/html\/master\/includes\/system\/docker\/auto_prepend_file.php/g' \
     -e 's/html_errors = On/html_errors = Off/g' > /etc/php/7.4/cli/php.ini

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
