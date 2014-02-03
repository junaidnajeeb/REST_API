REST_API
========

DEMO REST API


httpd.conf setting
========

<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule /REST_API/(.*)$ /REST_API/public_html/index.php?request=$1 [QSA,NC,L]
</IfModule>

========

config/DummyDataClass.php 
========
This contains a sql script i worte to insert dummy data

config/jtest_2014-02-01.sql
========
it was a sql dump but wasnt able to upload. 
remote: warning: File config/jtest_2014-02-01.sql is 61.44 MB; this is larger than GitHub's recommended maximum file size of 50 MB

config/schema.sql
========
Schema file

library/API.php
========
This has code i look it up online.

library/ObjectBase.php
========
This is my ObjectBase Class i use to extend other class from. I can explain what is this if needed.


library/MySQLConnection.php
========
This is just my a MySQL connection library.


library/User.php
========
User Class which contains functions to service endpoints.


library/UserAPI.php
========
UserAPI contains endpoint functions.


public_html/index.php
========
My httpd.conf point to this file to service all REST endpoints.
