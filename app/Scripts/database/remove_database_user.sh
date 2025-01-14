#!/bin/bash

# Parse arguments into an associative array
declare -A args
for arg in "$@"; do
  key=$(echo "$arg" | cut -d'=' -f1)
  value=$(echo "$arg" | cut -d'=' -f2)
  args[$key]=$value
done

# Check if the username argument is provided
if [ -z "${args[username]}" ]; then
  echo "Usage: $0 --username=<username>"
  exit 1
fi

USER_NAME="${args[username]}"

# Drop the database user
mysql -u root -e "DROP USER IF EXISTS '${USER_NAME}'@'localhost';"

# Check if the command succeeded
if [ $? -eq 0 ]; then
  echo "User ${USER_NAME} removed successfully."
else
  echo "Failed to remove user ${USER_NAME}."
  exit 1
fi
