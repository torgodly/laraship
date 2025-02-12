# Replace the following variables with actual values
DOMAIN="{{$site->domain}}"
ALIASES="{{$site->aliases? implode(' ', $site->aliases) : ''}}"
PHP_VERSION="{{$site->php_version}}"
EMAIL="admin@example.com"
WEB_DIRECTORY="{{$site->web_directory}}" # Use '/' if it's the root directory


# Ensure required directories exist
mkdir -p /etc/nginx/laraship-conf/$DOMAIN/before
mkdir -p /home/laraship/$DOMAIN$WEB_DIRECTORY

# Step 1: Create the SSL redirection include file
cat > /etc/nginx/laraship-conf/$DOMAIN/before/redirect-to-https.conf << EOF
server {
listen 80;
listen [::]:80;
server_name $DOMAIN $ALIASES;
server_tokens off;

# Redirect HTTP to HTTPS
return 301 https://\$host\$request_uri;

access_log off;
error_log /var/log/nginx/$DOMAIN-error.log error;

error_page 404 /index.php;
}
EOF

# Step 2: Create the Nginx configuration for the site
rm -f /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/$DOMAIN

cat > /etc/nginx/sites-available/$DOMAIN << EOF
include laraship-conf/$DOMAIN/before/*;

server {
listen 443 ssl;
listen [::]:443 ssl;
server_name $DOMAIN $ALIASES;
server_tokens off;
root /home/laraship/$DOMAIN$WEB_DIRECTORY;

# SSL Configuration
ssl_certificate /etc/letsencrypt/live/$DOMAIN/fullchain.pem;
ssl_certificate_key /etc/letsencrypt/live/$DOMAIN/privkey.pem;

ssl_protocols TLSv1.2 TLSv1.3;
ssl_prefer_server_ciphers off;
ssl_ciphers 'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY130';

add_header X-Frame-Options "SAMEORIGIN";
add_header X-XSS-Protection "1; mode=block";
add_header X-Content-Type-Options "nosniff";

index index.html index.htm index.php;

location / {
try_files \$uri \$uri/ /index.php?\$query_string;
}

location = /favicon.ico { access_log off; log_not_found off; }
location = /robots.txt  { access_log off; log_not_found off; }

access_log /var/log/nginx/$DOMAIN-access.log;
error_log  /var/log/nginx/$DOMAIN-error.log error;

error_page 404 /index.php;

location ~ \.php$ {
fastcgi_split_path_info ^(.+\.php)(/.+)$;
fastcgi_pass unix:/var/run/php/$PHP_VERSION-fpm.sock;
fastcgi_index index.php;
include fastcgi_params;
fastcgi_param SCRIPT_FILENAME "\$document_root\$fastcgi_script_name";
}

location ~ /\.(?!well-known).* {
deny all;
}
}

include laraship-conf/$DOMAIN/after/*;
EOF

# Step 3: Generate SSL certificates with Certbot
ALIASES_CLEAN=$(echo $ALIASES | tr -s ' ' | sed 's/ / -d /g')

certbot certonly --nginx --agree-tos --non-interactive -m $EMAIL -d $DOMAIN -d $ALIASES_CLEAN

# Verify SSL certificates
if [[ ! -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" || ! -f "/etc/letsencrypt/live/$DOMAIN/privkey.pem" ]]; then
echo "Error: SSL certificates for $DOMAIN could not be generated."
exit 1
fi

chmod 600 /etc/letsencrypt/live/$DOMAIN/privkey.pem
chmod 644 /etc/letsencrypt/live/$DOMAIN/fullchain.pem

# Step 4: Create default site content
cat > /home/laraship/$DOMAIN$WEB_DIRECTORY/index.html << EOF
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="robots" content="noindex"/>

    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,600,700&display=swap"/>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>
<body class="antialiased">
Hello world from $DOMAIN Laraship
</body>
</html>
EOF

# Step 5: Set up the Nginx symlink
ln -s /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/$DOMAIN

# Step 6: Reload Nginx configuration
nginx -t && systemctl reload nginx || {
echo "Error: Failed to reload Nginx configuration."
exit 1
}

# Step 7: Restart Nginx for safety
systemctl restart nginx
