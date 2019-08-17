<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
class Mdl_logins extends MY_Model {
    public function __construct() {
        parent::__construct();
        $this->table_name = 'sst_logins';
    }
}