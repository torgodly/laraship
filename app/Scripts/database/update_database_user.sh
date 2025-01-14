#!/bin/bash

# Parse arguments into an associative array
declare -A args
for arg in "$@"; do
  key=$(echo "$arg" | cut -d'=' -f1)
  value=$(echo "$arg" | cut -d'=' -f2)
  args[$key]=$value
done

# Required arguments
if [ -z "${args[old_username]}" ] || [ -z "${args[new_username]}" ] || [ -z "${args[new_password]}" ] || [ -z "${args[databases]}" ]; then
  echo "Usage: $0 --old_username=<old_username> --new_username=<new_username> --new_password=<new_password> --databases=<db1,db2,...>"
  exit 1
fi

OLD_USERNAME="${args[old_username]}"
NEW_USERNAME="${args[new_username]}"
NEW_PASSWORD="${args[new_password]}"
DATABASES="${args[databases]}"

# Check if password change is needed
CURRENT_PASSWORD=$(mysql -N -B -u root -e "SELECT authentication_string FROM mysql.user WHERE user = '${OLD_USERNAME}';")
if [ "$CURRENT_PASSWORD" != "$NEW_PASSWORD" ]; then
  mysql -u root -e "ALTER USER '${OLD_USERNAME}'@'localhost' IDENTIFIED BY '${NEW_PASSWORD}';"
fi

# Skip renaming if the old and new usernames are the same
if [ "$OLD_USERNAME" != "$NEW_USERNAME" ]; then
  mysql -u root -e "RENAME USER '${OLD_USERNAME}'@'localhost' TO '${NEW_USERNAME}'@'localhost';"
fi

# Check current database privileges
IFS=',' read -r -a DB_ARRAY <<< "$DATABASES"
for DB in "${DB_ARRAY[@]}"; do
  GRANTED=$(mysql -N -B -u root -e "SHOW GRANTS FOR '${NEW_USERNAME}'@'localhost';" | grep "ON \`${DB}\`.* TO")
  if [ -z "$GRANTED" ]; then
    mysql -u root -e "GRANT ALL PRIVILEGES ON $DB.* TO '${NEW_USERNAME}'@'localhost';"
  fi
done

# Flush privileges
mysql -u root -e "FLUSH PRIVILEGES;"

echo "User '${OLD_USERNAME}' updated successfully."
