<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function __invoke()
    {
        $steps = [
            ['Sense', 'A PIR sensor detects movement while an ambient light sensor measures how bright the room already is.'],
            ['Decide', 'The Arduino weighs presence, brightness and the schedule from the real-time clock to decide if the lights should be on.'],
            ['Switch', 'A relay module turns the lights on or off, and the ESP8266 reports every change to your web dashboard.'],
        ];

        $features = [
            ['Presence detection', 'Lights turn on only when someone is in the room and switch off when they leave.', 'M3 12h4l3-8 4 16 3-8h4'],
            ['Ambient light sensing', 'Skips switching on when daylight is already enough, so you save power.', 'M12 3v2m0 14v2M3 12h2m14 0h2M5.6 5.6l1.4 1.4m10 10l1.4 1.4M5.6 18.4l1.4-1.4m10-10l1.4-1.4M12 8a4 4 0 100 8 4 4 0 000-8z'],
            ['Energy monitoring', 'A PZEM-004T meter tracks voltage, current and consumption in real time.', 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z'],
            ['Smart schedules', 'A DS3231 real-time clock keeps accurate time for schedule-based rules.', 'M12 6v6l4 2M12 21a9 9 0 100-18 9 9 0 000 18z'],
            ['Remote dashboard', 'Check status and usage from any browser over Wi-Fi.', 'M2 9a15 15 0 0120 0M5 12.5a10 10 0 0114 0M8.5 16a5 5 0 017 0M12 19.5h.01'],
            ['Relay control', 'A relay module safely switches one or several lighting circuits.', 'M12 3v9M6.3 6.3a8 8 0 1011.4 0'],
        ];

        $hardware = [
            ['Controller', 'ESP32', 'Runs the logic that ties every sensor and output together.'],
            ['Sensor', 'Ambient light sensor', 'Measures how bright the room is for smarter switching.'],
            ['Sensor', 'PIR HC-SR501', 'Detects human presence by sensing infrared motion.'],
            ['Metering', 'PZEM-004T', 'Reads voltage, current, power and energy use.'],
            ['Timing', 'DS3231 RTC', 'Keeps precise time so schedules stay accurate.'],
            ['Output', '5V relay module', 'Switches the lighting circuits on and off.'],
            ['Connectivity', 'ESP8266 ESP-01', 'Sends readings to the dashboard over Wi-Fi.'],
            ['Software', 'Web dashboard', 'Shows live status and stores readings in a database.'],
        ];

        return view('welcome', compact('steps', 'features', 'hardware'));
    }
}
