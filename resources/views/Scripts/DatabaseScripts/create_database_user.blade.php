
# Create the user and grant privileges
mysql -u root -e "CREATE USER IF NOT EXISTS '{{$username}}'@'localhost' IDENTIFIED BY '{{$password}}';"
echo "User '{{$username}}' has been created."

