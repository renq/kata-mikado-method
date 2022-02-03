#!/bin/bash

alias php='docker-compose run app /usr/local/bin/php'
alias phpunit='docker-compose run app bin/phpunit'
alias composer='docker-compose run app composer'
