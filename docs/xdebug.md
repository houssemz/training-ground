# How to enable Xdebug

1. Run `cp docker/dev/xdebug.env.dist docker/dev/xdebug.env` at project root and setup local configuration based on information provided

2. In Phpstorm settings, go to PHP -> Servers.  
Add a new server by clinking on + button
Set the name as "cli" (like described in the xdebug.env.dist file under `PHP_IDE_CONFIG=serverName=`key)  
Set the host as "localhost", port 80, with Xdebug debugger  
Check _Use path mapping_ checkbox and at `shippeo.php-ms-template/srv` path, add `/srv` as absolute path on server
3. Go to settings, PHP -> Debug  
In _External connection_ fieldset, check only the first checkbox  
In _Xdebug_ fieldset, do the same
4. Empty .env `APP_DISABLED_EXTENSIONS` value
5. run `task app:docker:clean` then `task app:docker:start` 
6. To check that Xdebug is enabled, run `task app:docker:enter` then `php -m`. Xdebug should be displayed under [Zend Modules]
7. Click on the toolbar button "Start listening for PHP debug connection"
8. You can start debugging
