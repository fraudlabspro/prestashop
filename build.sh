#!/bin/bash

composer install
cd _dev/apps
npm i
cd ../
npm i
npm run build