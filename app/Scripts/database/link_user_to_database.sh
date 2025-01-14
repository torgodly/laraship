#!/bin/bash

# Function to display usage
usage() {
  echo "Usage: $0 --username=<username> --databases=<db1,db2,db3>"
  exit 1
}

# Parse command-line arguments
for i in "$@"; do
  case $i in
    --username=*)
      USERNAME="${i#*=}"
      shift
      ;;
    --databases=*)
      DATABASES="${i#*=}"
      shift
      ;;
    *)
      usage
      ;;
  esac
done

# Check if username and databases are provided
if [[ -z "$USERNAME" || -z "$DATABASES" ]]; then
  usage
fi

# Convert the comma-separated databases into an array
IFS=',' read -r -a DB_ARRAY <<< "$DATABASES"

# Loop through each database and grant the user access
for DB in "${DB_ARRAY[@]}"; do
  echo "Linking user '$USERNAME' to database '$DB'..."
  mysql -e "GRANT ALL PRIVILEGES ON $DB.* TO '$USERNAME'@'localhost';"
  mysql -e "FLUSH PRIVILEGES;"
  echo "User '$USERNAME' has been linked to '$DB'."
done

echo "User '$USERNAME' has been linked to all specified databases."
