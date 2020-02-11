# canAx

Turn a Raspberry Pi into a car speedometer, lap timer, heads up display.

Requires:
- Raspberry Pi with a hardware CAN interface (I used a PiCAN2)
- Raspbian with:
- NodeJS
- Socket.io node module
- PHP + Apache

Script run at boot launches CanAx.js

CanAx will spawn a candump process and feed the data into the vehicle-specific driver file. (found in /modules/drivers/xyz.js)

The js driver parses the raw CAN data and outputs a compiled and updated list of variables pertaining to the vehicles various systems.

The data below is an example of what can be obtained by built out driver:

- Steering angle and torque
- Speed, accelerator pedal, brake pedal pressure/sweep, parking brake, windshield wipers
- RPM, Gal/hr, trip odometer, MPG, 
- Engine temperature of oil and coolant
- Transmission data, gear, clutch sensor data, reverse sensor
- Individual wheel speed sensors, indirect TPMS data
- ESC data, esc enabled, stability control state, stability control intervention activity
- Door ajar status
- HVAC, temperature, fan speed, AC compressor, recirc air, defrosters
- Lighting, headlights, brake lights, turn signals, fog lights, instrument cluster lights
- Cruise control states, activity
- Full VIN decode
- Door lock state via key remote button press

All of this data is parsed into a js object that is sent over TCP/IP using Socket.io.

The client browser, either a GUI on the raspberry Pi (Chromium pointed to 127.0.0.1 in kiosk mode) or another PC/Device acts as front end.
The front end receives a large JS object sent over socket.io every few ms.

The front end can manipulate the data and present it on screen. 
End users can interact with the screen without taking their hand off the steering wheel by using the inactive cruise control buttons for navigation


# Premade Templates
## F1 Screen

Mimics an F1 steering wheel.

Other Features:
- Shift light
- Cold engine overrev protection
- Automatically changing day/night theme depending on headlight setting.

![F1 Screen](/screenshots/f1.png)
## Main Menu
- Main menu navagable via cruise control +/- buttons and cruise cancel. Only works when cruise crontrol is disabled.

Menu Options:

- Emulate/Live: Switches between a candump log file and live can data for easy debugging.
- Record: Starts a candump session to log file. Allows for quick logging during a drive
- Flip: Flips raspberry pi screen so it can be put on a dashboard as a HUD
- Mode: Changes between page screens
- Reboot, Power Off, Refresh, Cancel - Self explanatory

![Menu Screen](/screenshots/menu.png)
## Debug Screen
- Shows object tree and every variable available, navagable with Cruise +/- buttons on wheel

![Debug Screen](/screenshots/debug.png)
## Modern Screen
- Modern speedometer, basic

![Modern Screen](/screenshots/modern.png)
## Timer Screen
- +/- cruise buttons reset and freeze lap data

![Timer Screen](/screenshots/timer.png)
