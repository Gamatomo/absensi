# Sistem Presensi - Laravel Attendance System

A complete attendance tracking system built with Laravel and Tailwind CSS.

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm

### Installation

1. **Install PHP dependencies:**
   ```bash
   composer install
   ```

2. **Install JavaScript dependencies:**
   ```bash
   npm install
   ```

3. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

4. **Run migrations:**
   ```bash
   php artisan migrate
   ```

5. **Compile assets (development):**
   ```bash
   npm run dev
   ```

### Running the Application

Simply run:
```bash
php artisan serve
```

Then open **http://localhost:8000** in your browser.

## 📋 Features

- ✅ Dashboard with real-time statistics
- ✅ Student management (add, view, delete)
- ✅ Bulk CSV import for students
- ✅ Attendance tracking
- ✅ Multiple attendance methods (face recognition, card swipe)
- ✅ Attendance history with filtering
- ✅ Responsive design
- ✅ Beautiful Tailwind CSS UI

## 📁 Project Structure

```
Attendance Website/
├── app/
│   ├── Http/Controllers/
│   │   └── DashboardController.php
│   └── Models/
│       ├── Student.php
│       └── AttendanceRecord.php
├── database/
│   ├── migrations/
│   ├── database.sqlite
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       └── dashboard.blade.php
├── routes/
│   └── web.php
└── composer.json
```

## 🎨 UI Features

- Dashboard with statistics cards
- Student CSV upload with drag-and-drop
- Student addition form
- Student management table
- Attendance history table
- Beautiful Tailwind CSS design
- Responsive on all devices

## ✅ Getting Started

After installation:

1. Visit http://localhost:8000
2. Upload students via CSV or add them manually
3. Record attendance
4. View statistics on the dashboard

## 🐛 Troubleshooting

**Port 8000 already in use?**
```bash
php artisan serve --port=8001
```

**Assets not loading?**
```bash
npm install && npm run dev
```

That's it! Your attendance system is ready to use.
