# LightSense device API

Base address: `http://<server>/api`  (a laptop on the same Wi-Fi: `http://192.168.x.x:8000/api`)

Every request needs the light's key (from the Devices page):

    Authorization: Bearer ls_xxxxxxxx
    Content-Type: application/json

## GET /ping
Checks the key. Reply: `{"ok":true,"device":"Ceiling light","location":"Living room","server_time":"2026-11-16 14:03:55"}`

## GET /settings
What the ESP32 saves in its own memory and works from, even when offline.

    {
      "mode": "automatic",            // "automatic" or "manual"
      "manual_on": false,             // used only in manual mode
      "lux_threshold": 300,           // switch on only when darker than this
      "schedules": [ {"days":[1,2,3,4,5,6,7],"start":"18:00","end":"23:00","enabled":true} ],
      "version": "a1b2c3d4",          // changes whenever any setting changes
      "server_time": "2026-11-16 14:03:55",   // local time, use it to set the DS3231 clock
      "timezone": "Asia/Manila"
    }

`days` is a list of weekday numbers: 1 = Monday ... 7 = Sunday (for example `[1,3,5]` = Mon, Wed, Fri). `end` may be null ("onward", until midnight) or earlier than `start` (crosses midnight).

## POST /readings
One reading, or a batch of up to 100 (what was saved while offline).

    { "readings": [
        { "lux": 280, "presence": true, "lights_on": true,
          "voltage": 229.8, "current": 0.148, "power": 34.0, "energy_kwh": 1.2345,
          "age_s": 15 }                 // seconds ago the reading was taken
    ] }

A single reading can be sent without the `readings` wrapper. Only `lux` and `presence` are required.
Success: HTTP 201 `{"ok":true,"saved":3,"settings_version":"a1b2c3d4"}`.
If `settings_version` differs from the saved `version`, download `/settings` again.

## What the ESP32 must do (the same as `php artisan lightsense:fake-esp32`)
1. At start-up: `GET /settings`, save it in flash memory. If the website cannot be reached, use the saved copy.
2. Every few seconds: read PIR, light sensor and the energy meter; decide the relay state yourself:
   - manual mode: relay = `manual_on`
   - automatic mode: relay = someone present AND lux < `lux_threshold` AND the schedule rule allows it
   - schedule rule: no enabled schedule = always allowed; otherwise only inside an enabled window
3. Keep each reading in memory with the time it was taken.
4. Every 15-60 seconds: `POST /readings` with everything saved. Remove readings from memory **only after HTTP 201**.
5. If the POST fails, keep the readings and try again later. Cap the buffer (for example 500 readings, dropping the oldest).
6. Use `settings_version` from each reply to know when to download new settings.
7. A physical override switch must still work when everything else fails.
