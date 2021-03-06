<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
 * Backend Controller
 *
 * @package Controllers
 */
class Backend extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');

		// Set user's selected language.
        if ($this->session->userdata('language')) {
        	$this->config->set_item('language', $this->session->userdata('language'));
        	$this->lang->load('translations', $this->session->userdata('language'));
        } else {
        	$this->lang->load('translations', $this->config->item('language')); // default
        }
    }

    /**
     * Display the main backend page.
     *
     * This method displays the main backend page. All users login permission can
     * view this page which displays a calendar with the events of the selected
     * provider or service. If a user has more priviledges he will see more menus
     * at the top of the page.
     *
     * @param string $appointment_hash If given, the appointment edit dialog will
     * appear when the page loads.
     */
    public function index($appointment_hash = '') {
    	date_default_timezone_set ( 'America/New_York' );
    	 
        $this->session->set_userdata('dest_url', $this->config->item('base_url') . '/index.php/backend');
        if (!$this->has_privileges(PRIV_APPOINTMENTS)) return;

        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('customers_model');
        $this->load->model('settings_model');
        $this->load->model('roles_model');
        $this->load->model('user_model');
        $this->load->model('secretaries_model');
        $this->load->model('appointment_status_model');
        

        $view['base_url'] = $this->config->item('base_url');
        $view['user_display_name'] = $this->user_model->get_user_display_name($this->session->userdata('user_id'));
        $view['active_menu'] = PRIV_APPOINTMENTS;
        $view['book_advance_timeout'] = $this->settings_model->get_setting('book_advance_timeout');
        $view['missed_app_num'] = $this->settings_model->get_setting('missed_app_num');
        $view['date_format'] = $this->settings_model->get_setting('date_format');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['available_providers'] = $this->providers_model->get_available_providers();
        $view['available_services'] = $this->services_model->get_available_services();
        $view['customers'] = $this->customers_model->get_batch();
        
        $default_provider_settings = ($this->user_model->get_user_settings_by_username('default'));
        $view['default_working_plan'] = $default_provider_settings['working_plan'];
        
        // variables for missed appointment calculations
        $missed_app_timeframe = $this->settings_model->get_setting('missed_app_timeframe');
        $app_probation = $this->settings_model->get_setting('app_probation');
        $today = new DateTime();
        $missed_app_cutoff = (new DateTime())->sub(new DateInterval("P" . $missed_app_timeframe . "M"))->format('Y-m-d 00:00:00');
        $date_format = $this->settings_model->get_setting('date_format');
        
        
        // variables for next appointment time calculations
        $start_date_found = false;
        $working_plan = json_decode($view['default_working_plan'], true);
        $last_appointment_startdate = ($this->appointments_model->get_last_appointment());
        $last_appointment_startdate = $last_appointment_startdate['start_datetime'];
        $last_appointment_datetime = new DateTime($last_appointment_startdate);
        
        if(!empty($last_appointment_startdate) && ($last_appointment_datetime > $today)) {
        	
        	$last_appointment_day_of_week = new DateTime($last_appointment_startdate);
        	$last_appointment_day_of_week = $last_appointment_day_of_week->format('l');
        	$view['data'] = $last_appointment_day_of_week;
        	$working_plan_day = $working_plan[strtolower($last_appointment_day_of_week)];
        	$next_appointment_startdate = (new DateTime($last_appointment_startdate))->add(new DateInterval('PT' . $view['book_advance_timeout'] . 'M'));
        	$view['next_appointment_startdate'] = $next_appointment_startdate->format('Y-m-d H:i:s');
        	
        	
        	if(isset($working_plan_day)) {
        		$working_day_endtime = explode(':', $working_plan_day['end']);
        		$working_day_endtime_date = (new DateTime($last_appointment_startdate));
        		$working_day_endtime_date->setTime($working_day_endtime[0], $working_day_endtime[1]);
        			
        		if($next_appointment_startdate < $working_day_endtime_date)  {
        			$start_date_found = true;
        		}
        			
        	}
        	
        	if(!($start_date_found == true)) {
        		
        		//$view['next_appointment_startdate'] = 'null';
        		
        		
        		$day_num_found = false;
        		$days_until_next_working_day = 1;
        		$start_date = (new DateTime($last_appointment_startdate));
        		$start_date->add(new DateInterval('P1D'));        		
        		$day_of_week = strtolower($start_date->format('l'));
        		
        		 
        		 
        		 
        		while(!($day_num_found)) {
        			
        			$day_of_week = strtolower($start_date->format('l'));
        			 
        			if(isset($working_plan[$day_of_week])) {
        				$day_num_found = true;
        				
        			} else {
        				$days_until_next_working_day = $days_until_next_working_day + 1;
        				$start_date->add(new DateInterval('P1D'));
        	
        				if(strtolower($start_date->format('l')) == strtolower($last_appointment_day_of_week)) {
        					$days_until_next_working_day = 1;
        					$day_num_found = true;
        				}
        	
        			}
        			
        		}
        		
        		$next_appointment_startdate = (new DateTime($last_appointment_startdate));        		
        		$next_appointment_startdate->add(new DateInterval('P' . $days_until_next_working_day . 'D'));        		
        		$working_plan_day = $working_plan[strtolower($next_appointment_startdate->format('l'))];
        		if(isset($working_plan_day)) {
        			$working_day_endtime = explode(':', $working_plan_day['start']);
        			$next_appointment_startdate->setTime(intval($working_day_endtime[0], 10), intval($working_day_endtime[1], 10));
        		}

        		$view['next_appointment_startdate'] = $next_appointment_startdate->format('Y-m-d H:i:s');
        		
        	}
        	
        	

        	 
        }  else {
        	
        	
        	$day_num_found = false;
        	$days_until_next_working_day = 1;
        	$start_date = (new DateTime());
        	//$view['next_appointment_startdate'] = $start_date->format('Y-m-d H:i:s');
        	$start_date->add(new DateInterval('P1D'));
        	$day_of_week = strtolower($start_date->format('l'));
        	$today_day_of_week = new DateTime();
        	$today_day_of_week = $today_day_of_week->format('l');
        	
        	 
        	 
        	 
        	while(!($day_num_found)) {
        		 
        		$day_of_week = strtolower($start_date->format('l'));
        	
        		if(isset($working_plan[$day_of_week])) {
        			$day_num_found = true;
        			//$view['next_appointment_startdate'] = $days_until_next_working_day;
        			        			
        		} else {
        			$days_until_next_working_day = $days_until_next_working_day + 1;
        			$start_date->add(new DateInterval('P1D'));
        			 
        			if(strtolower($start_date->format('l')) == strtolower($today_day_of_week)) {
        				$days_until_next_working_day = 1;
        				$day_num_found = true;
        			}
        			 
        		}
        		 
        	}
        	
        	$next_appointment_startdate = (new DateTime());
        	$next_appointment_startdate->add(new DateInterval('P' . $days_until_next_working_day . 'D'));
        	$working_plan_day = $working_plan[strtolower($next_appointment_startdate->format('l'))];
        	if(isset($working_plan_day)) {
        		$working_day_endtime = explode(':', $working_plan_day['start']);
        		$next_appointment_startdate->setTime(intval($working_day_endtime[0], 10), intval($working_day_endtime[1], 10));
        	}

        	$view['next_appointment_startdate'] = $next_appointment_startdate->format('Y-m-d H:i:s');
        	
        	//$view['next_appointment_startdate'] = 'null';
        	
        }
        
        
        foreach ($view['customers'] as &$customerRow) {
        
        	if (isset($customerRow['unlock_date']) && ($today  < (new DateTime($customerRow['unlock_date'])))) {
                
                	$customerRow['status'] = 'locked';
                	$customerRow['unlock_date_display'] = $this->format_date($customerRow['unlock_date'], $date_format);
                	
                	
                }   else {
                	
        			$status = ($this->appointments_model->get_missed_appointments_within_date($customerRow['id'],  $missed_app_cutoff) < $view['missed_app_num']) ? 'unlocked' : 'locked';
        			
        			if ($status === 'locked') {
                    
                    	$customerRow['unlock_date'] = $this->appointments_model->get_last_missed_appointment_date($customerRow['id']);
                    	$customerRow['unlock_date'] = (isset($customerRow['unlock_date'])) ? (new DateTime($customerRow['unlock_date']))->add(new DateInterval("P" . $app_probation . "M"))->format('Y-m-d 00:00:00') : $today->format('Y-m-d 00:00:00');
                    	$this->customers_model->add($customerRow);
                    	$customerRow['unlock_date_display'] = $this->format_date($customerRow['unlock_date'], $date_format);
                    	
                    
                    } else if ($status === 'unlocked') {
                    	$customerRow['unlock_date'] = '';
                    	$customerRow['unlock_date_display'] = '';
                    	
                    	
                    }
                    
                    $customerRow['status'] = $status;
                    
                
            }
            
            
        
        }
        
        
        
        $this->set_user_data($view);

        if ($this->session->userdata('role_slug') == DB_SLUG_SECRETARY) {
            $secretary = $this->secretaries_model->get_row($this->session->userdata('user_id'));
            $view['secretary_providers'] = $secretary['providers'];
        } else {
            $view['secretary_providers'] = array();
        }

        $results = $this->appointments_model->get_batch(array('hash' => $appointment_hash));
        if ($appointment_hash != '' && count($results) > 0) {
            $appointment = $results[0];
            $appointment['customer'] = $this->customers_model->get_row($appointment['id_users_customer']);
            $view['edit_appointment'] = $appointment; // This will display the appointment edit dialog on page load.
        } else {
            $view['edit_appointment'] = NULL;
        }

        $this->load->view('backend/header', $view);
        $this->load->view('backend/calendar', $view);
        $this->load->view('backend/footer', $view);
    }

    /**
     * Display the backend customers page.
     *
     * In this page the user can manage all the customer records of the system.
     */
    public function customers() {
    	date_default_timezone_set ( 'America/New_York' );
    	 
        $this->session->set_userdata('dest_url', $this->config->item('base_url') . '/index.php/backend/customers');
    	if (!$this->has_privileges(PRIV_CUSTOMERS)) return;

        $this->load->model('providers_model');
        $this->load->model('customers_model');
        $this->load->model('appointments_model');
        $this->load->model('services_model');
        $this->load->model('settings_model');
        $this->load->model('user_model');

        $view['base_url'] = $this->config->item('base_url');
        $view['user_display_name'] = $this->user_model->get_user_display_name($this->session->userdata('user_id'));
        $view['active_menu'] = PRIV_CUSTOMERS;
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['date_format'] = $this->settings_model->get_setting('date_format');
        $view['customers'] = $this->customers_model->get_batch();
        $view['available_providers'] = $this->providers_model->get_available_providers();
        $view['available_services'] = $this->services_model->get_available_services();
        $view['missed_app_num'] = $this->settings_model->get_setting('missed_app_num');
        
        
        $missed_app_timeframe = $this->settings_model->get_setting('missed_app_timeframe');
        $app_probation = $this->settings_model->get_setting('app_probation');
        $today = new DateTime();
        $missed_app_cutoff = (new DateTime())->sub(new DateInterval("P" . $missed_app_timeframe . "M"))->format('Y-m-d 00:00:00');
        $date_format = $this->settings_model->get_setting('date_format');
        
        
        foreach ($view['customers'] as &$customerRow) {
        	
        	$customerRow['missed_app_num'] = $this->appointments_model->get_missed_appointments_within_date($customerRow['id'],  $missed_app_cutoff);
        
        	if (isset($customerRow['unlock_date']) && ($today  < (new DateTime($customerRow['unlock_date'])))) {
                
                	$customerRow['status'] = 'locked';
                	$customerRow['unlock_date_display'] = $this->format_date($customerRow['unlock_date'], $date_format);
                	
                	
                }   else {
                	
        			$status = ($customerRow['missed_app_num'] < $view['missed_app_num']) ? 'unlocked' : 'locked';
        			
        			if ($status === 'locked') {
                    
                    	$customerRow['unlock_date'] = $this->appointments_model->get_last_missed_appointment_date($customerRow['id']);
                    	$customerRow['unlock_date'] = (isset($customerRow['unlock_date'])) ? (new DateTime($customerRow['unlock_date']))->add(new DateInterval("P" . $app_probation . "M"))->format('Y-m-d 00:00:00') : $today->format('Y-m-d 00:00:00');
                    	$this->customers_model->add($customerRow);
                    	$customerRow['unlock_date_display'] = $this->format_date($customerRow['unlock_date'], $date_format);
                    	
                    
                    } else if ($status === 'unlocked') {
                    	$customerRow['unlock_date'] = '';
                    	$customerRow['unlock_date_display'] = '';
                    	
                    	
                    }
                    
                    $customerRow['status'] = $status;
                    
                
       		}
       	}
        
        
        
        
        $this->set_user_data($view);

        $this->load->view('backend/header', $view);
        $this->load->view('backend/customers', $view);
        $this->load->view('backend/footer', $view);
    }

    /**
     * Displays the backend services page.
     *
     * Here the admin user will be able to organize and create the services
     * that the user will be able to book appointments in frontend.
     *
     * NOTICE: The services that each provider is able to service is managed
     * from the backend services page.
     */
    public function services() {
        $this->session->set_userdata('dest_url', $this->config->item('base_url') . '/index.php/backend/services');
        if (!$this->has_privileges(PRIV_SERVICES)) return;

        $this->load->model('customers_model');
        $this->load->model('services_model');
        $this->load->model('settings_model');
        $this->load->model('user_model');

        $view['base_url'] = $this->config->item('base_url');
        $view['user_display_name'] = $this->user_model->get_user_display_name($this->session->userdata('user_id'));
        $view['active_menu'] = PRIV_SERVICES;
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['date_format'] = $this->settings_model->get_setting('date_format');
        $view['services'] = $this->services_model->get_batch();
        $view['categories'] = $this->services_model->get_all_categories();
        $this->set_user_data($view);

        $this->load->view('backend/header', $view);
        $this->load->view('backend/services', $view);
        $this->load->view('backend/footer', $view);
    }

    /**
     * Display the backend users page.
     *
     * In this page the admin user will be able to manage the system users.
     * By this, we mean the provider, secretary and admin users. This is also
     * the page where the admin defines which service can each provider provide.
     */
    public function users() {
        $this->session->set_userdata('dest_url', $this->config->item('base_url') . '/index.php/backend/users');
        if (!$this->has_privileges(PRIV_USERS)) return;

        $this->load->model('providers_model');
        $this->load->model('secretaries_model');
        $this->load->model('admins_model');
        $this->load->model('services_model');
        $this->load->model('settings_model');
        $this->load->model('user_model');

        $view['base_url'] = $this->config->item('base_url');
        $view['user_display_name'] = $this->user_model->get_user_display_name($this->session->userdata('user_id'));
        $view['active_menu'] = PRIV_USERS;
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['date_format'] = $this->settings_model->get_setting('date_format');
        $view['admins'] = $this->admins_model->get_batch();
        $view['providers'] = $this->providers_model->get_batch();
        $view['secretaries'] = $this->secretaries_model->get_batch();
        $view['services'] = $this->services_model->get_batch();
        $view['working_plan'] = $this->settings_model->get_setting('company_working_plan');
        $this->set_user_data($view);

        $this->load->view('backend/header', $view);
        $this->load->view('backend/users', $view);
        $this->load->view('backend/footer', $view);
    }

    /**
     * Display the user/system settings.
     *
     * This page will display the user settings (name, password etc). If current user is
     * an administrator, then he will be able to make change to the current Easy!Appointment
     * installation (core settings like company name, book timeout etc).
     */
    public function settings() {
        $this->session->set_userdata('dest_url', $this->config->item('base_url') . '/index.php/backend/settings');
        if (!$this->has_privileges(PRIV_SYSTEM_SETTINGS, FALSE)
                && !$this->has_privileges(PRIV_USER_SETTINGS)) return;

        $this->load->model('settings_model');
        $this->load->model('user_model');

        $this->load->library('session');
        $user_id = $this->session->userdata('user_id');

        $view['base_url'] = $this->config->item('base_url');
        $view['user_display_name'] = $this->user_model->get_user_display_name($user_id);
        $view['active_menu'] = PRIV_SYSTEM_SETTINGS;
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['date_format'] = $this->settings_model->get_setting('date_format');
        $view['role_slug'] = $this->session->userdata('role_slug');
        $view['system_settings'] = $this->settings_model->get_settings();
        $view['user_settings'] = $this->user_model->get_settings($user_id);
        $view['book_advance_timeout'] = $this->settings_model->get_setting('book_advance_timeout');
        $this->set_user_data($view);

        $this->load->view('backend/header', $view);
        $this->load->view('backend/settings', $view);
        $this->load->view('backend/footer', $view);
    }

    /**
     * Check whether current user is logged in and has the required privileges to
     * view a page.
     *
     * The backend page requires different privileges from the users to display pages. Not all
     * pages are avaiable to all users. For example secretaries should not be able to edit the
     * system users.
     *
     * @see Constant Definition In application/config/constants.php
     *
     * @param string $page This argument must match the roles field names of each section
     * (eg "appointments", "users" ...).
     * @param bool $redirect (OPTIONAL - TRUE) If the user has not the required privileges
     * (either not logged in or insufficient role privileges) then the user will be redirected
     * to another page. Set this argument to FALSE when using ajax.
     *
     * @return bool Returns whether the user has the required privileges to view the page or
     * not. If the user is not logged in then he will be prompted to log in. If he hasn't the
     * required privileges then an info message will be displayed.
     */
    private function has_privileges($page, $redirect = TRUE) {
        // Check if user is logged in.
        $user_id = $this->session->userdata('user_id');
        if ($user_id == FALSE) { // User not logged in, display the login view.
            if ($redirect) {
                header('Location: ' . $this->config->item('base_url') . '/index.php/user/login');
            }
            return FALSE;
        }

        // Check if the user has the required privileges for viewing the selected page.
        $role_slug = $this->session->userdata('role_slug');
        $role_priv = $this->db->get_where('ea_roles', array('slug' => $role_slug))->row_array();
        if ($role_priv[$page] < PRIV_VIEW) { // User does not have the permission to view the page.
             if ($redirect) {
                header('Location: ' . $this->config->item('base_url') . '/index.php/user/no_privileges');
            }
            return FALSE;
        }

        return TRUE;
    }

    /**
     * This method will update the installation to the latest available
     * version in the server. IMPORTANT: The code files must exist in the
     * server, this method will not fetch any new files but will update
     * the database schema.
     *
     * This method can be used either by loading the page in the browser
     * or by an ajax request. But it will answer with json encoded data.
     */
    public function update() {
        try {
            if (!$this->has_privileges(PRIV_SYSTEM_SETTINGS, TRUE))
                throw new Exception('You do not have the required privileges for this task!');

            $this->load->library('migration');

            if (!$this->migration->current())
                throw new Exception($this->migration->error_string());

            echo json_encode(AJAX_SUCCESS);

        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavaScript($exc))
            ));
        }
    }

    /**
     * Set the user data in order to be available at the view and js code.
     *
     * @param array $view Contains the view data.
     */
    private function set_user_data(&$view) {
        $this->load->model('roles_model');

        // Get privileges
        $view['user_id'] = $this->session->userdata('user_id');
        $view['user_email'] = $this->session->userdata('user_email');
        $view['role_slug'] = $this->session->userdata('role_slug');
        $view['privileges'] = $this->roles_model->get_privileges($this->session->userdata('role_slug'));
    }
    
    /**
    * Return a date of the specified format
    *
    * @param string dateString contains a date as a string
    * @param string dateFormat contains a date format as a string
    */
    private function format_date($dateString, $dateFormat) {
    	date_default_timezone_set ( 'America/New_York' );
    	 
        	
        switch($dateFormat) {
            case 'DMY':
                $result =  (new DateTime($dateString))->format("j/n/Y g:i:s A");
                break;
            case 'MDY':
                $result = (new DateTime($dateString))->format("n/j/Y g:i:s A");
                break;
            case 'YMD':
                $result = (new DateTime($dateString))->format("Y/n/j g:i:s A");
                break;
            default:
                $result = (new DateTime($dateString))->format("n/j/Y g:i:s A");
        }
        return $result;
	}
    
    
}

/* End of file backend.php */
/* Location: ./application/controllers/backend.php */
