server {
     listen 80 default backlog=2048;
     server_name localhost;
     root  /www/default;

     add_header Access-Control-Allow-Origin *;
     add_header Access-Control-Allow-Headers X-Requested-With,Content-Type;
     add_header Access-Control-Allow-Methods GET,POST,OPTIONS;

     location / {
        try_files $uri $uri/ /index.php?$query_string;
     }

     location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
	    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include        fastcgi_params;
     }
}