<?php
    /* Script Cyril Lopez 
    * Display weather informations from Netatmo station
    * This script use the api from Netatmo
    * Please configure it with Netatmo developper information
    */
    
    # Configure this
    $app_id = 'YOUR_APP_ID';
    $app_secret = 'YOUR_APP_SECRET';
    $username = 'YOUR_USERNAME';
    $password = 'YOUR_PASSWORD';
    $device_id = "YOUR_DEVICE_ID"; //for internal module
    $module_id = "YOUR_MODULE_ID"; //for external module
    
    $token_url = "https://api.netatmo.net/oauth2/token";
	  $postdata = http_build_query(
        array(
            'grant_type' => "password",
            'client_id' => $app_id,
            'client_secret' => $app_secret,
            'username' => $username,
            'password' => $password
        )
    );

    $opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
    );

    $context  = stream_context_create($opts);

    $response = file_get_contents($token_url, false, $context);
    $params = null;
    $params = json_decode($response, true);

    $api_url_int = "https://api.netatmo.net/api/getmeasure?access_token=" . $params['access_token'] . "&device_id=" . $device_id . "&scale=max&type=Temperature,CO2,Humidity,Pressure,Noise&date_end=last";
    $api_url_ext = "https://api.netatmo.net/api/getmeasure?access_token=" . $params['access_token'] . "&device_id=" . $device_id . "&module_id=" . $module_id . "&scale=max&type=Temperature,CO2,Humidity,Pressure,Noise&date_end=last";
    
    $data_int = json_decode(file_get_contents($api_url_int), true);
    $data_ext = json_decode(file_get_contents($api_url_ext), true);
    
    # Netatmo station datas
    $temperature_int = $data_int['body'][0]['value'][0][0];
    $co2_int = $data_int['body'][0]['value'][0][1];
    $humidity_int = $data_int['body'][0]['value'][0][2];
    $pressure_int = $data_int['body'][0]['value'][0][3];
    $noise_int = $data_int['body'][0]['value'][0][4];
    $temperature_ext = $data_ext['body'][0]['value'][0][0];
    $humidity_ext = $data_ext['body'][0]['value'][0][2];
	
    # This is the Json line data display for external use
    # You can configure this with informations that you want to extract
    echo '{"content":"NETATMO\nInt : '.$temperature_int.' C\nExt : '.$temperature_ext.' C","refresh_frequency":30}';

?>
