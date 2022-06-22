# Clever

Web game based on That is pretty clever by Wolgang Warsch

This project uses [defuse/php-encryption](https://github.com/defuse/php-encryption) and [walkor/workerman](https://github.com/walkor/workerman).

## Websocket

The game is played in browser with a websocket connection.

All following commands must be executed in the project root folder.

Command to start the websocket deamon

`php clever.php start -d`

Command to start the websocket in debug mode

`php clever.php start`

Command to stop the websocket (deamon only)

`php clever.php stop`

Command to see the status of the websocket (deamon only)

`php clever.php status`