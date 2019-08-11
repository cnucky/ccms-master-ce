# Cloud Computing Management System - master
CCMS主控端，受控端见：https://github.com/yzsme/ccms-slave-ce
## 部署
以下部署步骤以Debian Stretch为例。
### 安装所需程序
```
# DEB.SURY.ORG repositories
apt update
apt -y install apt-transport-https lsb-release ca-certificates
wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
apt update

apt -y install \
mariadb-server \
nginx-extras \
redis-server \
php7.2-cli \
php7.2-common \
php7.2-curl \
php7.2-dev \
php7.2-fpm \
php7.2-json \
php7.2-mbstring \
php7.2-mysql \
php7.2-opcache \
php7.2-readline \
php7.2-xml \
php7.2-zip \
php-redis \
zip \
unzip \
php-imagick \
libvirt-dev \
git \
sudo \
supervisor \
build-essential
```
### 安装libvirt-php
```
pushd /usr/src
git clone https://github.com/yzsme/libvirt-php.git
pushd ./libvirt-php
./autogen.sh \
    --with-php-config=/usr/bin/php-config7.2 \
    --without-php-confdir
make -j4 && make -j8 install
echo "extension = libvirt-php.so" > /etc/php/7.2/mods-available/libvirt-php.ini
phpenmod -v 7.2 libvirt-php
popd
popd
```
### 安装composer
见：https://getcomposer.org/download/
### 创建ccms用户
```
useradd -rm ccms
usermod -aG ccms www-data
```
### 创建ccms数据库及用户
```
# 以下三项请根据实际情况自行更改
database_name="ccms" # 数据库名
database_username="ccms" # 数据库用户名
database_password="YOUR_PASSWORD" # 数据库密码，记得更改

echo "CREATE DATABASE ${database_name}" | mysql
echo "GRANT ALL ON ${database_name}.* TO '${database_username}'@'localhost' IDENTIFIED BY '${database_password}'" | mysql
```
### 下载主控端
```
pushd /var/www/
git clone https://github.com/yzsme/ccms-master-ce.git ccms-master
chown -R ccms:ccms ./ccms-master
chmod -R 750 ./ccms-master

pushd ./ccms-master
sudo -u ccms composer.phar install --no-dev
```
### 配置主控端
```
sudo -u ccms \cp -i .env.example .env
sudo -u ccms php artisan key:generate
```

编辑.env文件，把：
```
APP_DEBUG=true
...
CACHE_DRIVER=file
SESSION_DRIVER=file
```
更改为：
```
APP_DEBUG=false
...
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```
并更改其中的数据库连接信息。

.env文件编辑完毕后，创建数据库表：
```
sudo -u ccms php artisan migrate
```
执行上一条命令，在开始会输出管理员的邮箱（统一为：root@ccms.localhost）和随机生成的密码，如错过密码，使用以下命令重置：
```
sudo -u ccms php artisan ccms:admin:reset-password 1
```

最后：
```
sudo -u ccms php artisan route:cache
popd
popd
```
### 配置PHP FPM
以下配置根据实际需求自行更改
```
cat <<EOF > /etc/php/7.2/fpm/pool.d/ccms.conf
[ccms]
user = ccms
group = ccms

listen = /run/php/ccms.sock

listen.owner = www-data
listen.group = www-data
listen.mode = 0600

pm = dynamic
pm.max_children = 64
pm.start_servers = 8
pm.min_spare_servers = 1
pm.max_spare_servers = 16
EOF

systemctl restart php7.2-fpm
```

### 配置cron
```
cat <<EOF > /etc/cron.d/ccms-master
0 * * * * ccms /usr/bin/php7.2 /var/www/ccms-master/artisan charge:start
30 * * * * ccms /usr/bin/php7.2 /var/www/ccms-master/artisan user:inactive
EOF
```

### 配置supervisor
自行根据服务器实际情况更改numprocs数目
```
cat <<EOF > /etc/supervisor/conf.d/ccms-queue.conf
[program:ccms-queue]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php7.2 /var/www/ccms-master/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=ccms
numprocs=32
redirect_stderr=true
stdout_logfile=/var/www/ccms-master/storage/logs/queue.log
EOF

supervisorctl reload
```

### 配置Nginx
以下配置根据实际需求自行更改
```
cat <<EOF > /etc/nginx/sites-enabled/ccms-master
server {
    listen 80;
    listen [::]:80;

    listen 443 ssl http2;
    listen [::]:443 ssl http2;

    # SSL必须启用
    ssl_certificate SSL证书;
    ssl_certificate_key SSL证书私钥;

    root /var/www/ccms-master/public;

    index index.php;

    server_name 域名;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/ccms.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

systemctl reload nginx
```

### 完成
若以上步骤均无误，访问管理员区域：https://域名/ccms/ ，尝试使用前面生成的用户名密码登录。

首次需访问“系统设置->一般设置”，填写“系统Host”的值，指定一个启用了SSL的域名，用于受控端访问主控端。
