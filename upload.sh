#!/bin/bash
sftp kyoko@130.37.53.28 <<EOT
cd /var/www/IPGAP
put convert2server.pl ./
put app/Http/routes.php app/Http/
put app/Http/Controllers/* app/Http/Controllers/
put public/js/* public/js/
put public/css/* public/css/
put public/image/* public/image/
put resources/views/includes/* resources/views/includes/
put resources/views/layouts/* resources/views/layouts/
put resources/views/pages/* resources/views/pages/
put storage/scripts/* storage/scripts
#put database/database.sqlite database/
quit
EOT
