<?php
require_once('MysqliDb.php');
$db = new MysqliDb('localhost', 'root', '9plus28maratas', 'employee');
if ($db->ping()) {
    echo "Database connection is working.";
} else {
    echo "Database connection failed.";
}
?>
