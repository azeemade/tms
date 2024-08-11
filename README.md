# TMS - Task Management System

This project is a RESTful API for a Task Management System built with Laravel. It provides endpoints for managing tasks, including CRUD operations, and implements authentication for secure access.

## Author

>azeem
>adenugaazeem80@gmail.com

## Features

- User authentication
- Create, Read, Update, and Delete tasks
- Task validation
- Unit and Feature tests
- API documentation

## Requirements

- PHP 8.1+
- Composer
- Laravel 11.x
- MySQL 

## Installation

1. Clone the repository:
`git clone https://github.com/your-username/task-management-api.git`
2. Navigate to the project directory:
`cd tms`
3. Install dependencies:
`composer install`
4. Copy the `.env.example` file to `.env` and configure your database:
`cp .env.example .env`
5. Generate application key:
`php artisan key:generate`
6. Run database migrations:
`php artisan migrate`
7. Start the development server:
`php artisan serve`

## API Documentation

The API documentation is available as a Postman collection. You can find the collection at the following URL:

[Postman Collection URL] : https://documenter.getpostman.com/view/12928307/2sA3s3JWvL

The collection includes details on:
- How to authenticate
- Endpoint usage
- Request/response formats
- Other relevant information

## Authentication

This API uses [Laravel Sanctum/JWT/Laravel Passport] for authentication. To access protected endpoints, you need to include a valid authentication token in the request headers.

## Testing

To run the tests, use the following command:
`php artisan test`

## API Endpoints

- POST /api/register - Register a new user
- POST /api/login - Login and receive an authentication token
- GET /api/tasks - Get all tasks for the authenticated user
- POST /api/tasks - Create a new task
- GET /api/tasks/{id} - Get a specific task
- PUT /api/tasks/{id} - Update a specific task
- DELETE /api/tasks/{id} - Delete a specific task

For detailed information on request/response formats, please refer to the Postman collection.

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)