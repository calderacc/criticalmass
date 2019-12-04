#!/bin/bash

bin/console doctrine:database:drop --force --env=test
bin/console doctrine:database:create --env=test
bin/console doctrine:schema:create --env=test

bin/console doctrine:fixtures:load -n --env=test

bin/console fos:elastica:populate --env=test