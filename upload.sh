#!/bin/bash
sftp kyoko@130.37.53.28 <<EOT
cd /var/www/IPGAP
put convert2server.pl ./
put app/Http/routes.php app/Http/
put app/Http/Controllers/* app/Http/Controllers/
put public/js/* public/js/
put resources/views/includes/* resources/views/includes/
put resources/views/pages/* resources/views/pages/
put storage/scripts/* storage/scripts
#put database/database.sqlite database/
quit
EOT
