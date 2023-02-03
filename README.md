# ![Symf](https://github.com/kacpergorec/symf/blob/main/public/assets/images/logo/symf-logo-light.png?raw=true)

Symf is an URL Shortener built with Symfony 5 framework, leveraging the power of Doctrine ORM. The goal of this project is to showcase the potential of Symfony and demonstrate its features to the web development community.

## Main Features
- User authentication and authorization with roles (user and admin)
- Real-time notifications for URL creation and expiration
- Email verification for new users with expiration
- Automatic cleanup of expired URLs
- Fast and intuitive interface with modern design
- Full integration with Docker for fast and easy deployment
- Simple Admin with EasyAdmin 4

## Requirements
- PHP 8+
- Composer
- If using Docker: Symfony Binary

## Installation
1. Clone the repo
2. Run `composer install`
3. Create a new .env.local file or edit the existing .env file
4. Set up the mailer DSN for sending verification emails (ex. SendGrid)
5. Set up the admin credentials
6. Set up the database credentials (if not using Docker)

## Database
1. If using Docker:
   - Set up the docker-compose.yml file
   - Run `docker-compose up -d`
   - By default, port 3306 will be exposed
2. If not using Docker, set up the .env or .env.local file
3. Run migrations `symfony console doctrine:migrations:migrate` or `php bin/console doctrine:migrations:migrate` if using .env variables
4. Seed the database with fixtures using `symfony console doctrine:fixtures:load`
5. Alternatively, you can create users and admins via commands `symf:add:admin` and `symf:add:user`

## Asynchronous Mailing
To send emails, run the worker using `symfony console messenger:consume async`

## Mail Expiration
Expired URLS should be cleaned by a CRON task using the command `symf:cleanup:urls`

## Presentation
[TODO]

## Contributing
If you would like to contribute to this project, please fork the repository and create a pull request. I am always happy to receive contributions and feedback!
