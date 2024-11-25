# PHP Microservice API - for Finance Operations and Audit

## Features

- **Customer Management**: CRUD operations for customer data.
- **Account Transactions**: Deposit, withdraw, and transfer funds between customer accounts.
- **Auditing**: Audit individual or all accounts to ensure account balances are correct.
- **Security**: API token-based security to secure endpoints.

## Technology Stack

- **Language**: PHP 8.2
- **Framework**: Laravel 11.3
- **Database**: MariaDB
- **Other Tools**: Docker for containerization, Composer for dependency management, PHPUnit for testing

## Installation and Setup

To run this project locally, you will need Docker installed. The project uses Docker containers to ensure easy setup and consistent environments.

### Step-by-Step Setup

1. **Clone the Repository**

   ```bash
   git clone https://github.com/BilalMahmud12/api-microservice.git
   cd api-microservice
   ```

2. **Create Environment File**

   ```bash
   cp .env.example .env
   ```

   Update the `.env` file to match your local environment setup.

3. **Build and Start Containers**

   ```bash
   docker-compose build
   docker-compose up -d
   ```

4. **Run Migrations**

   Run the following command to set up the database:

   ```bash
   docker-compose exec app php artisan migrate
   ```

5. **Generate API Token**

   Set an API token in your `.env` file:

   ```env
   API_TOKEN=6f9d1e28-75b9-4b8a-90a3-04fa6e31c1b2
   ```

## Architecture Overview

This project follows a multi-layered architecture, segregating responsibilities across various layers:

- **Controllers**: Handle HTTP requests, input validation, and pass data to service layers.
- **Services**: Contain business logic, interacting with repositories to handle operations and DB transactions.
- **Repositories**: Interact with the database using Eloquent models, providing an abstraction layer for data retrieval and manipulation.
- **Models**: Represent the data structure in the application.
- **Middleware**: Custom middleware used to verify API tokens for authorized access.

The main layers interact as follows:

1. **Controller**: Receives requests, validates inputs, then delegates to the service.
2. **Service**: Handles business logic and ensures atomic operations and implementation of ACID principles using MariaDB database transactions.
3. **Repository**: Responsible for direct database interactions.
4. **Validator**: Handles input validation.

### Account Operations Flow

1. **Deposit/Withdraw**:

   - Controller validates the request.
   - Service handles transaction operations.
   - Repository interacts with the database for data updates.

2. **Transfer**:

   - Service ensures funds are deducted and added atomically from the accounts involved.

## API Endpoints

Here is a list of the available API endpoints with their descriptions:

### Customer Endpoints

- **GET /api/v1/customers**: Retrieve all customers (paginated).

  **Response**:

  ```json
  {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "John",
        "surname": "Doe",
        "balance": 100.00,
        "created_at": "2024-11-25T10:00:00.000000Z",
        "updated_at": "2024-11-25T10:00:00.000000Z"
      }
    ],
    "total": 50
  }
  ```

- **POST /api/v1/customers**: Create a new customer.

  **Request Body**:

  ```json
  {
    "name": "Jane",
    "surname": "Doe"
  }
  ```

  **Response**:

  ```json
  {
    "name": "Jane",
    "surname": "Doe",
    "balance": 0.0
  }
  ```

- **GET /api/v1/customers/{id}**: Retrieve a customer by ID.

- **PUT /api/v1/customers/{id}**: Update a customer's information.

- **DELETE /api/v1/customers/{id}**: Delete a customer.

### Account Endpoints

- **GET /api/v1/accounts/{id}**: Retrieve the balance of a specific customer.

  **Response**:

  ```json
  {
    "balance": 200.50
  }
  ```

- **POST /api/v1/accounts/{id}/deposit**: Deposit funds to an account.

  **Request Body**:

  ```json
  {
    "funds": 50.00
  }
  ```

- **POST /api/v1/accounts/{id}/withdraw**: Withdraw funds from an account.

- **POST /api/v1/accounts/transfer**: Transfer funds between accounts.

  **Request Body**:

  ```json
  {
    "from": 1,
    "to": 2,
    "funds": 25.00
  }
  ```

### Audit Endpoints

- **POST /api/v1/audit/all**: Audit all customer accounts.

  **Response**:

  ```json
  {
    "message": "Audit completed",
    "results": {
      "1": {
        "status": "success",
        "balance": 150.00
      },
      "2": {
        "status": "failed",
        "error": "Customer not found"
      }
    }
  }
  ```

- **POST /api/v1/audit/{id}**: Audit a single customer account.

## Security

All endpoints are secured using a static API token defined in the `.env` file. The token should be provided in the request headers as follows:

```
Authorization: Bearer YOUR_GENERATED_TOKEN
```

Requests without the correct token will receive a `401 Unauthorized` response.

## Testing

Unit and feature tests are included for all major functionalities using PHPUnit. To run the tests, use the following command:

```bash
docker-compose exec app php artisan test
```

Tests include:

- Customer management (CRUD operations)
- Account transactions (deposit, withdraw, transfer)
- Auditing functionality
