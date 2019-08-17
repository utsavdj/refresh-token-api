<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mdl_sessions extends MY_Model
{

    public function validate($obj = NULL)
    {

        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('email', $this->lang->line('email'), 'required');

        $this->form_validation->set_rules('password', $this->lang->line('password'), 'required|md5');


        return parent::validate();

    }

}