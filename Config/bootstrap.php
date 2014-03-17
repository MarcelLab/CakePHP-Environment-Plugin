<?php

Configure::write('Exception.initRenderer', Configure::read('Exception.renderer'));
Configure::write('Exception.renderer', 'Environment.EnvironmentExceptionRenderer');
App::uses('Environment', 'Environment.Lib');
Environment::getInstance()->start();
