Sprintfwd Takehome Assignment
Table of Contents

    Introduction
    Setup
    Running Test Cases

Introduction

 A Laravel app to keep track of members of different teams and their projects.

Setup

Follow these steps to set up the project on your local machine:
Prerequisites

    Ensure you have the following software installed on your machine:

    Composer (Latest Version)
    PHP 8.1
    Laravel 10
    MySQL (Latest Version)

Installation

    Clone the repository:

    bash

`git clone https://github.com/Chsaleem31/laravel-sprintfwd-takehome.git`

Navigate to the project directory:

cd laravel-sprintfwd-takehome

Install dependencies:

`composer install`

Create a .env file

Configure the database in the .env file:

APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-database-name
DB_PASSWORD=your-database-password


Generate the application key:

`php artisan key:generate`

after generating add this key to APP_KEY in .env file

Run database migrations:

`php artisan migrate`

Serve the application:

    `php artisan serve`

    Access the application in your web browser at http://localhost:8000.

Running Test Cases

To run test cases, follow these steps:

    Ensure that the project is set up and dependencies are installed (follow the Setup instructions).

    Execute the following command to run tests:

    `php artisan test`
