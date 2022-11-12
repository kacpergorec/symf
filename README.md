# Symf - A Symfony 5 URL Shortener

Symf is a complete full-stack application, an URL Shortener built in Symfony 5 Framework.

The goal of this project was simply to to broaden my knowledge about core aspects of Symfony and Doctrine in my spare time.


## Main features:
  - `to be continued...`
  
 ## Requirements:
  - PHP 8 +
  - Composer
  - If you're using Docker: ****Symfony Binary****

 
 ### Installation:
  1. After cloning the repo run `composer install`
  
  2. Edit your `.env` file or create new `.env.local`:
  
   - Set up your mailer DSN in order to send verification emails. (ex. sendgrid)
   - Set up admin credentials
   - Set up database credentials if You're not using Docker

 ### Database:
   1. If you are using docker:
   
   - set up `docker-compose.yml`
   - run `docker-compose up -d`
   - by default __port 3306__ will be exposed
   
   2. If you are not using Docker set up `.env` or `.env.local`
   
   3. Run migrations `symfony console doctrine:migrations:migrate` or alternatively `php bin/console doctrine:migrations:migrate` if you're using .env variables
   
   4. You can seed the database with fixtures: `symfony console doctrine:fixtures:load` 
   
   5. If you dont want to fill dummy data but still have access to application, you can also create users and admins via commands `symf:add:admin` and `symf:add:user`
   
### Asynchronous Mailing:
  In order for the app to send emails, you also need to run the worker: `symfony console messenger:consume async`
  
### Mail expiration:
  Expired mails should be cleaned by a CRON task, using the `symf:cleanup:urls`
   
