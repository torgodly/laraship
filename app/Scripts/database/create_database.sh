#!/bin/bash

# Parse arguments into an associative array
declare -A args
for arg in "$@"; do
  key=$(echo "$arg" | cut -d'=' -f1)
  value=$(echo "$arg" | cut -d'=' -f2)
  args[$key]=$value
done

# Check if the database argument is provided
if [ -z "${args[database]}" ]; then
  echo "Usage: $0 --database=<database_name>"
  exit 1
fi

DATABASE_NAME="${args[database]}"

# Create the database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS ${DATABASE_NAME};"

# Check if the command succeeded
if [ $? -eq 0 ]; then
  echo "Database ${DATABASE_NAME} created successfully."
else
  echo "Failed to create database ${DATABASE_NAME}."
  exit 1
fi
