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
            ['Presence detection', 'Lights turn on only when someone is in the room and switch off when they leave.', 'M5 19h14M7 19c0-3 2.2-5 5-5s5 2 5 5M9.5 11.5a3.5 3.5 0 015 0M7 9a7 7 0 0110 0'],
            ['Ambient light sensing', 'Skips switching on when daylight is already enough, so you save power.', 'M2 15h5M17 15h5M7 12h10v6H7zM5 4l4 4M5 4v3M5 4h3M10 3l4 4M10 3v3M10 3h3'],
            ['Energy monitoring', 'A PZEM-004T meter tracks voltage, current and consumption in real time.', 'M12 3a9 9 0 100 18 9 9 0 000-18zM12 12l4-4M6 14h.01M8 8.5h.01M12 6h.01M16 8.5h.01M18 14h.01'],
            ['Smart schedules', 'A DS3231 real-time clock keeps accurate time for schedule-based rules.', 'M2 12h6M16 12h6M8 7v10M16 7v10M10.5 8h3v8h-3z'],
            ['Remote dashboard', 'Check status and usage from any browser over Wi-Fi.', 'M12 21v-9M12 12L7 3M12 12l5-9M7 3h10'],
            ['Relay control', 'A relay module safely switches one or several lighting circuits.', 'M3 8h4M17 8h4M7 5h10v6H7zM3 17h5M8 17l8-4M17 13h4M17 19h4'],
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
