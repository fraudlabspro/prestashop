#!/bin/bash

ROOT="$PWD"
rm fraudlabspro.zip
rm -rf /tmp/fraudlabspro
mkdir /tmp/fraudlabspro
cp -r * /tmp/fraudlabspro
cd /tmp/fraudlabspro
rm release.sh README.md
composer install
cd ..
zip -r fraudlabspro.zip fraudlabspro
mv fraudlabspro.zip $ROOT/