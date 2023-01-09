#!/bin/bash

mkdir fraudlabspro
cp config.xml fraudlabspro/
cp fraudlabspro.php fraudlabspro/
cp logo.png fraudlabspro/
cp -r config fraudlabspro/
cp -r vendor fraudlabspro/
cp -r views fraudlabspro/
zip -q -r fraudlabspro.zip fraudlabspro/
rm -rf fraudlabspro
mv fraudlabspro.zip ../fraudlabspro-release.zip
