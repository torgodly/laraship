@props(['php_version'])
apt-get install -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" -y --force-yes \
{{$php_version}}-fpm {{$php_version}}-cli {{$php_version}}-dev \
{{$php_version}}-pgsql {{$php_version}}-sqlite3 {{$php_version}}-gd {{$php_version}}-curl \
{{$php_version}}-imap {{$php_version}}-mysql {{$php_version}}-mbstring \
{{$php_version}}-xml {{$php_version}}-zip {{$php_version}}-bcmath {{$php_version}}-soap \
{{$php_version}}-intl {{$php_version}}-readline {{$php_version}}-gmp \
{{$php_version}}-redis {{$php_version}}-memcached {{$php_version}}-msgpack {{$php_version}}-igbinary {{$php_version}}-swoole

yes '' | apt install {{$php_version}}-imagick
