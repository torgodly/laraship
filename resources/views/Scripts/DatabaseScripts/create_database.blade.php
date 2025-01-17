

# Create the database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS {{$database}};"
if [ $? -eq 0 ]; then
  echo "Database '{{$database}}' has been created."
else
  echo "Failed to create database '{{$database}}'."
    exit 1
fi

