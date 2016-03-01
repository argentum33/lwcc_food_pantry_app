<?php
/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Easy!Appointments Configuration File
 *
 * Set your installation BASE_URL * without the trailing slash * and the database
 * credentials in order to connect to the database. You can enable the DEBUG_MODE
 * while developing the application.
 *
 * Set the default language by changing the LANGUAGE constant. For a full list of
 * available languages look at the /application/config/config.php file.
 *
 * IMPORTANT:
 * If you are updating from version 1.0 you will have to create a new "config.php"
 * file because the old "configuration.php" is not used anymore.
 */
 
// testing git local branch
class Config {
    // ------------------------------------------------------------------------
    // General Settings
    // ------------------------------------------------------------------------
    const BASE_URL      = 'http://javascript-test-argentum33.c9users.io'; // I think this is normally http://localhost
    const LANGUAGE      = 'english';
    const DEBUG_MODE    = FALSE;

    // ------------------------------------------------------------------------
    // Database Settings
    // ------------------------------------------------------------------------
    const DB_HOST       = '0.0.0.0'; // I think this would normally be localhost
    const DB_NAME       = 'c9'; // your database name
    const DB_USERNAME   = 'argentum33'; // database username
    const DB_PASSWORD   = ''; // my database has no password

    // ------------------------------------------------------------------------
    // Google Calendar Sync
    // ------------------------------------------------------------------------
    const GOOGLE_SYNC_FEATURE   = FALSE; // Enter TRUE or FALSE
    const GOOGLE_PRODUCT_NAME   = '';
    const GOOGLE_CLIENT_ID      = '';
    const GOOGLE_CLIENT_SECRET  = '';
    const GOOGLE_API_KEY        = '';
}
/* End of file config.php */
/* Location: ./config.php */
