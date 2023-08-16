<?php

defined('MOODLE_INTERNAL') || die;

$ADMIN->add(
    'reports', 
    new admin_externalpage(
        'randomuserscourses', 
        get_string('pluginname', 'report_randomuserscourses'), 
        "$CFG->wwwroot/report/randomuserscourses/view.php",
        'report/randomuserscourses:view'
    )
);

// no report settings
$settings = null;
