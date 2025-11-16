# Laravel CRM


## Table of Contents

- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
- [Running the Project with Docker](#running-the-project-with-docker)
- [Running the Project Locally (Without Docker)](#running-the-project-locally-without-docker)
- [Key Commands](#key-commands)

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

Make sure you have the following software installed on your machine:

*   [Git](https://git-scm.com/)
*   [PHP](https://www.php.net/downloads.php) (>= 8.1)
*   [Composer](https://getcomposer.org/)
*   A database server (e.g., MySQL)

For the Docker setup, you only need:
*   [Docker](https://www.docker.com/products/docker-desktop)
*   [Docker Compose](https://docs.docker.com/compose/install/)

---

## Running the Project with Docker

This is the recommended way to run the project for a consistent development environment.

1.  **Clone the repository:**
    ```
    git clone (https://github.com/rajvansh-369/laravel_crm)
    cd your-repository
    ```

2.  **Create your environment file:**
    Copy the example environment file and update it as needed. The `DB_HOST` should be set to `db`, which is the name of the database service in `docker-compose.yml`.
    ```
    cp .env.example .env
    ```
    Inside your `.env` file, make sure the database connection is set up to match the Docker service:
    ```
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=laraveluser
    DB_PASSWORD=your_user_password
    ```

3.  **Build and run the Docker containers:**
    This command will build the images and start the services in the background.
    ```
    docker-compose up -d --build
    ```

4.  **Install PHP dependencies:**
    Use `docker-compose exec` to run Composer inside the `app` container.
    ```
    docker-compose exec app composer install
    ```

5.  **Generate the application key:**
    ```
    docker-compose exec app php artisan key:generate
    ```

6.  **Run database migrations:**
    This will create the necessary tables in the database.
    ```
    docker-compose exec app php artisan migrate
    ```

7.  **Access the application:**
    You can now access the application in your browser at: [http://localhost:8080](http://localhost:8080) [web:6].

---

## Running the Project Locally (Without Docker)

Follow these steps if you prefer to run the project on your local machine's native environment.

1.  **Clone the repository:**
    ```
    git clone https://github.com/your-username/your-repository.git
    cd your-repository
    ```

2.  **Install PHP dependencies:**
    ```
    composer install
    ```

3.  **Create and configure the environment file:**
    Copy the `.env.example` file to `.env`.
    ```
    cp .env.example .env
    ```
    Open the `.env` file and update your database credentials (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

4.  **Generate the application key:**
    ```
    php artisan key:generate
    ```

5.  **Run database migrations:**
    Make sure your local database server is running before executing this command.
    ```
    php artisan migrate
    ```
6.  **Start the development server:**
    ```
    php artisan serve
    ```

7.  **Access the application:**
    The application will be available at [http://localhost:8000](http://localhost:8000) [web:1, web:4].

---

## Key Commands

Here are some useful commands for managing the Docker environment.

*   **Start containers:** `docker-compose up -d`
*   **Stop containers:** `docker-compose down`
*   **Run Artisan commands:** `docker-compose exec app php artisan <command>`
*   **Run Composer commands:** `docker-compose exec app composer <command>`
*   **Access the app container's shell:** `docker-compose exec app bash`

