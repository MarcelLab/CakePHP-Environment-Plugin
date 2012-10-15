<?php
/**
 * EnvironmentShell
 * It is a Cake command to generate environment config files from the general of default ones.
 * Just execute in the console: cake Environment.environment [env name] and 
 * confirm the process.
 * The Cake config class must be deleted in the general config files to do not 
 * have a redefintion case but files still must be present because Cake checks 
 * its. The command looks after these constraints.
 *
 * @uses Shell
 * @package Environment Plugin
 * @version 1.0
 * @copyright Copyright (C) 2012 Marcel Publicis All rights reserved.
 * @author Vivien Ripoche <vivien.ripoche@marcelww.com> 
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class EnvironmentShell extends Shell
{
    private $_envFile = null;
    private $_configDir = null;
    private $_databaseFile = null;
    private $_emailFile = null;

    /**
     * getOptionParser 
     * Method called by Shell to configure the help and paramaters of the 
     * command.
     *
     * @return parser object
     */
    public function getOptionParser ()
    {
        try {
            $parser = parent::getOptionParser ();
            $parser->addArgument ( 'environment', array (
                'help' => 'Environment to create',
                'required' => false
            ) )->description('Set a new environment from a classical setup of CakePHP');
        } catch (Exception $e) {
            $this->out ( '<error>' . $e->getMessage() . '</error>' );
        }
        return $parser;
    }

    /**
     * main 
     * Method called by Shell at the begining.
     *
     * @return NULL
     */
    public function main ()
    {
        $this->_configDir = APP . 'Config' . DIRECTORY_SEPARATOR;
        $this->_databaseFile = $this->_configDir . Environment::DBCONFIGFILE;
        $this->_emailFile = $this->_configDir . Environment::EMAILCONFIGFILE;

        $environment = isset ( $this->args[0] ) ? $this->args[0] : null;

        while ( empty ( $environment ) ) {
            $environment = $this->in ( 'Environment name:' );
            if ( empty ( $environment ) ) $this->out('Environment name must not be empty!' );
        }

        $this->_envFile = $this->_configDir . Environment::DIRNAME . DIRECTORY_SEPARATOR . strtolower ( $environment ) . '.php';

        $confirm = $this->in ( 'Do you confirm, basic setup will be removed ?', array('y', 'n'), 'n' );
        if( $confirm == 'y' ) {
            $this->_copyConfigurationFiles ();
            $this->out ('<info>Process Finished</info>');
        }
    }

    /**
     * _copyConfigurationFiles 
     * Used to copy the configs from the general or default files to the 
     * environment one.
     *
     * @return $this
     */
    private function _copyConfigurationFiles ()
    {
        $configList = array();
        $classList = array(Environment::DBCLASS, Environment::EMAILCLASS);

        if ( ! file_exists( $this->_configDir . Environment::DIRNAME ) ) {
            mkdir ( $this->_configDir . Environment::DIRNAME );
        }
        foreach( array( $this->_databaseFile, $this->_emailFile ) as $index => $file ) {
            $handle = @fopen($file, "r");
            if ( ! $handle || ! preg_match( sprintf ( '/class\s+%s\s*\{/', $classList[$index ] ), fread( $handle, filesize ( $file ) ) ) ) {
                fclose($handle);
                $file = $file . '.default';
                $handle = @fopen($file, "r");
                if ( ! $handle ) {
                    throw new CakeException( "The file {$file}.config the was found" );
                }
            }

            $configList[] = "\t" . self::_selectConfiguration( fread( $handle, filesize ( $file ) ) );
            fclose($handle);

        }
        $configList[0] = str_replace ( '$default', '$database', $configList[0] );
        $configList[1] = str_replace ( '$default', '$email', $configList[1] );

        $handle = @fopen($this->_envFile, "w");
        fputs ( $handle, "<?php\n\n" . implode("\n\n", $configList ) . "\n");
        fclose ( $handle );

        $this->_emptyDefaultConfigurationFiles ();

        return $this;
    }

    /**
     * _emptyDefaultConfigurationFiles 
     * Used to delete the configuraton class definitions in the general 
     * configuration files.
     *
     * @return $this
     */
    private function _emptyDefaultConfigurationFiles ()
    {
        foreach( array( $this->_databaseFile, $this->_emailFile ) as $file ) {
            $handle = @fopen ( $file, "r" );
            if ( ! $handle ) {
                throw new CakeException( "The file {$file}.config the was found" );
            }
            $content = fread ( $handle, filesize ( $file ) );
            fclose ( $handle );
            $handle = @fopen ( $file, "w" );
            fputs ( $handle, self::_emptyConfiguration ( $content ) );
            fclose ( $handle );
        }

        return $this;
    }

    /**
     * _selectConfiguration 
     * It finds the array configuration block in Cake files.
     *
     * @param mixed $content 
     * @return string
     */
    private static function _selectConfiguration ( $content )
    {
        $matchesList = array();
        preg_match ( "/(\\\$default.*;)$/msU", $content, $matchesList );
        return isset ( $matchesList[1] ) ? $matchesList[1] : null;
    }

    /**
     * _emptyConfiguration 
     * Used to flush general configuration because of the class redefinition 
     * forbidding.
     *
     * @param mixed $content 
     * @return string
     */
    private static function _emptyConfiguration ( $content )
    {
        return preg_replace ( "/class .*\}$/ms", null, $content );
    }
}
