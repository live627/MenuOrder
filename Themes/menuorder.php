<?php
ob_start("ob_gzhandler");
header("Content-Type: text/javascript");
echo file_get_contents("menuorder.js");
ob_end_flush();
?>