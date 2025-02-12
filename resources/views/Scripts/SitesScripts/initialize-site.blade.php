# Step 1: Create the SSL redirection include file
mkdir -p /etc/nginx/laraship-conf/{{$site->domain}}/before

cat > /etc/nginx/laraship-conf/{{$site->domain}}/before/redirect-to-https.conf << EOF
server {
    listen 80;
    listen [::]:80;
    server_name {{$site->domain}} {{$site->aliases ? ' '.implode(' ', $site->aliases) : ''}};
    server_tokens off;

    # Redirect HTTP to HTTPS
    return 301 https://\$host\$request_uri;

    access_log off;
    error_log /var/log/nginx/{{$site->domain}}-error.log error;

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/{{$site->php_version}}-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Step 2: Create the Nginx configuration for the site
rm -rf /etc/nginx/sites-available/{{$site->domain}}
rm -rf /etc/nginx/sites-enabled/{{$site->domain}}

cat > /etc/nginx/sites-available/{{$site->domain}} << EOF
include laraship-conf/{{$site->domain}}/before/*;

server {
    listen 443 ssl;
    listen [::]:443 ssl;
    server_name {{$site->domain}} {{$site->aliases ? ' '.implode(' ', $site->aliases) : ''}};
    server_tokens off;
    root /home/laraship/{{$site->domain}}{{ $site->web_directory == '/' ? '' : $site->web_directory }};

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/{{$site->domain}}/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/{{$site->domain}}/privkey.pem;
    ssl_trusted_certificate /etc/letsencrypt/live/{{$site->domain}}/chain.pem;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers off;
    ssl_ciphers 'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY130';
    ssl_dhparam /etc/nginx/dhparams.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    # Laraship CONFIG (DO NOT REMOVE!)
    include laraship-conf/{{$site->domain}}/server/*;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    access_log off;
    error_log  /var/log/nginx/{{$site->domain}}-error.log error;

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/{{$site->php_version}}-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# Laraship CONFIG (DO NOT REMOVE!)
include laraship-conf/{{$site->domain}}/after/*;
EOF

# Step 3: Generate SSL certificates with Certbot (Let's Encrypt)
sudo certbot certonly --nginx -d {{$site->domain}} {{$site->aliases ? '--domains '.implode(' ', $site->aliases) : ''}} --agree-tos --non-interactive

# Check the SSL permissions and adjust if necessary
chmod 600 /etc/letsencrypt/live/{{$site->domain}}/privkey.pem
chmod 644 /etc/letsencrypt/live/{{$site->domain}}/fullchain.pem

# Step 4: Create the default site content for the site
# Ensure the web directory exists before creating content
mkdir -p /home/laraship/{{$site->domain}}{{ $site->web_directory == '/' ? '' : $site->web_directory }}

cat > /home/laraship/{{$site->domain}}{{ $site->web_directory == '/' ? '' : $site->web_directory }}/index.html << EOF
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="noindex" />

    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,600,700&display=swap" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>
<body class="antialiased">
    hello world from {{$site->domain}} laraship
</body>
</html>
EOF

# Step 5: Set up the Nginx symlink
ln -s /etc/nginx/sites-available/{{$site->domain}} /etc/nginx/sites-enabled/{{$site->domain}}

# Step 6: Reload Nginx to apply all changes
systemctl reload nginx

# Step 7: Final check for Nginx and SSL status
sudo nginx -t && sudo systemctl restart nginx
