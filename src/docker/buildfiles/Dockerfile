FROM php:8.1-fpm-alpine AS base

LABEL maintainer="hello@waldhacker.dev"

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY ./ /

ENV VERSION=1.0.0 \
    TZ=Etc/UTC \
    LANG=en_US.UTF-8 \
    LANGUAGE=en_US.UTF-8 \
    LC_ALL=en_US.UTF-8 \
    TEXTDOMAINDIR=/home/ada/locale

RUN apk -U update \
    && apk add --no-cache \
        bash \
        ca-certificates \
        dialog \
        gettext \
        nginx \
        runit \
        shadow \
        sudo \
        supervisor \
        tzdata \
    \
    \
    && update-ca-certificates \
    \
    \
    && addgroup -g 61042 ada \
    && adduser --uid 61042 --shell /bin/bash --home /home/ada/ --disabled-password --ingroup ada ada \
    && echo 'ada ALL= NOPASSWD:SETENV: /usr/local/etc/ada/entrypoint_unix, /usr/local/etc/ada/entrypoint_win' >> /etc/sudoers \
    \
    && delgroup ping \
    \
    \
    && rm -rf /etc/nginx/http.d/default.conf \
    && sed -i -e 's/user nginx/user ada/g' /etc/nginx/nginx.conf \
    \
    \
    && chmod 750 /usr/local/bin/ada-php-configure-fpm \
    && chown root:root /usr/local/bin/ada-php-configure-fpm \
    && /usr/local/bin/ada-php-configure-fpm \
    && rm -f /usr/local/bin/ada-php-configure-fpm \
    \
    \
    && cp /usr/share/zoneinfo/Etc/UTC /etc/localtime \
    && echo "Etc/UTC" > /etc/timezone \
    \
    && msgfmt -o /home/ada/locale/de/LC_MESSAGES/ada-setup.mo /home/ada/locale/de/LC_MESSAGES/ada-setup.po \
    && msgfmt -o /home/ada/locale/en/LC_MESSAGES/ada-setup.mo /home/ada/locale/en/LC_MESSAGES/ada-setup.po \
    && find /home/ada/locale/ -type f -name "*.po" -print0 | xargs -0 -I {} /bin/rm -f "{}" \
    \
    \
    && chmod 550 /etc/nginx \
    && chmod 550 /etc/nginx/http.d \
    && chmod 440 /etc/nginx/http.d/* \
    && chmod 550 /etc/nginx/include \
    && chmod 440 /etc/nginx/include/* \
    && chmod 550 /etc/nginx/modules \
    && chmod 440 /etc/nginx/modules/* \
    \
    && chmod 550 /etc/supervisor.d/ \
    && chown -R root:root /etc/supervisor.d/ \
    && chmod 440 /etc/supervisor.d/*.ini \
    && chown root:root /etc/supervisor.d/*.ini \
    \
    && chmod 770 /home/ada/ \
    && chown -R ada:ada /home/ada/ \
    \
    && chmod 770 /opt/ada/ \
    && chown -R ada:ada /opt/ada/ \
    \
    && chmod 700 /run/nginx \
    && chown root:root /run/nginx \
    \
    && chmod 555 /usr/local/bin/entrypoint \
    && chown root:root /usr/local/bin/entrypoint \
    && chmod 555 /usr/local/bin/adai \
    && chown root:root /usr/local/bin/adai \
    && chmod 555 /usr/local/bin/ada-setup \
    && chown root:root /usr/local/bin/ada-setup \
    && chmod 555 /usr/local/bin/ada-translate \
    && chown root:root /usr/local/bin/ada-translate \
    \
    && chmod 444 /usr/local/etc/php/php.ini \
    \
    && chmod -R 550 /usr/local/etc/ada/entrypoint.d/ \
    && chown -R root:root /usr/local/etc/ada/entrypoint.d/ \
    && chmod 550 /usr/local/etc/ada/entrypoint.d/*.sh \
    && chown root:root /usr/local/etc/ada/entrypoint.d/*.sh \
    \
    && chmod -R 555 /usr/local/etc/ada/adai.d/ \
    && chown -R root:root /usr/local/etc/ada/adai.d/ \
    && chmod 555 /usr/local/etc/ada/adai.d/*.sh \
    && chown root:root /usr/local/etc/ada/adai.d/*.sh \
    \
    && chmod 550 /usr/local/etc/ada/entrypoint_unix \
    && chown root:root /usr/local/etc/ada/entrypoint_unix \
    && chmod 550 /usr/local/etc/ada/entrypoint_win \
    && chown root:root /usr/local/etc/ada/entrypoint_win \
    \
    \
    && find /usr/lib/python3.10/ -type d -name "__pycache__" -print0 | xargs -0 -I {} /bin/rm -rf "{}" \
    && rm -rf /usr/lib/python3.10/__pycache__ \
    && rm -rf /usr/lib/python3.10/site-packages/supervisor/tests \
    \
    && rm -rf /tmp/* \
    \
    && rm -rf /var/cache/apk/

VOLUME ["/opt/ada/app/"]

WORKDIR /opt/ada/app/

EXPOSE 8041

ENTRYPOINT ["/usr/local/bin/entrypoint"]

#### DEVELOPMENT STAGE ####
FROM base as development

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions xdebug \
    && sed -i -e 's/zend_extension/;zend_extension/g' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && chmod 777 /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    \
    \
    && chmod 777 /tmp

USER ada

#### PRODUCTION STAGE ####
FROM base AS production

RUN rm -f /bin/tar \
    \
    && rm -rf /lib/apk/db/ \
    \
    && rm -f /sbin/apk \
    && rm -f /sbin/chpst \
    && rm -f /sbin/chpasswd \
    && rm -f /sbin/faillock \
    && rm -f /sbin/ldconfig \
    && rm -f /sbin/mkhomedir_helper \
    && rm -f /sbin/mkmntdirs \
    && rm -f /sbin/pam_namespace_helper \
    && rm -f /sbin/pam_timestamp_check \
    && rm -f /sbin/pwhistory_helper \
    && rm -f /sbin/runit \
    && rm -f /sbin/runit-init \
    && rm -f /sbin/runsv \
    && rm -f /sbin/runsvchdir \
    && rm -f /sbin/runsvdir \
    && rm -f /sbin/sv \
    && rm -f /sbin/svlogd \
    && rm -f /sbin/unix_chkpwd \
    && rm -f /sbin/unix_update \
    && rm -f /sbin/utmpset \
    \
    && rm -f /usr/bin/2to3 \
    && rm -f /usr/bin/2to3-3.10 \
    && rm -f /usr/bin/autopoint \
    && rm -f /usr/bin/chage \
    && rm -f /usr/bin/chfn \
    && rm -f /usr/bin/chsh \
    && rm -f /usr/bin/composer \
    && rm -f /usr/bin/c_rehash \
    && rm -f /usr/bin/curl \
    && rm -f /usr/bin/cvtsudoers \
    && rm -f /usr/bin/echo_supervisord_conf \
    && rm -f /usr/bin/expiry \
    && rm -f /usr/bin/getconf \
    && rm -f /usr/bin/gnu-iconv \
    && rm -f /usr/bin/gpasswd \
    && rm -f /usr/bin/groups \
    && rm -f /usr/bin/iconv \
    && rm -f /usr/bin/ldd \
    && rm -f /usr/bin/lzcmp \
    && rm -f /usr/bin/lzdiff \
    && rm -f /usr/bin/lzegrep \
    && rm -f /usr/bin/lzfgrep \
    && rm -f /usr/bin/lzgrep \
    && rm -f /usr/bin/lzless \
    && rm -f /usr/bin/lzmadec \
    && rm -f /usr/bin/lzmainfo \
    && rm -f /usr/bin/lzmore \
    && rm -f /usr/bin/openssl \
    && rm -f /usr/bin/passwd \
    && rm -f /usr/bin/pidproxy \
    && rm -f /usr/bin/pinentry \
    && rm -f /usr/bin/posixtz \
    && rm -f /usr/bin/pydoc3 \
    && rm -f /usr/bin/pydoc3.10 \
    && rm -f /usr/bin/scanelf \
    && rm -f /usr/bin/ssl_client \
    && rm -f /usr/bin/sudoedit \
    && rm -f /usr/bin/sudoreplay \
    && rm -f /usr/bin/supervisorctl \
    && rm -f /usr/bin/tree \
    && rm -f /usr/bin/xmlwf \
    && rm -f /usr/bin/xz \
    && rm -f /usr/bin/xzcmp \
    && rm -f /usr/bin/xzdec \
    && rm -f /usr/bin/xzdiff \
    && rm -f /usr/bin/xzegrep \
    && rm -f /usr/bin/xzfgrep \
    && rm -f /usr/bin/xzgrep \
    && rm -f /usr/bin/xzless \
    && rm -f /usr/bin/xzmore \
    \
    && rm -f /usr/local/bin/docker-php-* \
    && rm -f /usr/local/bin/install-php-extensions \
    && rm -f /usr/local/bin/pear* \
    && rm -f /usr/local/bin/pecl \
    && rm -f /usr/local/bin/phar* \
    && rm -f /usr/local/bin/php \
    && rm -f /usr/local/bin/php-config \
    && rm -f /usr/local/bin/phpize \
    \
    && rm -rf /usr/local/include/php/ \
    && rm -rf /usr/local/lib/php/Archive/ \
    && rm -rf /usr/local/lib/php/build/ \
    && rm -rf /usr/local/lib/php/Console/ \
    && rm -rf /usr/local/lib/php/data/ \
    && rm -rf /usr/local/lib/php/doc/ \
    && rm -rf /usr/local/lib/php/OS/ \
    && rm -rf /usr/local/lib/php/PEAR/ \
    && rm -f /usr/local/lib/php/PEAR.php \
    && rm -f /usr/local/lib/php/pearcmd.php \
    && rm -f /usr/local/lib/php/peclcmd.php \
    && rm -rf /usr/local/lib/php/Structures/ \
    && rm -f /usr/local/lib/php/System.php \
    && rm -rf /usr/local/lib/php/test/ \
    && rm -rf /usr/local/lib/php/XML/ \
    && rm -rf /usr/local/php/man/ \
    \
    && rm -f /usr/sbin/chgpasswd \
    && rm -f /usr/sbin/chpasswd \
    && rm -f /usr/sbin/groupadd \
    && rm -f /usr/sbin/groupdel \
    && rm -f /usr/sbin/groupmems \
    && rm -f /usr/sbin/grpck \
    && rm -f /usr/sbin/logoutd \
    && rm -f /usr/sbin/newusers \
    && rm -f /usr/sbin/pwck \
    && rm -f /usr/sbin/sudo_logsrvd \
    && rm -f /usr/sbin/sudo_sendlog \
    && rm -f /usr/sbin/update-ca-certificates \
    && rm -f /usr/sbin/useradd \
    && rm -f /usr/sbin/userdel \
    && rm -f /usr/sbin/vigr \
    && rm -f /usr/sbin/vipw \
    && rm -f /usr/sbin/visudo \
    && rm -f /usr/sbin/zdump \
    && rm -f /usr/sbin/zic \
    \
    && rm -rf /usr/src/

USER ada
