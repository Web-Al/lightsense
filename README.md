# LightSense – Laravel 12 + Tailwind 4 (Blade)

Pages built from the Figma design: landing page, log in, register, dashboard (desktop + mobile responsive).

## Install
1. Create a fresh project:  `composer create-project laravel/laravel lightsense && cd lightsense`
2. Copy the folders from this archive (`app/`, `resources/`, `routes/`) into the project, overwriting `routes/web.php` and `resources/css/app.css`.
3. Set up the database in `.env` (SQLite works out of the box), then:
   ```
   php artisan migrate
   npm install
   npm run dev
   php artisan serve
   ```
4. Open http://localhost:8000

Notes
- Uses Laravel's default `User` model/migration; passwords are hashed by its `hashed` cast.
- `DashboardController` returns sample data – replace with readings from your ESP32 (e.g. a `readings` table + API route).
- If you're on Laravel 11 (Tailwind 3), move the `@theme` colors into `tailwind.config.js`.

## Dashboard update
Added Energy, Schedules (saved to the database) and Activity pages. After copying files run `php artisan migrate`.
