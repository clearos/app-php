<?php

/**
 * PHP class.
 *
 * @category   apps
 * @package    php
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2017 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/php/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\php;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('php');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

// Classes
//--------

use \clearos\apps\base\File as File;
use \clearos\apps\base\Software as Software;
use \clearos\apps\date\Time as Time;

clearos_load_library('base/File');
clearos_load_library('base/Software');
clearos_load_library('date/Time');

// Exceptions
//-----------

use \Exception as Exception;
use \clearos\apps\base\Engine_Exception as Engine_Exception;
use \clearos\apps\base\File_No_Match_Exception as File_No_Match_Exception;
use \clearos\apps\base\File_Not_Found_Exception as File_Not_Found_Exception;

clearos_load_library('base/Engine_Exception');
clearos_load_library('base/File_No_Match_Exception');
clearos_load_library('base/File_Not_Found_Exception');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * PHP class.
 *
 * @category   apps
 * @package    php
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2017 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/php/
 */

class PHP extends Software
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    const FILE_APP_CONFIG = '/etc/clearos/php.conf';
    const FILE_CONFIG = '/etc/php.ini';

    var $mapping = [
        'Cuba' => 'America/Havana',
        'Egypt' => 'Africa/Cairo',
        'Greenwich' => 'UTC',
        'Hongkong' => 'Asia/Hong_Kong',
        'Iceland' => 'Atlantic/Reykjavik',
        'Iran' => 'Asia/Tehran',
        'Israel' => 'Asia/Tel_Aviv',
        'Jamaica' => 'America/Jamaica',
        'Japan' => 'Asia/Tokyo',
        'Libya' => 'Africa/Tripoli',
        'Poland' => 'Europe/Warsaw',
        'Portugal' => 'Europe/Lisbon',
        'Singapore' => 'Asia/Singapore',
        'Turkey' => 'Asia/Istanbul',
        'Universal' => 'UTC',
        'US/Alaska' => 'America/Juneau',
        'US/Aleutian' => 'Pacific/Honolulu',
        'US/Central' => 'America/Chicago',
        'US/Eastern' => 'America/New_York',
        'US/Hawaii' => 'Pacific/Honolulu',
        'US/Mountain' => 'America/Denver',
        'US/Pacific' => 'America/Los_Angeles',
        'Zulu' => 'UTC',
    ];

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * PHP constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);

        parent::__construct('php');
    }

    /**
     * Auto configures PPTP.
     *
     * @return void
     * @throws Engine_Exception
     */

    public function auto_configure()
    {
        clearos_profile(__METHOD__, __LINE__);

        if (! $this->get_auto_configure_state())
            return;

        $php_timezones = $this->get_timezones();

        $time = new Time();
        $system_timezone = $time->get_time_zone();
        $php_timezone = '';

        if (in_array($system_timezone, $php_timezones)) {
            $php_timezone = $system_timezone;
            clearos_log('php', "set timezone - $php_timezone");
        } else {
            if (array_key_exists($system_timezone, $this->mapping)) {
                $php_timezone = $this->mapping[$system_timezone];
                clearos_log('php', "mapped system timezone $system_timezone to PHP timezone $php_timezone");
            } else {
                $php_timezone = 'America/New_York';
                clearos_log('php', "unable find PHP timezone for system timezone: $system_timezone");
            }
        }

        $file = new File(self::FILE_CONFIG);

        $replaced = $file->replace_lines('/^date.timezone\s*=/', "date.timezone = $php_timezone\n");

        if ($replaced == 0) {
            $replaced = $file->replace_lines('/^;\s*date.timezone\s*=/', "date.timezone = $php_timezone\n");

            if ($replaced == 0)
                $file->add_lines_after("date.timezone = $php_timezone\n", "/^\[Date\]/");
        }
    }

    /**
     * Returns auto-configure state.
     *
     * @return boolean state of auto-configure mode
     */

    public function get_auto_configure_state()
    {
        clearos_profile(__METHOD__, __LINE__);

        try {
            $file = new File(self::FILE_APP_CONFIG);
            $value = $file->lookup_value("/^auto_configure\s*=\s*/i");
        } catch (File_Not_Found_Exception $e) {
            return FALSE;
        } catch (File_No_Match_Exception $e) {
            return FALSE;
        } catch (Exception $e) {
            throw new Engine_Exception($e->get_message());
        }

        if (preg_match('/yes/i', $value))
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Gets list of PHP timezones.
     *
     * @return array list of PHP timezones
     * @throws Engine_Exception, Validation_Exception
     */

    public function get_timezones()
    {
        clearos_profile(__METHOD__, __LINE__);

        $timezones = [];

        foreach(timezone_abbreviations_list() as $abbr => $timezone){
            foreach($timezone as $val){
                if (isset($val['timezone_id']))
                    $timezones[] = $val['timezone_id'];
            }
        }

        $timezones = array_unique($timezones);
        sort($timezones);

        return $timezones;
    }

    /**
     * Provides list of PHP timezones that do not exist in Linux.
     *
     * @return void
     * @throws Engine_Exception, Validation_Exception
     */

    public function timezone_diff()
    {
        clearos_profile(__METHOD__, __LINE__);

        $time = new Time();
        $system_timezones = array_keys($time->get_time_zone_list());
        $php_timezones = $this->get_timezones();

        $diff['mapped'] = [];
        $diff['missing'] = [];

        foreach ($system_timezones as $timezone) {
            // $timezone = preg_replace('/ /', '_', $timezone);

            if (!in_array($timezone, $php_timezones)) {
                if (!array_key_exists($timezone, $this->mapping))
                    $diff['missing'][] = $timezone;
                else
                    $diff['mapped'][] = $timezone;
            }
        }

        return $diff;
    }
}
