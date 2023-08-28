#!/bin/bash

ROOT="$PWD"
mkdir /tmp/fraudlabspro
cp -r * /tmp/fraudlabspro
cd /tmp/fraudlabspro
rm release.sh README.md
composer install
cd ..
zip -r fraudlabspro.zip fraudlabspro
mv fraudlabspro.zip $ROOT/