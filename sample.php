<?php
    
    require 'Nets.php';
    $nets = new Payment();
    
    // Livstidsmedlemskap
    $transaction_id = $nets->register_transaction(1, 300000, 'http://localhost/nets-php/validate.php');
    $nets->launch_terminal($transaction_id);
    
    
    // Membership
    $transaction_id = $nets->register_new_recurring_payment(1, 20000, 'http://localhost/nets-php/validate.php', '2014-01-01');
    $nets->launch_terminal($transaction_id);
    
    
    // Continue with membership
    $pan_hash = 'QfjgQrZ9/wf07TUBjl32OiN7AG0=';
    
    $transaction_id = $nets->register_recurring_payment(2, 20000, $pan_hash);
    
    if(!$nets->perform_sale($response))
    {
        echo 'Betaling feilet. Vennligst kontakt din kortutsteder.';
        exit();
    }
    
    $data = $nets->perform_query($response);