<?php

    // This is a sample validate file where you would 
    
    require 'Nets.php';
    $nets = new Payment();
    

    $response = $nets->validate_response($_GET['transactionId'], $_GET['responseCode']);
    
    if($response == 'cancel')
    {
        return 'User canceled, redirect to cart.';
    }
    
    if(!$response)
    {
        return 'Payment failed. Contact the bank for more details.';
    }
    
    // This is where the actual charging of money is happening.
    if(!$nets->perform_sale($response))
    {
        return 'Payment failed. Contact the bank for more details.';
    }
    
    // Query the transaction for details.
    $data = $nets->perform_query($response);
    