#!/bin/bash

# **********************************************
# @version 1.0.0
# **********************************************

echo php
php-fpm --version

echo php modules
php-fpm -m
