
# Loop through each database and grant the user access
@foreach($databases as $database)
    mysql -e "GRANT ALL PRIVILEGES ON {{$database}}.* TO '{{$username}}'@'164.92.167.192';"
    mysql -e "FLUSH PRIVILEGES;"
    echo "User '{{$username}}' has been linked to '{{$database}}'."
@endforeach
