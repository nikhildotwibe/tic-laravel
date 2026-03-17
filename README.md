# TIC Tours Laravel

A comprehensive Laravel-based tour management system designed for travel agencies to manage quotations, itineraries, user roles, and various travel-related settings.

## Features

### User Management
- User registration and authentication
- Role-based access control (RBAC)
- Permissions management
- Secure API authentication using Laravel Sanctum

### Quotations & Itineraries
- Create and manage tour quotations
- Detailed itinerary planning
- Dynamic pricing management
- PDF generation for quotations and itineraries

### Settings Management
- **Destinations & Sub-destinations**: Manage travel locations
- **Hotels**: Hotel management with amenities and room types
- **Activities**: Tour activities and experiences
- **Countries & Languages**: Geographic and language settings
- **Categories & Property Types**: Classification systems
- **Suppliers & Agents**: Business partner management
- **Currencies**: Multi-currency support
- **System Settings**: Application configuration

### Additional Features
- Media library integration for image management
- AWS S3 file storage support
- Modular architecture using Laravel Modules
- RESTful API design
- Modern frontend build with Vite

## Tech Stack

### Backend
- **Laravel 9.x**: PHP framework
- **PHP 8.0+**: Server-side scripting
- **MySQL/PostgreSQL**: Database
- **Laravel Sanctum**: API authentication
- **Laravel Modules**: Modular architecture
- **Spatie Media Library**: File management
- **MPDF**: PDF generation
- **AWS SDK**: Cloud storage

### Frontend
- **Vite**: Build tool and dev server
- **Axios**: HTTP client
- **PostCSS**: CSS processing

### Development Tools
- **Composer**: PHP dependency management
- **NPM**: Node.js package management
- **PHPUnit**: Testing framework
- **Laravel Pint**: Code style fixer

## Installation

### Prerequisites
- PHP 8.0 or higher
- Composer
- Node.js and NPM
- MySQL or PostgreSQL database

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/tic-laravel.git
   cd tic-laravel
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   ```
   Update the `.env` file with your database credentials, AWS settings, and other configuration.

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed the database (optional)**
   ```bash
   php artisan db:seed
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

## Usage

### API Endpoints

The application provides a RESTful API. Here are the main endpoint categories:

#### Authentication
- `POST /api/user/register` - User registration
- `POST /api/user/login` - User login
- `POST /api/user/logout` - User logout

#### User Management
- `GET /api/user/list` - List users
- `GET /api/user/info` - Get current user info
- `PUT /api/user/update/{id}` - Update user
- `DELETE /api/user/delete/{id}` - Delete user

#### Roles & Permissions
- `GET /api/roles` - List roles
- `POST /api/roles` - Create role
- `GET /api/permissions` - List permissions

#### Quotations & Itineraries
- `GET /api/itineraries` - List itineraries
- `POST /api/itineraries` - Create itinerary
- `POST /api/itineraries/{id}/set-pricing` - Set pricing
- `POST /api/itinerary/print/{id}` - Generate PDF

#### Settings
- `GET /api/destinations` - List destinations
- `GET /api/hotels` - List hotels
- `GET /api/activities` - List activities
- `GET /api/countries` - List countries
- And many more settings endpoints...

### Authentication
All protected endpoints require authentication using Laravel Sanctum. Include the bearer token in the Authorization header:
```
Authorization: Bearer {token}
```

## Project Structure

```
tic-laravel/
├── app/                    # Core Laravel application
│   ├── Console/           # Artisan commands
│   ├── Exceptions/        # Exception handlers
│   ├── Helpers/           # Helper functions
│   ├── Http/              # Controllers, Middleware
│   ├── Models/            # Eloquent models
│   ├── Providers/         # Service providers
│   └── Services/          # Business logic services
├── Modules/               # Modular components
│   ├── User/              # User management module
│   ├── Quotations/        # Quotations module
│   └── Settings/          # Settings module
├── config/                # Configuration files
├── database/              # Migrations, seeders, factories
├── public/                # Public assets
├── resources/             # Views, CSS, JS
├── routes/                # Route definitions
├── storage/               # File storage
├── tests/                 # Test files
└── vendor/                # Composer dependencies
```

## Modules

The application uses a modular architecture with three main modules:

### User Module
Handles user authentication, roles, permissions, and user management.

### Quotations Module
Manages tour quotations, itineraries, pricing, and PDF generation.

### Settings Module
Comprehensive settings management including destinations, hotels, activities, suppliers, and system configuration.

## Testing

Run the test suite using PHPUnit:
```bash
php artisan test
```

## Code Quality

Format code using Laravel Pint:
```bash
./vendor/bin/pint
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, please contact the development team or create an issue in the repository.