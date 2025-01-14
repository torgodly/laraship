#!/bin/bash

# Parse arguments into an associative array
declare -A args
for arg in "$@"; do
  key=$(echo "$arg" | cut -d'=' -f1)
  value=$(echo "$arg" | cut -d'=' -f2)
  args[$key]=$value
done

# Check if the username and password are provided
if [ -z "${args[username]}" ] || [ -z "${args[password]}" ]; then
  echo "Usage: $0 --username=<username> --password=<password>"
  exit 1
fi

USER_NAME="${args[username]}"
USER_PASSWORD="${args[password]}"

# Create the user and grant privileges
mysql -u root -e "CREATE USER IF NOT EXISTS '${USER_NAME}'@'localhost' IDENTIFIED BY '${USER_PASSWORD}';"

# Check if the command succeeded
if [ $? -eq 0 ]; then
  echo "User ${USER_NAME} created successfully."
else
  echo "Failed to create user ${USER_NAME}."
  exit 1
fi
