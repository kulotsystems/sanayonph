@echo off
start "" php artisan serve
start "" npm run watch
echo.
echo WEB APP IS NOW RUNNING IN:
echo 1. [   php   ] - backend web server
echo 2. [ webpack ] - frontend watcher
echo.
echo Please don't close these two terminals while you're running the app.
echo The app URL can be found in the [   php   ] terminal.
