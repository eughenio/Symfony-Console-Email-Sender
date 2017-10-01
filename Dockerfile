FROM php:7-cli
MAINTAINER Eughenio Constantino <eughenio@gmail.com>

RUN apt-get update \
&& apt-get install -y git

# Install Composer w/ Prestissimo
ENV COMPOSER_VERSION 1.5.2
RUN curl -sS https://getcomposer.org/installer | php -- \
--install-dir=/usr/local/bin \
--filename=composer \
--version=$COMPOSER_VERSION \
&& composer global require --quiet hirak/prestissimo:^0.3

CMD ["/bin/bash"]