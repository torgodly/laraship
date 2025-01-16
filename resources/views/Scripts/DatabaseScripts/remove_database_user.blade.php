#!/bin/bash


# Drop the database user
mysql -u root -e "DROP USER IF EXISTS '{{$USER_NAME}'@'localhost';"

