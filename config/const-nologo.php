<?php

require_once('connection.php');

date_default_timezone_get();
date_default_timezone_set("Asia/Manila");

//Roles
define("ACCOUNT_TYPE_A","Administrator");
define("ACCOUNT_TYPE_AM","Attendance Manager");
define("ACCOUNT_TYPE_EM","Event Manager");

//Filenames
define('LOGIN_URL', '/aems/login.php');

//Queries Select
define('CURRENT_USER','../model/select/session-user.php');
define('STUDENTS','../model/select/students.php');
define('OFFICERS','../model/select/officers.php');
define('EVENT','../model/select/event.php');
define('SYEAR','../model/select/syear.php');
define('COUNT_REQUEST','../model/select/countRequest.php');

//Queries Insert
define('INSERT_STUDENT','../model/insert/student-record.php');
define('INSERT_OFFICER','../model/insert/officer-record.php');
define('INSERT_USER','../model/insert/user-record.php');
define('INSERT_EVENT','../model/insert/event-record.php');
define('INSERT_EVENT_ATT','../model/insert/eventatt-record.php');
define('INSERT_SYEAR','../model/insert/syear-record.php');

//Queries Delete
define('DELETE_STUDENT','../model/delete/delete-student.php');
define('DELETE_OFFICER','../model/delete/delete-sbo.php');
define('DELETE_USER','../model/delete/delete-user.php');
define('DELETE_EVENT','../model/delete/delete-event.php');
define('DELETE_SYEAR','../model/delete/delete-syear.php');

//Queries Update
define('UPDATE_USER','../model/update/editUser.php');
define('UPDATE_REQUEST_TIME','../model/update/requestTimeUpdate.php');
define('UPDATE_REQUEST_ABSENCE','../model/update/requestAbsenceUpdate.php');
define('UPDATE_OFFICER','../model/update/editOfficer.php');
define('UPDATE_STUDENT','../model/update/editStudent.php');
define('UPDATE_EVENT','../model/update/editEvent.php');
?>