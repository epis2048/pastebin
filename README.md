# pastebin
自用pastebin代码，不含登录、管理等功能

设置伪静态，Nginx配置文件如下：

        rewrite ^/favicon\.ico$ /favicon.ico last;
        rewrite ^/pastebin\.css$ /pastebin.css last;
        rewrite ^/(.*)$ /index.php?id=$1;

数据库结构见database.sql
