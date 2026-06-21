# WayWay 🌍✈️

WayWay is an AI-powered tourism web application designed to help travelers discover destinations, obtain travel information, and generate personalized travel itineraries. The platform leverages Natural Language Processing (NLP) and recommendation systems to provide an interactive and customized travel-planning experience.

## Overview

Planning a trip often requires searching through multiple sources to find destinations, activities, and travel information. WayWay simplifies this process by integrating destination recommendations, travel information, and itinerary generation into a single platform.

Through the Waybot chatbot, users can ask travel-related questions in natural language and receive recommendations tailored to their interests and preferences.

## Key Features

### 🤖 AI Travel Assistant (Waybot)

* NLP-based chatbot for travel inquiries
* Personalized destination recommendations
* Interactive travel guidance and information

### 🗺️ Destination Discovery

* Browse tourist destinations by city
* View destination details and descriptions
* Explore recommended attractions based on user preferences

### 📅 Smart Itinerary Generator

* Automatically generates travel itineraries
* Personalized scheduling based on selected destinations
* Helps users organize their trips efficiently

### ⭐ Review System

* Users can submit reviews after visiting destinations
* Supports community-driven destination insights

### 👥 Role-Based Access

* Tourist users
* Travel agent partners

## Business Value

WayWay provides benefits for both travelers and tourism businesses:

### For Travelers

* Faster travel planning process
* Personalized recommendations
* Centralized travel information
* Convenient itinerary generation

### For Tourism Businesses

* Increased destination visibility
* Better engagement with potential visitors
* Opportunity to promote tourism packages and services

## Technology Stack

### Backend

* Laravel 12
* PHP 8+
* MySQL

### Frontend

* Tailwind CSS
* Blade Template Engine

### Artificial Intelligence

* Natural Language Processing (NLP)
* Recommendation System

### Testing & Quality Assurance

* PHPUnit Feature Testing
* API Health Monitoring
* Load Testing with Artillery
* Database Transaction-Based Testing

## Project Structure

```text
WayWay/
├── app/
├── database/
├── resources/
├── routes/
├── tests/
│   ├── Feature/
│   └── documentation/
├── api-status-check.php
├── load-test.yml
└── README.md
```

## Testing

The project includes automated testing to ensure system reliability and maintain software quality.

### Feature Testing

Run all feature tests:

```bash
php artisan test
```

### API Health Check

Verify API endpoint availability:

```bash
php api-status-check.php
```

### Load Testing

Simulate concurrent users using Artillery:

```bash
npx artillery run load-test.yml
```

## Installation

### Clone Repository

```bash
git clone <repository-url>
cd WayWay
```

### Install Dependencies

```bash
composer install
npm install
```

### Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Configure database credentials inside `.env`.

### Run Migrations

```bash
php artisan migrate
```

### Start Development Server

```bash
php artisan serve
```

## Future Development

* AI-powered travel package recommendations
* Multi-city itinerary planning
* Budget-based trip optimization
* Real-time travel information integration
* Multilingual chatbot support

