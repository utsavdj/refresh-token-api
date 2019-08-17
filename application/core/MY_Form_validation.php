<?php if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class MY_Form_validation extends CI_Form_validation
{
    public function set_ci( MX_Controller $ci )
    {
        $this->CI = $ci;
    }
}