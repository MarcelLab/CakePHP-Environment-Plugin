<?php
/**
 * DATABASE_CONFIG 
 * 
 * @package Environment Plugin
 * @version 1.0
 * @copyright Copyright (C) 2012 Marcel Publicis All rights reserved.
 * @author Vivien Ripoche <vivien.ripoche@marcelww.com> 
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class DATABASE_CONFIG
{
    public $default = null;

    /**
     * __construct 
     * 
     * @return NULL
     */
    public function __construct ()
    {
        $this->default = Environment::getInstance()->getDatabaseConfiguration ();
    }
}
