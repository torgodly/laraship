

# List all database users
users=$(mysql -u root -e "SELECT User FROM mysql.user;" | tail -n +2)

# Print each user name
echo "$users"
