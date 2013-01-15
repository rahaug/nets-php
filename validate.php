<?php
    
    require 'Nets.php';
    $nets = new Payment();
    

    $response = $nets->validate_response();
    
    if($response == 'cancel')
    {
        echo 'User canceled';
        exit();
    }
    
    if(!$response)
    {
        echo 'Betaling feilet. Vennligst kontakt din kortutsteder.';
        exit();
    }
    
    if(!$nets->perform_sale($response))
    {
        echo 'Betaling feilet. Vennligst kontakt din kortutsteder.';
        exit();
    }
    
    $data = $nets->perform_query($response);
    