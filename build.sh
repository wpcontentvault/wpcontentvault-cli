#!/bin/bash

docker build ./docker \
  --build-arg USER_ID=$(id -u) \
  --build-arg GROUP_ID=$(id -g) \
  -t wpcontentvault/wpcontentvault:latest