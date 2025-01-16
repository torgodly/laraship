#!/bin/bash


# Create the database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS {{$database}};"
