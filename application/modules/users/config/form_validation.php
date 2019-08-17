<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$config = array(
        array(
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'trim|required|valid_email|callback_check_unique_email',
				'errors'=>array(
					'required' => '%s is required.',
					'check_unique_email'=>'%s is not available'
				)
        ),
		array(
			'field'=>'status',
			'label'=>'Status',
			'rules'=>'required',
			'errors'=>array(
					'required' => '%s is required.'
				)
		),
		array(
			'field'=>'usergroup',
			'label'=>'User group',
			'rules'=>'required|greater_than[0]',
			'errors'=>array(
					'required' => '%s is required.',
					'greater_than' => '%s is required.'
				)
		)
);
