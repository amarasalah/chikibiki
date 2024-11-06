<?php
namespace LoftOcean\Libraries\iCalendar;

/**
 * framework.php - framework file
 *
 * @package	ZapCalLib
 * @author	Dan Cogliano <http://zcontent.net>
 * @copyright   Copyright (C) 2006 - 2017 by Dan Cogliano
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link	http://icalendar.org/php-library.html
 */

// No direct access
defined('_ZAPCAL') or die( 'Restricted access' );

/**
 * set MAXYEAR to 2036 for 32 bit systems, can be higher for 64 bit systems
 *
 * @var integer
 */

if ( ! defined( '_ZAPCAL_MAXYEAR' ) ) {
    define( '_ZAPCAL_MAXYEAR', 3000 );
}

/**
 * set MAXREVENTS to maximum # of repeating events
 *
 * @var integer
 */
if ( ! defined( '_ZAPCAL_MAXREVENTS' ) ) {
    define( '_ZAPCAL_MAXREVENTS', 5000 );
}

require_once( _LOFTOCEAN_ZAPCAL_BASE . '/includes/date.php' );
require_once( _LOFTOCEAN_ZAPCAL_BASE . '/includes/recurringdate.php' );
require_once( _LOFTOCEAN_ZAPCAL_BASE . '/includes/ical.php' );
require_once( _LOFTOCEAN_ZAPCAL_BASE . '/includes/timezone.php' );
