<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      Tiffani Singley
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Appointment_status Model
 *
 * @package Models
 */
class Appointment_Status_Model extends CI_Model {
    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Add a appointment_status record to the database.
     *
     * This method adds an appointment_status to the database. If the appointment_status
     * doesn't exists it is going to be inserted, otherwise the
     * record is going to be updated.
     *
     * @param array $status Associative array with the appointment status
     * data. Each key has the same name with the database fields.
     * @return int Returns the appointment_status id.
     */
    public function add($status) {
        // Validate the appointment_status data before doing anything.
        $this->validate($status); 

        // :: CHECK IF APPOINTMENT_STATUS ALREADY EXIST
        if ($this->exists($status) && !isset($status['id'])) {
        	// Find the status id from the database.
        	$status['id'] = $this->find_record_id($status);
        }

        // :: INSERT OR UPDATE APPOINTMENT_STATUS RECORD
        if (!isset($status['id'])) {
            $status['id'] = $this->insert($status);
        } else {
            $this->update($status);
        }

        return $status['id'];
    }

    /**
     * Check if a particular appointment_status record already exists.
     *
     * This method checks wether the given appointment_status record already exists in
     * the database. It doesn't search with the id, but with the following
     * fields: "ea_appointment_id, ea_user_id"
     *
     * @param array $status Associative array with the appointment_status
     * data. Each key has the same name with the database fields.
     * @return bool Returns whether the record exists or not.
     */
    public function exists($status) {
        if (!isset($status['ea_appointment_id']) ||
        		!isset($status['ea_user_id'])) {
            throw new Exception('appointment_status id is not provided.');
        }

        // This method shouldn't depend on another method of this class.
        $num_rows = $this->db
                ->select('*')
                ->from('appointment_status')
                ->where('appointment_status.ea_appointment_id', $status['ea_appointment_id'])
                ->where('appointment_status.ea_user_id', $status['ea_user_id'])
                ->get()->num_rows();

        return ($num_rows > 0) ? TRUE : FALSE;
    }

    /**
     * Insert a new customer record to the database.
     *
     * @param array $status Associative array with the customer's
     * data. Each key has the same name with the database fields.
     * @return int Returns the id of the new record.
     */
    private function insert($status) {

        if (!$this->db->insert('appointment_status', $status)) {
            throw new Exception('Could not insert appointment_status to the database.');
        }

        return intval($this->db->insert_id());
    }

    /**
     * Update an existing appointment_status record in the database.
     *
     * The appointment_status data argument should already include the record
     * id in order to process the update operation.
     *
     * @param array $status Associative array with the appointment_status
     * data. Each key has the same name with the database fields.
     * @return int Returns the updated record id.
     */
    private function update($status) {
        // Do not update empty string values.
        foreach ($status as $key => $value) {
            if ($value === '')
                unset($status[$key]);
        }

        $this->db->where('id', $status['id']);
        if (!$this->db->update('appointment_status', $status)) {
            throw new Exception('Could not update appointment_status to the database.');
        }

        return intval($status['id']);
    }



    /**
     * Find the database id of a appointment_status record.
     *
     * The appointment_status data should include the following fields in order to
     * get the unique id from the database: "ea_appointment_id, ea_user_id"
     *
     * <strong>IMPORTANT!</strong> The record must already exists in the
     * database, otherwise an exception is raised.
     *
     * @param array $status Array with the appointment_status data. The
     * keys of the array should have the same names as the db fields.
     * @return int Returns the id.
     */
    public function find_record_id($status) {
        if (!isset($status['ea_appointment_id']) ||
        	!isset($status['ea_user_id'])) {
            throw new Exception('appointment_status id was not provided : '
                    . print_r($status, TRUE));
        }

        // Get appointment_status role id
        $result = $this->db
                ->select('appointment_status.id')
                ->from('appointment_status')
                ->where('appointment_status.ea_appointment_id', $status['ea_appointment_id'])
                ->where('appointment_status.ea_user_id', $status['ea_user_id']) 
                ->get();

        if ($result->num_rows() == 0) {
            throw new Exception('Could not find appointment_status record id.');
        }

        return $result->row()->id;
    }

