<?php
    
    require 'Nets.php';
    $nets = new Payment();
    
    $transaction_id = $nets->register_transaction(1, 10000, 'http://myhost.com/validate.php');
    return $nets->launch_terminal($transaction_id);

    // This will open the NETS terminal, then redirect to validate.php file.