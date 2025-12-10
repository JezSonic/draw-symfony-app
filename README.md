# Project Setup

This document outlines the steps to set up and run the Symfony backend and Vue.js frontend of this project.

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Docker Setup](#docker-setup)
3. [Backend Setup (Symfony)](#backend-setup-symfony)
4. [Frontend Setup (Vue.js)](#frontend-setup-vuejs)

## Prerequisites

Before you begin, ensure you have the following installed:
- Docker and Docker Compose
- Node.js and npm (or yarn)
- Copy `.env.example` files from `backend` and `frontend` to `.env` files

## Docker Setup

1.  **Build and Run Containers:**
    ```bash
    docker-compose up -d
    ```
    This command builds the Docker images (if not already built) and starts the services defined in `docker-compose.yml` in detached mode.

2.  **Verify Running Containers:**
    ```bash
    docker-compose ps
    ```
    You should see the `backend` and `nginx` services running.

## Backend Setup (Symfony)

1.  **Access the Backend Container:**
    ```bash
    docker-compose exec backend bash
    ```
    This command opens a shell inside the Symfony backend container.

2.  **Install PHP Dependencies:**
    Once inside the container, install Composer dependencies:
    ```bash
    composer install
    ```

3.  **Database Setup:**
    This application requires database to work, therefore you have to setup it vie the following commands inside the backend's container:
    -   Run migrations: `php bin/console doctrine:migrations:migrate`. In case this fails due to not found database - execute `php bin/console doctrine:database:create` first

4.  **Exit the Container:**
    ```bash
    exit
    ```

## Frontend Setup (Optional)

1.  **Navigate to the Frontend Directory:**
    ```bash
    cd frontend
    ```

2.  **Install Node.js Dependencies:**
    ```bash
    npm install
    # or yarn install
    ```

3.  **Start the Development Server:**
    ```bash
    npm run dev
    # or yarn dev
    ```
    This will start the Vue.js development server, usually accessible at `http://localhost:5173` (or similar, check the console output).

4.  **Access the Application:**
    Once both backend and frontend services are running, you can access the full application.
    -   Backend API: `http://localhost:8080` (or as configured in `backend/docker/nginx/default.conf`)
    -   Frontend Application: `http://localhost:5173` (or as defined by port mapping in `docker-compose.yaml`)
