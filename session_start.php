<?php
ini_set('session.gc_maxlifetime', 3600);  // Extend session lifespan
ini_set('session.gc_probability', 1);    
ini_set('session.gc_divisor', 1000);      
session_set_cookie_params(3600);
session_start();
?>
