# canAx
UI for candump
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
![GitHub Logo](/images/logo.png)
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
