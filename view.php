<?php

// DISCLAIMER: As "guest" is also in database, I assumed, that he may be treated as any other user, so also is displayed in table

require_once('../../config.php');

$systemContext = context_system::instance();

require_login();
require_capability('report/randomuserscourses:view', $systemContext);


// ------ SELECT RANDOM USERS IDS ------

$randomUsersMaxCount = 10;

$sql = "SELECT id FROM mdl_user";
// I haven't found a way to get raw SQL data
$userIds = array_map(function ($user) {
    return (int) $user->id;
}, $DB->get_records_sql($sql));
$userIds = array_values($userIds);

$randomIds = [];
// I have assumed that users in the table must be unique
// If there's not enough users, return all their IDs
if (count($userIds) && count($userIds) < $randomUsersMaxCount) {
    $randomIds = $userIds;
}
elseif (count($userIds)) {
    // Select 10 unique random IDs
    while (count($randomIds) < $randomUsersMaxCount) {
        $randomId = $userIds[rand(0, count($userIds) - 1)];
        if ($randomId && !in_array($randomId, $randomIds)) {
            $randomIds[] = $randomId;
        }
    }
}


// ------ SELECT RANDOM USERS AND THEIR COURSES ------

list($inArraySql, $params) = $DB->get_in_or_equal($randomIds);
$sql = "
    SELECT u.username, STRING_AGG(c.shortname, ', ') AS enrolled_courses
    FROM mdl_user u
    LEFT JOIN mdl_user_enrolments ue ON ue.userid = u.id
    LEFT JOIN mdl_enrol e ON e.id = ue.enrolid
    LEFT JOIN mdl_course c ON e.courseid = c.id
    WHERE u.id $inArraySql
    GROUP BY u.id
";
$usersData = $DB->get_records_sql($sql, $params);

$usernameLabel = get_string('username', 'report_randomuserscourses');
$enrolledCoursesLabel = get_string('enrolledcourses', 'report_randomuserscourses');


// ------ PAGE AND OUTPUT ------

$url = new moodle_url('/report/randomuserscourses/index.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title(get_string('title', 'report_randomuserscourses'));
$PAGE->set_heading(get_string('heading', 'report_randomuserscourses'));

echo $OUTPUT->header();

echo <<<END
    <style scoped>
        td, th {
            padding: .5rem;
        }
    </style>

    <table border="1" style="width:100%;padding:">
        <thead>
            <th>$usernameLabel</th>
            <th>$enrolledCoursesLabel</th>
        </thead>
        <tbody>
END;

foreach ($usersData as $userData) {
    $courses = $userData->enrolled_courses ?: '-';
    echo <<<END
        <tr>
            <td>$userData->username</td>
            <td>$courses</td>
        </tr>
END;
}

echo <<<END
        </tbody>
    </table>
END;

echo $OUTPUT->footer();
