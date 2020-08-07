<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/
  $t = microtime(true);
$hook['pre_controller'] = array(
                                'class'    => 'RunTimeTest',
                                'function' => 'start',
                                'filename' => 'start.php',
                                'filepath' => 'hooks',
                                'params'   => array($t)
                                );
								
								
								$hook['post_system'] = array(
                                'class'    => 'RunTimeTest',
                                'function' => 'endT',
                                'filename' => 'start.php',
                                'filepath' => 'hooks',
                                'params'   => array($t)
                                );

/* End of file hooks.php */
/* Location: ./system/application/config/hooks.php */