CakePHP-Environment-Plugin
==========================

Plugin used to manage environments on CakePhp 2.x

Setup
-----

You need to clone the files into an "Environment" directory in app/Plugin.
Then, add this CakePlugin::load in the app bootstrap and active the plugin bootstrap :

> CakePlugin::load('Environment', array('bootstrap' => true));

Add a new Environment
--------------------

Execute in the Shell:

> cake Environment.environment

input the environment name and confirm the operation, it is ready !

Notes
-----

The environment name is defined in the server configuration with SetEnv, the variables can have these names:
- ENV
- APP_ENV
- CAKE_ENV
- ENVIRONMENT

The Shell creates an "Environments" directory and create one php file for each environment.
The database and the email configuration is took from the app "database.php" and "email.php" for the first environment,
for the followers, it takes the default configurations.