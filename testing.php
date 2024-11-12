<?php 
if (extension_loaded('gd') && function_exists('gd_info')) {
    echo "GD is installed!";
} else {
    echo "GD is NOT installed.";
}
phpinfo();
?>