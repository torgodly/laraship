#!/bin/bash

# Drop the database
mysql -u root -e "DROP DATABASE IF EXISTS {{$DATABASE_NAME}};"

