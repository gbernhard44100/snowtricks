#snowtricks
===========

## Context :
This is a Symfony project I have launched on March 17, 2018, 11:07 am in order to meet the next specifications : [Specifications for project 6 Parcours developer PHP / Symfony](https://openclassrooms.com/projects/developpez-de-a-a-z-le-site-communautaire-snowtricks). The website using this code, (http://snowtricks.bernharddesign.ovh/), is online since the 6 May 2018.

## Code quality :
Link of the code analysis made by Codacy : [![Codacy Badge](https://api.codacy.com/project/badge/Grade/5e1321b940f641d692d7256b25d26719)](https://www.codacy.com/app/gbernhard44100/snowtricks?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=gbernhard44100/snowtricks&amp;utm_campaign=Badge_Grade) <- reached on the 13 May 2018

## Main Code / frameworks used for this project :
* PHP 7.0
* Symfony 3.3.2
* HTML 5
* TWIG
* CSS 3
* Bootstrap 3.7
* Yaml
* htaccess

## Install :
1. clone this Git repository in your project folder
2. Rename the file **parameters.yml.dist** by **parameters.yml** in the folder *app/config*.
3. Open the renamed file **parameters.yml** and fill the information like below : 
    
    the next parameters are used to configure the database
    * database_host: *IP address of the server where your database is*
    * database_port: null
    * database_name: *name of your database*
    * database_user: *username to connect to your database*
    * database_password: *password to connect to your database*
    
    the next parameter is to protect you from CSRF attacks :
    * secret: *a string which has to be unique for each of your application*
    
    the next parameters are used to configure the mailer to send email
    * mailer_transport: *smtp you want to use*
    * mailer_auth_mode: *authentication mode to use : plain, login, cram-md5, or null*
    * mailer_encryption: *tencryption mode to use tls, ssl, or null*
    * mailer_host: *The host to connect to when using smtp as the transport.*
    * mailer_port: *The port when using smtp as the transport. This defaults to 465 if encryption is ssl and 25 otherwise.*
    * mailer_user: *email address*
    * mailer_password: *email password*    
    
    the next parameters are used to configure the view of the HomePage and Pages describing a trick
    * tricks_per_page: *number of tricks you want in your homepage and number of additional tricks you want to load every time you click on the button "Plus de figures"* 
    * pull_up: *minimum number of tricks to display a button to bring the visitor back to the top of the *
    * comments_per_page: *number of comments you want in each page describing a trick and number of additional additional you want to load every time you click on the button "Commentaires suivants"*
 
4. Open the command terminal
5. Create the database from your symfony project by using the next code on your terminal: *php bin/console doctrine:database:create*
6. Update the database by using the next code on your terminal: *php bin/console doctrine:schema:update --force*
7. Load the content of your database by using the next code on your terminal : *php bin/console doctrine:fixtures:load* and type **y**
8. Load the compiled css and js files by using the next code on your terminal : *php bin/console assetic:dump* if you  want to use in dev mode or *php bin/console assetic:dump --env=prod* if you want to use in production
9. ENJOY!!!
