#!/bin/bash

# Check if password change is needed
mysql -u root -e "ALTER USER '${OLD_USERNAME}'@'localhost' IDENTIFIED BY '${NEW_PASSWORD}';"

# Skip renaming if the old and new usernames are the same
@if($OLD_USERNAME !== $NEW_USERNAME)
  mysql -u root -e "RENAME USER '${OLD_USERNAME}'@'localhost' TO '${NEW_USERNAME}'@'localhost';"
@endif

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
