# Docker hub-eko php + apache-ko irudi ofiziala irudi gisa erabilita

FROM php:8.2-apache

# web-aplikazioaren fitxategiak kontenedore barruan kopiatu

COPY . /var/www/html

# Apache web zerbitzariak erabiltzen duen  Portuaren informazioa

EXPOSE 80
