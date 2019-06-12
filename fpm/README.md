# Aynchronous PHP with FPM Example Code

This code was used to showcase the [PHP FastCGI Client](https://github.com/hollodotme/fast-cgi-client) by Holger Woltersdorf. 

It uses Redis and its [Stream data type](https://redis.io/topics/streams-intro) introduced in version 5.0 as an event store.

## Usage

Start the required services by running `docker-compose up -d`. This will start a container running Redis and a container running PHP-FPM.

Run `php createEvents.php <numberOfEvents>` to add a given number of random events to Redis. 

Run `php listen.php` to start the (endlessly running) event listener.
