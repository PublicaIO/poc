#!/bin/bash

GREEN='\033[0;32m'
NC='\033[0m'

echo "${GREEN}Installing PHP packages via composer${NC}"
composer install

echo "${GREEN}Setting up database${NC}"
php artisan migrate --seed

echo "${GREEN}Installing node modules${NC}"
npm install

echo "${GREEN}Installing truffle and compiling contracts${NC}"
npm i -g truffle && truffle init && npm run compile

echo "${GREEN}Compile assets${NC}"
npm run dev
