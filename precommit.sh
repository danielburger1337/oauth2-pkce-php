#!/bin/sh

vendor/bin/php-cs-fixer fix

if ! vendor/bin/phpstan --memory-limit=512M; then
    exit 1;
fi

vendor/bin/phpunit --coverage-text --path-coverage
