
FROM mysql:8.0.24

ENV DEBIAN_FRONTEND noninteractive

RUN set -xe \
    RUN apt-key adv --keyserver pgp.mit.edu --recv-keys A8D3785C && gpg --export "A8D3785C" > "/etc/apt/trusted.gpg.d/mysql.gpg" \
    && apt-get update --allow-insecure-repositories \
    && apt-get install -y locales \
    && rm -rf /var/lib/apt/lists/* \
    && echo 'en_IN.UTF-8 UTF-8' > /etc/locale.gen \
    && locale-gen en_IN.UTF-8

ENV LANG en_IN.UTF-8
ENV LC_ALL en_IN.UTF-8

COPY character-set.cnf /etc/mysql/conf.d/
