<?php

defined('MOODLE_INTERNAL') || die;

$capabilities = array(
    'report/randomuserscourses:view' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_PREVENT,
            'student' => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'manager' => CAP_PREVENT,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/site:viewreports'
    )
);
