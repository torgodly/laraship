
# List all databases
databases=$(mysql -u root -e "SHOW DATABASES;" | tail -n +2)

# Print each database name
echo "$databases"
