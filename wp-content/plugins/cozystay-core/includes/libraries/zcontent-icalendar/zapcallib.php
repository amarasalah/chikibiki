<?php
/**
 * zapcallib.php
 *
 * @package	ZapCalLib
 * @author	Dan Cogliano <http://zcontent.net>
 * @copyright   Copyright (C) 2006 - 2017 by Dan Cogliano
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link	http://icalendar.org/php-library.html
 */

/**
 * used by ZapCalLib
 * @var integer
 */
if ( ! defined( '_ZAPCAL' ) ) {
	define( '_ZAPCAL', 1 );
}

if (  ! defined( '_LOFTOCEAN_ZAPCAL_BASE' ) ) {
	/**
	 * the base folder of the library
	 * @var string
	 */
	define( '_LOFTOCEAN_ZAPCAL_BASE',__DIR__ );
}

require_once( _LOFTOCEAN_ZAPCAL_BASE . '/includes/framework.php' );

