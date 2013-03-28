<?php

Configure::write('Exception.renderer', 'Environment.EnvironmentExceptionRenderer');
App::uses('Environment', 'Environment.Lib');
Environment::getInstance()->start();