    /**
     * Validate appointment_status data before the insert or update operation is executed.
     *
     * @param array $status Contains the appointment status data.
     * @return bool Returns the validation result.
     */
    public function validate($status) {
        $this->load->helper('data_validation');

        // If a appointment_status id is provided, check whether the record
        // exist in the database.
        if (isset($status['id'])) {
            $num_rows = $this->db->get_where('appointment_status',
                    array('id' => $status['id']))->num_rows();
            if ($num_rows == 0) {
                throw new Exception('Provided appointment_status id does not '
                        . 'exist in the database.');
            }
        }
        // Validate required fields
        if (!isset($status['ea_appointment_id'])
                || !isset($status['ea_user_id'])
                || !isset($status['status'])) {
            throw new Exception('Not all required fields are provided : '
                    . print_r($status, TRUE));
        }

        return TRUE;
    }


    /**
     * Delete an existing customer record from the database.
     *
     * @param numeric $customer_id The record id to be deleted.
     * @return bool Returns the delete operation result.
     */
     
     /*
    public function delete($customer_id) {
        if (!is_numeric($customer_id)) {
            throw new Exception('Invalid argument type $customer_id : ' . $customer_id);
        }

        $num_rows = $this->db->get_where('ea_users', array('id' => $customer_id))->num_rows();
        if ($num_rows == 0) {
            return FALSE;
        }

        return $this->db->delete('ea_users', array('id' => $customer_id));
    }
    
    */

    /**
     * Get a specific row from the appointments table.
     *
     * @param numeric $appointment_status_id The record's id to be returned.
     * @return array Returns an associative array with the selected
     * record's data. Each key has the same name as the database
     * field names.
     */
    public function get_row($status_id) {
        if (!is_numeric($status_id)) {
            throw new Exception('Invalid argument provided as $status_id : ' . $status_id);
        }
        return $this->db->get_where('appointment_status', array('id' => $status_id))->row_array();
    }
    
    
    /**
    * Get a row by the unique field combination of ea_appointment_id and ea_user_id
    */
    public function get_row_by_unique($appointment_id, $user_id) {
        if (!is_numeric($appointment_id) || !is_numeric($user_id)) {
            throw new Exception('Invalid argument provided as $appointment_id : ' . $appointment_id . 'Invalid argument provided as $user_id : ' . $user_id);
        }
        
        return  $this->db->get_where('appointment_status', array('ea_appointment_id' => $appointment_id, 'ea_user_id' => $user_id))->row_array();
        
    }

    /**
     * Get a specific field value from the database.
     *
     * @param string $field_name The field name of the value to be
     * returned.
     * @param int $status_id The selected record's id.
     * @return string Returns the records value from the database.
     */
    public function get_value($field_name, $status_id) {
        if (!is_numeric($status_id)) {
            throw new Exception('Invalid argument provided as $status_id : '
                    . $status_id);
        }

        if (!is_string($field_name)) {
            throw new Exception('$field_name argument is not a string : '
                    . $field_name);
        }

        if ($this->db->get_where('appointment_status', array('id' => $status_id))->num_rows() == 0) {
            throw new Exception('The record with the $status_id argument '
                    . 'does not exist in the database : ' . $status_id);
        }

        $row_data = $this->db->get_where('appointment_status', array('id' => $status_id)
                )->row_array();
        if (!isset($row_data[$field_name])) {
            throw new Exception('The given $field_name argument does not'
                    . 'exist in the database : ' . $field_name);
        }

        $status = $this->db->get_where('appointment_status', array('id' => $status_id))->row_array();

        return $status[$field_name];
    }

    /**
     * Get all, or specific records from appointment's table.
     *
     * @example $this->Model->getBatch('id = ' . $recordId);
     *
     * @param string $whereClause (OPTIONAL) The WHERE clause of
     * the query to be executed. DO NOT INCLUDE 'WHERE' KEYWORD.
     * @return array Returns the rows from the database.
     */
     /*
    public function get_batch($where_clause = '') {
        $customers_role_id = $this->get_customers_role_id();

        if ($where_clause != '') {
            $this->db->where($where_clause);
        }

        $this->db->where('id_roles', $customers_role_id);

        return $this->db->get('ea_users')->result_array();
    }
    */
    
    public function get_missed_appointments($user_id) {
    
    	// get missed appointment records
        $result = $this->db
                ->select('appointment_status.id')
                ->from('appointment_status')
                ->where('appointment_status.status', 'missed')
                ->where('appointment_status.ea_user_id', $user_id)
                ->get();


        return $result->num_rows();
    
    }

}

/* End of file customers_model.php */
/* Location: ./application/models/customers_model.php */
