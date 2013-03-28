<?php

App::uses('ExceptionRenderer', 'Error');

/**
 * ConsoleExceptionRenderer 
 * Renderer class for console
 * 
 * @uses ExceptionRenderer
 * @package Environment Plugin
 * @version 1.0
 * @copyright Copyright (C) 2012 Marcel Publicis All rights reserved.
 * @author Vivien Ripoche <vivien.ripoche@marcelww.com> 
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class EnvironmentExceptionRenderer extends ExceptionRenderer
{
    /**
     * render method is called to display all CakeError errors
     * 
     * @return NULL
     */
    public function render ()
    {
        if ( Environment::isConsole () ) {
            echo 'Error ' . $this->error->getCode () . ' - ' . str_replace( '<br />', "\n", $this->error->getMessage () );
        } else {
            parent::render ();
        }
    }
}
