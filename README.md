# Mini LMS API

A mini Learning Management System built with Laravel, featuring role-based access for Admins, Teachers, and Students. 

## Setup Instructions

1. Run `composer install`
2. Configure `.env` if necessary (SQLite is default)
3. Run migrations and seed the database:
   ```bash
   php artisan migrate:fresh --seed
   ```
4. Start the server:
   ```bash
   php artisan serve
   ```
 
## Docker Setup

1. Build and start the containers:
   ```bash
   docker-compose up -d --build
   ```
2. Run migrations and seed the database inside the container:
   ```bash
   docker-compose exec app php artisan migrate --seed
   ```
3. Access the API at **[http://localhost:8080](http://localhost:8080)**.
 
## Initial Seeded Users
- **Admin**: `admin@example.com` / `password123`
- **Teacher**: `teacher@example.com` / `password123`
- **Student**: `student@example.com` / `password123`
 
## Project Architecture
 
 This project follows a clean, modular architecture to ensure scalability and maintainability:
 
 - **Controllers**: Handle HTTP requests, input validation, and return standardized responses using the `ApiResponse` trait.
 - **Service Layer**: Contains core business logic (e.g., `AuthService`, `CourseService`, `QuizService`).
 - **DTOs (Data Transfer Objects)**: Encapsulate data passed between layers, ensuring type safety and structured IO.
 - **ApiResponse Trait**: Provides a consistent structure for all API responses, including a `meta` object for trace IDs:
   ```json
   {
     "success": true,
     "message": "...",
     "data": { ... },
     "meta": {
       "trace_id": "uuid-v4-trace-id"
     }
   }
   ```
 - **Observability**: Implemented via **Trace IDs** generated for every request. These IDs correlate logs and responses, making debugging seamless.
 - **Caching**: A robust caching layer using the `database` driver. Data like **Course listings** and **Analytical reports** are cached for 1 hour with automatic invalidation upon data changes (Create/Update/Delete/Enroll).

## Log Viewer & Auditing

A visual dashboard is available to monitor application activity and model changes in real-time.
- **URL**: `http://localhost:8080/logs` (or `/logs` on your local server)
- **Activity Logs**: Tracks every request, its method, IP, and associated **Trace ID**.
- **Model Tracking**: Automatic audit trails for changes in **Course** and **Quiz** models.
- **Error Correlation**: Exceptions in logs are tagged with the same Trace ID found in the API response meta.

## API Documentation (Swagger)

The project includes OpenAPI (Swagger) documentation for all endpoints.

1. Ensure your server is running (`php artisan serve`).
2. Visit the following URL in your browser:
   **[http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)**

*Note: If you run the server on a different port (e.g., 8080), update the URL to `http://localhost:8080/api/documentation`.*

---

## API Endpoints Testing (cURL)

Replace `YOUR_TOKEN_HERE` with the token received from the Login endpoint.

### 1. Authentication

**Login**
```bash
curl -X POST http://localhost:8000/api/login \
-H "Content-Type: application/json" \
-d '{"email": "admin@example.com", "password": "password123"}'
```

### 2. Admin Endpoints

**Create a Course**
```bash
curl -X POST http://localhost:8000/api/courses \
-H "Authorization: Bearer YOUR_TOKEN_HERE" \
-H "Content-Type: application/json" \
-d '{"title": "Advanced PHP", "description": "Mastering backend concepts."}'
```

**View Course Reports (Students per course)**
```bash
curl -X GET http://localhost:8000/api/reports/courses \
-H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 3. Student Endpoints

**Enroll in a Course**
*(Use Course ID 1 from the seeder)*
```bash
curl -X POST http://localhost:8000/api/courses/1/enroll \
-H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Get Enrolled Courses**
```bash
curl -X GET http://localhost:8000/api/courses/enrolled \
-H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Submit Quiz Answers**
*(Assuming Quiz 1 & Question 1 exists. Teacher must create them first.)*
```bash
curl -X POST http://localhost:8000/api/quizzes/1/submit \
-H "Authorization: Bearer YOUR_TOKEN_HERE" \
-H "Content-Type: application/json" \
-d '{"answers": {"1": "PHP: Hypertext Preprocessor"}}'
```

### 4. Teacher Endpoints

**Create a Quiz**
```bash
curl -X POST http://localhost:8000/api/courses/1/quizzes \
-H "Authorization: Bearer YOUR_TOKEN_HERE" \
-H "Content-Type: application/json" \
-d '{"title": "PHP Basics Quiz"}'
```

**Add Questions to Quiz**
```bash
curl -X POST http://localhost:8000/api/quizzes/1/questions \
-H "Authorization: Bearer YOUR_TOKEN_HERE" \
-H "Content-Type: application/json" \
-d '{"question": "What does PHP stand for?", "options": ["Python rules", "PHP: Hypertext Preprocessor"], "answer": "PHP: Hypertext Preprocessor"}'
```

**View Students in a Course**
```bash
curl -X GET http://localhost:8000/api/courses/1/students \
-H "Authorization: Bearer YOUR_TOKEN_HERE"
```
