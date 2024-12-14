# LaraShip 🚢

Set sail with Laravel! LaraShip is the ultimate Docker environment generator for Laravel projects. It helps you quickly set up a **production-ready Docker environment** with zero configuration.

![Version](https://img.shields.io/npm/v/laraship)
![Downloads](https://img.shields.io/npm/dt/laraship)
![License](https://img.shields.io/npm/l/laraship)


---

## ✨ Features

### 🐳 One-Command Setup
With a single command, you’re ready to sail:
```bash
npx laraship
```
LaraShip will automatically:
- Detect your PHP version and extensions
- Configure your database
- Set up Redis (if needed)
- Handle SSL certificates
- Configure backups
- Set up GitHub Actions

### 🔹 Zero Configuration Needed
- Auto-detects project requirements
- Applies sensible production defaults
- Allows customization when needed

### 🚀 Production-Ready
- Optimized `Dockerfile`
- Secure database configuration
- Built-in health checks
- SSL/TLS support
- Backup solutions included
- Monitoring integrations

---

## 📦 Installation

### Global Installation (Recommended)
```bash
# Install globally
npm install -g laraship

# Use in any Laravel project
cd your-laravel-project
laraship
```

### Using npx (No Installation Required)
```bash
cd your-laravel-project
npx laraship
```

---

## 🚀 Quick Start

1. **Navigate to your Laravel project:**
   ```bash
   cd your-laravel-project
   ```

2. **Run LaraShip:**
   ```bash
   laraship
   ```

3. **Answer a few questions:**
   - Database preferences
   - Redis setup
   - Backup configuration
   - Deployment options

4. **Start your containers:**
   ```bash
   docker-compose up -d
   ```

That’s it! Your Laravel app is now Dockerized and production-ready.

---

## 🎮 Usage Examples

### Development Setup
```bash
cd my-laravel-app
laraship
# Choose MySQL container
# Skip Redis
# Skip backups
```

### Production Setup
```bash
cd my-laravel-app
laraship
# Enable MySQL
# Enable Redis
# Configure backups
# Set up GitHub Actions
# Enable SSL
```

### Common Commands
```bash
# Start services
docker-compose up -d

# View logs
docker-compose logs -f

# Run migrations
docker-compose exec app php artisan migrate

# Stop services
docker-compose down
```

---

## 🛠️ What’s Included

### Core Features
- 🐳 Docker configuration
- 👎 Database setup (MySQL)
- 📦 Redis support
- 🔄 Queue workers
- ⏰ Task scheduling
- 🚀 GitHub Actions integration
- 🔒 SSL/TLS support
- 📀 Automated backups
- 📊 Health monitoring

### Additional Services
- PHPMyAdmin
- Mailhog
- Redis Commander
- Backup solutions
- Slack notifications

---

## 🤝 Contributing
Contributions are welcome! Please feel free to submit a Pull Request.

---

## 🖋️ License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## ❤️ Support
If you find LaraShip helpful, give it a ⭐ to show your support!

---

Made with ❤️ by [torgodly](https://github.com/torgodly)

