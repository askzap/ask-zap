#!/bin/bash

git pull origin $1
php vendor/bin/phinx migrate