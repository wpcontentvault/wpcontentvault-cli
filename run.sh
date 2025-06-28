#!/bin/bash

touch ./data/.bash_history
mkdir -p data
mkdir -p vault

docker run \
  -e "WPCONTENTVAULT_VAULT_PATH=/var/www/vault" \
  -e "WPCONTENTVAULT_DATA_PATH=/var/www/data" \
  -v "./src:/var/www" \
  -v "./data:/var/www/data" \
  -v "./vault:/var/www/vault" \
  -v "./data/.bash_history:/var/www/.bash_history" \
  --add-host=host.docker.internal:host-gateway \
  -it wpcontentvault/wpcontentvault:latest /bin/bash