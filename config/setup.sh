#Create configuration files from conf template files
echo "Creating dbconfiguration file: /config/dbconnection.php"
cp ../config/dbconnection_template.php ../config/dbconnection.php
echo "Creating Symbiota configuration file: /config/symbini.php"
cp ../config/symbini_template.php ../config/symbini.php
echo "Creating homepage: /index.php"
cp ../index_template.php ../index.php
echo "Creating header include: /header.php"
cp ../header_template.php ../header.php
echo "Creating Left Menu include: /leftmenu.php"
cp ../leftmenu_template.php ../leftmenu.php
echo "Creating footer include: /footer.php"
cp ../footer_template.php ../footer.php

#Occurrence Editor config files
echo "Creating occurrence editor default configuration file"
cp occurEditorDefaultConf_template.php occurEditorDefaultConf.php


#Adjust file permission to give write access to certain folders and files
echo "Adjusting file permissions"
chmod 777 ../webservices/dwc/rss.xml
chmod -R 777 ../temp
chmod -R 777 ../content/collicon
chmod -R 777 ../content/css
chmod -R 777 ../content/dwca
chmod -R 777 ../content/geolocate
chmod -R 777 ../content/imglib
chmod -R 777 ../content/lang
chmod -R 777 ../content/logs 
