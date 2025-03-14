# Replace the following variables with actual values
DOMAIN="{{ $site->domain }}"
ALIASES="{{ implode(' ', $site->aliases) }}"
PHP_VERSION="{{$site->php_version}}"
EMAIL="{{Auth::user()->email}}"
WEB_DIRECTORY="{{$site->web_directory}}" # Use '/' if it's the root directory


# Ensure required directories exist
rm -rf /etc/nginx/laraship-conf/$DOMAIN
rm -rf /home/laraship/$DOMAIN

mkdir -p /etc/nginx/laraship-conf/$DOMAIN/before
mkdir -p /home/laraship/$DOMAIN/$WEB_DIRECTORY

# Step 1: Create the SSL redirection include file
cat > /etc/nginx/laraship-conf/$DOMAIN/before/ssl_redirect.conf << EOF
# Redirect every request to HTTPS...
server {
    listen 80;
    listen [::]:80;
    server_tokens off;

    server_name .$DOMAIN $ALIASES;
    return 301 https://\$host\$request_uri;
}
EOF

cat > /etc/nginx/laraship-conf/$DOMAIN/before/redirect.conf << EOF
# Redirect SSL to primary domain SSL...
server {
    http2 on;
    listen 443 ssl;
    listen [::]:443 ssl;
    server_tokens off;

    # Laraship SSL (DO NOT REMOVE!)
    ssl_certificate /etc/letsencrypt/live/$DOMAIN/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$DOMAIN/privkey.pem;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_dhparam /etc/nginx/dhparams.pem;

    server_name www.$DOMAIN.cloud;
    return 301 https://$DOMAIN\$request_uri;
}
EOF

# Step 2: Create the Nginx configuration for the site
rm -f /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/$DOMAIN

cat > /etc/nginx/sites-available/$DOMAIN << EOF
# Laraship CONFIG (DO NOT REMOVE!)
include /etc/nginx/laraship-conf/$DOMAIN/before/*;

server {
    http2 on;
    listen 443 ssl;
    listen [::]:443 ssl;
    server_name $DOMAIN $ALIASES;
    server_tokens off;
    root /home/laraship/$DOMAIN$WEB_DIRECTORY;

    # Laraship SSL (DO NOT REMOVE!)
    ssl_certificate /etc/letsencrypt/live/$DOMAIN/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$DOMAIN/privkey.pem;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_dhparam /etc/nginx/dhparams.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    charset utf-8;

    # Laraship CONFIG (DO NOT REMOVE!)
    include /etc/nginx/laraship-conf/$DOMAIN/server/*;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    access_log off;
    error_log /var/log/nginx/$DOMAIN-error.log error;

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)\$;
        fastcgi_pass unix:/var/run/php/$PHP_VERSION-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# Laraship CONFIG (DO NOT REMOVE!)
include /etc/nginx/laraship-conf/$DOMAIN/after/*;
EOF

# Step 3: Generate or Renew SSL certificates with Certbot
ALIASES_CLEAN=""
for ALIAS in $ALIASES; do
    ALIASES_CLEAN="$ALIASES_CLEAN -d $ALIAS"
done
CERT_PATH="/etc/letsencrypt/live/$DOMAIN"

# Check if the certificate already exists and is valid
if [ -d "$CERT_PATH" ] && openssl x509 -checkend 86400 -noout -in "$CERT_PATH/fullchain.pem"; then
    echo "Certificate for $DOMAIN is already valid and does not need renewal."
else
    echo "Generating or renewing SSL certificates for $DOMAIN..."
    certbot certonly --nginx --agree-tos --non-interactive -m $EMAIL -d $DOMAIN $ALIASES_CLEAN
fi

# Verify SSL certificates
if [[ ! -f "$CERT_PATH/fullchain.pem" || ! -f "$CERT_PATH/privkey.pem" ]]; then
    echo "Error: SSL certificates for $DOMAIN could not be generated."
    exit 1
fi

chmod 600 "$CERT_PATH/privkey.pem"
chmod 644 "$CERT_PATH/fullchain.pem"

# Step 4: Create default site content
su - laraship -c '
cat > /home/laraship/$DOMAIN/$WEB_DIRECTORY/index.html << EOF
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
            font-family: "Figtree", sans-serif;
        }
    </style>
</head>
<body class="antialiased">
Hello world from $DOMAIN Laraship
</body>
</html>
EOF
'
chown -R laraship:laraship /home/laraship/$DOMAIN/$WEB_DIRECTORY
chmod -R 755 /home/laraship/$DOMAIN/$WEB_DIRECTORY

# Step 5: Set up the Nginx symlink
ln -s /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/$DOMAIN

# Step 6: Reload Nginx configuration
nginx -t && systemctl reload nginx || {
echo "Error: Failed to reload Nginx configuration."
exit 1
}

# Step 7: Restart Nginx for safety
systemctl restart nginx

# Step 8: curl sites.initialize to initialize the site
sleep 5
curl -s -X GET {{route('sites.initialize', $site->uuid)}}

