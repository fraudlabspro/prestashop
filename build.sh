#!/bin/bash

composer install
cd _dev
npm i --unsafe-perm
npm i bootstrap-vue
npm add prestashop_accounts_vue_components
npm add @prestashopcorp/billing-cdc
npm run build

