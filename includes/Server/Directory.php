<?php
namespace Redaxscript\Server;

use function dirname;

/**
 * children class to get the directory
 *
 * @since 2.4.0
 *
 * @package Redaxscript
 * @category Server
 * @author Henry Ruhs
 */

class Directory extends ServerAbstract
{
	/**
	 * get the output
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */

	public function getOutput() : string
	{
		return dirname($this->_request->getServer('SCRIPT_NAME'));
	}
}