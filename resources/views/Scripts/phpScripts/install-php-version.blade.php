@props(['php_version'])
apt-get install -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" -y --force-yes \
{{$php_version}}-fpm {{$php_version}}-cli {{$php_version}}-dev \
{{$php_version}}-pgsql {{$php_version}}-sqlite3 {{$php_version}}-gd {{$php_version}}-curl \
{{$php_version}}-imap {{$php_version}}-mysql {{$php_version}}-mbstring \
{{$php_version}}-xml {{$php_version}}-zip {{$php_version}}-bcmath {{$php_version}}-soap \
{{$php_version}}-intl {{$php_version}}-readline {{$php_version}}-gmp \
{{$php_version}}-redis {{$php_version}}-memcached {{$php_version}}-msgpack {{$php_version}}-igbinary {{$php_version}}-swoole

# Misc. PHP CLI Configuration

sudo sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/{{$php_version_number}}/cli/php.ini
sudo sed -i "s/display_errors = .*/display_errors = On/" /etc/php/{{$php_version_number}}/cli/php.ini
sudo sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/{{$php_version_number}}/cli/php.ini
sudo sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php/{{$php_version_number}}/cli/php.ini
sudo sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php/{{$php_version_number}}/cli/php.ini

# Misc. PHP FPM Configuration

sudo sed -i "s/display_errors = .*/display_errors = Off/" /etc/php/{{$php_version_number}}/fpm/php.ini

# Ensure Imagick Is Available

echo "Configuring Imagick"

apt-get install -y --force-yes libmagickwand-dev
echo "extension=imagick.so" > /etc/php/{{$php_version_number}}/mods-available/imagick.ini
yes '' | apt install {{$php_version}}-imagick


# Configure FPM Pool Settings

sed -i "s/^user = www-data/user = laraship/" /etc/php/{{$php_version_number}}/fpm/pool.d/www.conf
sed -i "s/^group = www-data/group = laraship/" /etc/php/{{$php_version_number}}/fpm/pool.d/www.conf
sed -i "s/;listen\.owner.*/listen.owner = laraship/" /etc/php/{{$php_version_number}}/fpm/pool.d/www.conf
sed -i "s/;listen\.group.*/listen.group = laraship/" /etc/php/{{$php_version_number}}/fpm/pool.d/www.conf
sed -i "s/;listen\.mode.*/listen.mode = 0666/" /etc/php/{{$php_version_number}}/fpm/pool.d/www.conf
sed -i "s/;request_terminate_timeout .*/request_terminate_timeout = 60/" /etc/php/{{$php_version_number}}/fpm/pool.d/www.conf

# Tweak Some PHP-FPM Settings

sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/{{$php_version_number}}/fpm/php.ini
sed -i "s/display_errors = .*/display_errors = Off/" /etc/php/{{$php_version_number}}/fpm/php.ini
sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/{{$php_version_number}}/fpm/php.ini
sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php/{{$php_version_number}}/fpm/php.ini
sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php/{{$php_version_number}}/fpm/php.ini

# Optimize FPM Processes

sed -i "s/^pm.max_children.*=.*/pm.max_children = 20/" /etc/php/{{$php_version_number}}/fpm/pool.d/www.conf

# Ensure Sudoers Is Up To Date

LINE="ALL=NOPASSWD: /usr/sbin/service {{$php_version}}-fpm reload"
FILE="/etc/sudoers.d/php-fpm"
grep -q -- "^laraship $LINE" "$FILE" || echo "laraship $LINE" >> "$FILE"

# Write Systemd File For Linode


if [[ $(grep --count "maxsize" /etc/logrotate.d/{{$php_version}}-fpm) == 0 ]]; then
sed -i -r "s/^(\s*)(daily|weekly|monthly|yearly)$/\1\2\n\1maxsize 100M/" /etc/logrotate.d/{{$php_version}}-fpm
else
sed -i -r "s/^(\s*)maxsize.*$/\1maxsize 100M/" /etc/logrotate.d/{{$php_version}}-fpm
fi

