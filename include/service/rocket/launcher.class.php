<?php

/**
 * Class Rhetinizr_Service_Rocket_Launcher
 */
class Rhetinizr_Service_Rocket_Launcher extends Phpfox_Service
{
	/**
	 * Called when the module is installed
	 */
	public function on()
	{
		if (false === is_dir( Phpfox::getParam( 'core.dir_file' ) . 'rhetina/cache/' )) {
			mkdir( Phpfox::getParam( 'core.dir_file' ) . 'rhetina/cache/' );
		}
		if (false === is_dir( Phpfox::getParam( 'core.dir_file' ) . 'rhetina/logs/' )) {
			mkdir( Phpfox::getParam( 'core.dir_file' ) . 'rhetina/logs/' );
		}
	}

	/**
	 * Called when the module is uninstalled
	 */
	public function off()
	{
		if (true === is_dir( Phpfox::getParam( 'core.dir_file' ) . 'rhetina/' )) {
			rmdir( Phpfox::getParam( 'core.dir_file' ) . 'rhetina/' );
		}
	}
}
