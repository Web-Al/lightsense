# LightSense (Laravel 12, Blade, Tailwind 4)

Landing page, log in and register, and a dashboard with Overview, Energy, Schedules, Activity and Devices
(administrators also get Users). Roles: Administrator, Staff, Viewer. Open-source maps (Leaflet + OpenStreetMap).
Device API for the ESP32 (see docs/API.md) and a fake ESP32 for testing: `php artisan lightsense:fake-esp32 KEY`.

## Install
1. `composer create-project laravel/laravel lightsense`
2. Copy `app`, `bootstrap`, `database`, `docs`, `public`, `resources` and `routes` from this folder into it. Replace when asked.
3. Set up the database in `.env`, then:  `php artisan migrate:fresh`  (wipes test data)
4. `npm install`  then  `npm run dev`  and in a second terminal  `php artisan serve`
5. Open http://127.0.0.1:8000 and register. The first account to register becomes the administrator.
