<?php

/**
 * Class Rhetinizr_Service_Rocket_Launcher
 */
class Rhetinizr_Service_Rocket_Launcher extends Phpfox_Service
{
	const MODULE = 'rhetinizr';
	const INC = 'rhetina';

	/**
	 * Called when the module is installed
	 */
	public function on()
	{
		if (false === is_dir( PHPFOX_DIR_FILE . static::INC . DIRECTORY_SEPARATOR )) {
			mkdir( PHPFOX_DIR_FILE . static::INC . DIRECTORY_SEPARATOR );
		}
		if (true === is_dir( PHPFOX_DIR_FILE . static::INC . DIRECTORY_SEPARATOR )) {
			if (false === is_dir( PHPFOX_DIR_FILE . static::INC . '/cache/' )) {
				mkdir( PHPFOX_DIR_FILE . static::INC . '/cache/' );
			}
			if (false === is_dir( PHPFOX_DIR_FILE . static::INC . '/logs/' )) {
				mkdir( PHPFOX_DIR_FILE . static::INC . '/logs/' );
			}
		}

		if (false === is_dir( PHPFOX_DIR_MODULE . static::MODULE . '/headquarters/' )) {
			Phpfox::getLib( 'archive.extension.zip' )->extract(
				PHPFOX_DIR_MODULE . static::MODULE . PHPFOX_DIR_MODULE_XML . '/packed-rocket-elements.zip',
				PHPFOX_DIR_MODULE . static::MODULE
			);
		}
	}

	/**
	 * Called when the module is uninstalled
	 */
	public function off()
	{
		$this->deleteDirectory( PHPFOX_DIR_FILE . static::INC . DIRECTORY_SEPARATOR );
	}

	/**
	 * @param $dir
	 *
	 * @return bool
	 */
	private function deleteDirectory($dir)
	{
		if (!file_exists( $dir )) {
			return true;
		}

		if (!is_dir( $dir )) {
			return unlink( $dir );
		}

		foreach (scandir( $dir ) as $item) {
			if ($item == '.' || $item == '..') {
				continue;
			}

			if (!$this->deleteDirectory( $dir . DIRECTORY_SEPARATOR . $item )) {
				return false;
			}

		}

		return rmdir( $dir );
	}
}
