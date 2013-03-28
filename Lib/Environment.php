<?php
/**
 * Environment
 * Used to manage dynamic environments depends on Server env variables CAKE_ENV, 
 * APPLICATION_ENV, ENVIRONMENT or ENV.
 * 
 * @package Environment Plugin
 * @version 1.0
 * @copyright Copyright (C) 2012 Marcel Publicis All rights reserved.
 * @author Vivien Ripoche <vivien.ripoche@marcelww.com> 
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Environment
{
    const DIRNAME = 'Environments';
    const DBCONFIGFILE = 'database.php';
    const EMAILCONFIGFILE = 'email.php';
    const DBCLASS = 'DATABASE_CONFIG';
    const EMAILCLASS = 'EmailConfig';

    private static $_envVariablesList = array('CAKE_ENV', 'APPLICATION_ENV', 'ENVIRONMENT', 'ENV');
    private static $_configList = array();
    private static $_instance = null;

    private $_environment = 'default';
    private $_envFile = null;
    private $_configDir = null;
    private $_pluginConfigDir = null;

    private $_dbConfig = null;
    private $_emailConfig = null;

    /**
     * __construct 
     * Used to initiate the environment and the different files or directories 
     * paths.
     * 
     * @return NULL
     */
    public function __construct ()
    {
        $this->_setEnv ();
        $this->_configDir = APP . 'Config' . DIRECTORY_SEPARATOR;
        $this->_pluginConfigDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR;
        $this->_envFile = $this->_configDir . self::DIRNAME . DIRECTORY_SEPARATOR . $this->_environment . '.php';
    }

    /**
     * &getInstance 
     * SINGLETON instanciation method.
     * 
     * @return NULL
     */
    public static function &getInstance ()
    {
        if ( ! self::$_instance ) {
            self::$_instance = new Environment ();
        }
        return self::$_instance;
    }

    /**
     * start 
     * It begins the environment selection process; it chooses the good app 
     * config file to load, includes it and saves the configuration data for the 
     * specific CakePHP config classes.
     *
     * @return $this to able chainig
     */
    public function start ()
    {

        if ( self::isEnvironmentCommand () ) {
            return $this;
        }

        if ( ! is_file ( $this->_envFile ) ) {
            throw new CakeException( sprintf( 'Environment file "%s.php" is not present in the Config/%s directory.%sUse the "cake environment [env-name]" command to create it.', $this->getEnv (), self::DIRNAME, "<br />" ) );
        }
        if ( ! is_readable ( $this->_envFile ) ) {
            throw new CakeException( sprintf( 'Environment file "%s.php" is not readable.', $this->getEnv() ) );
        }
        if ( class_exists ( self::DBCONFIGFILE ) ) {
            throw new CakeException ( 'DATABASE_CONFIG class exists, you must delete it if you want use environments.' );
        }
        if ( class_exists ( self::EMAILCONFIGFILE ) ) {
            throw new CakeException ( 'EmailConfig class exists, you must delete it if you want use environments.' );
        }

        require_once ( $this->_envFile );

        if ( ! isset ( $database ) ) {
            throw new CakeException ( sprintf ( '$database variable is not set in "%s.php".', $this->getEnv() ) );
        }

        if ( ! isset ( $email ) ) {
            throw new CakeException ( sprintf ( '$email variable is not set in "%s.php".', $this->getEnv() ) );
        }

        $this->_dbConfig = $database;
        $this->_emailConfig = $email;

        require_once( $this->_pluginConfigDir . self::DBCONFIGFILE );
        require_once( $this->_pluginConfigDir . self::EMAILCONFIGFILE );

        return $this;
    }

    /**
     * getDatabaseConfiguration 
     * Accessor to get the database configuration.
     * 
     * @return array
     */
    public function getDatabaseConfiguration ()
    {
        return $this->_dbConfig;
    }

    /**
     * getEmailConfiguration 
     * Accessor to get the email configuration.
     * 
     * @return array
     */
    public function getEmailConfiguration ()
    {
        return $this->_emailConfig;
    }

    /**
     * getEnv 
     * Accessor to get the current environment.
     *
     * @return string
     */
    public function getEnv ()
    {
        return $this->_environment;
    }

    /**
     * _setEnv 
     * Environment configurator depends on Server env variables.
     * 
     * @return $this
     */
    private function _setEnv ()
    {
        foreach( self::$_envVariablesList as $envVariable ) {
            if( getenv( $envVariable ) ) {
                $this->_environment = strtolower ( getenv( $envVariable ) );
                break;
            }
        }
        if( self::isConsole () ) $this->_environment = 'console';
        return $this;
    }

    /**
     * isConsole 
     * Check if the program is in the CLI context.
     *
     * @return bool
     */
    public static function isConsole ()
    {
        return isset ( $_SERVER['argc'] ) && is_numeric( $_SERVER['argc'] ) && $_SERVER['argc'] > 0;
    }

    /**
     * isEnvironmentCommand 
     * Check if the current executed command is the Environment one.
     * 
     * @return bool
     */
    public static function isEnvironmentCommand ()
    {
        return isset ( $_SERVER['argv'] ) && isset ( $_SERVER['argv'][3] ) && $_SERVER['argv'][3] == 'Environment.environment';
    }
}
