# News Aggregator API Case Study - Kabiru Wahab

📋 Project Overview

A comprehensive Laravel-based News Aggregation system that fetches, stores, and serves articles from multiple news sources through a unified RESTful API. The system provides advanced search features, filtering capabilities, and personalized user preferences for a tailored news experience.

⚙️ Installation & Setup
✅ 1. Clone the Repository
git clone https://github.com/your-username/news-aggregator.git
cd news-aggregator

✅ 2. Install Dependencies
composer install

✅ 3. Create Environment File
cp .env.test .env

✅ 4. Generate Application Key
php artisan key:generate

✅ 5. Run Migrations
php artisan migrate

php artisan db:seed

✅ 6. Start Local Server
php artisan serve

✅ 7. Queue Worker (if using queues for fetching)
php artisan queue:work

✅ 8. Access API Documentation / Postman
Import the Postman collection located in:
/storage/postman_collection.json

🛠️ Technologies Used
Layer	Technology
Framework	Laravel 12
Database	MySQL 8.0 (Full-text search)
Caching	Redis
Authentication	Laravel Sanctum (API Tokens)
Testing	PHPUnit
Containerization	Docker & Docker Compose
⚙️ System Architecture
1. Fetcher Pattern (Data Aggregation Layer)

Abstract BaseNewsFetcher class for shared logic (DRY Principle).

Each news source extends this class and implements its own transformation.

Built-in error handling & request logging.

API endpoints and rate limits configurable via .env.

2. Service Layer

Business logic is separated from controllers using Service Classes, promoting the Single Responsibility Principle (SRP).

3. Data Models

Article Model – Stores normalized article data

UserPreference Model – Stores user categories, sources, keywords, etc.

User Model – Handles authentication & relation with preferences

4. Request Validation

All incoming API requests are validated using custom Form Requests for security and data integrity.

🚀 Features

✅ Multi-source aggregation (NewsAPI, The Guardian, NewsDataIO)
✅ Automatic hourly synchronization using Laravel Scheduler
✅ Advanced search & filter (source, category, author, date)
✅ User-specific news feed based on saved preferences
✅ RESTful API with Resources (JSON formatting)
✅ Clean architecture (Service Layer, Controllers, Resources, Requests)

📦 How It Works
1. User registers or logs in to obtain API token (via Sanctum).
2. User selects preferred categories, sources, or keywords.
3. Preferences are stored in JSON format in the database.
4. When fetching news:
   - If no filters are provided → system applies saved preferences automatically.
   - User can override preferences with manual query filters.

🧪 Testing Strategy
Test Type	Coverage
Unit Tests	ArticleService (CRUD, filtering, syncing)
Feature Tests	API endpoints in ArticleController and AuthController
🔒 Security Considerations

✅ Input Validation using Laravel Form Requests

✅ Prevention of SQL Injection via Eloquent ORM

✅ XSS Protection with API Resources

✅ Token-Based Authentication using Sanctum

✅ Rate Limiting using Laravel throttling middleware

✅ Environment Variables for sensitive API credentials

📁 Extras

Postman Collection: Included in the /storage folder for easy API testing.