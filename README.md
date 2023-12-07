# Contact Manager Backend
- PHP application to manage data sent to and requested from the Flutter Application.

## Setup
- Clone this project to your local device.
- Run `composer update` to update all dependencies.
- Run mysql and setup the database and ports.
- Test for complete connection of the application to mysql database using IDE provided tools and dialog prompts.
- Run `php artisan migrate: fresh --seed` to create tables and feed them with data.
- Run `php artisan serve` to start the server application.
- Install ngrok client on local device to expose local server URL over the internet.
- Run `ngrok http <local_port>` where "local_port" is the port on the local server address.
- Use the URL in Flutter Application or API client application such as Postman.
