<?php
/* 
* Script by @Cyril Lopez 
* For display Netatmo informations on Pebble watch
* From Netatmo API
*/ 
    
// Mettre ce paramètre à true la première fois pour afficher la liste des stations et récupérer les adresses MAC pour $device_id et $module_id
$display_list_devices = false; //true or false
	
// Nombre de minutes pour raffraichir les informations affichées
$refresh_frequency = 30;
	
// Indiquez les informations après avoir créer une application sur http://dev.netatmo.com/dev/createapp
$app_id = 'YOUR_APP_ID';
$app_secret = 'YOUR_APP_SECRET';
$username = 'YOUR_USERNAME';
$password = 'YOUR_PASSWORD';
$device_id = "YOUR_DEVICE_ID"; //Adresse Mac station
$module_id = "YOUR_MODULE_ID"; //Adresse Mac module
    
   
    
// Ne rien toucher ci-dessous
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

$api_liste = "https://api.netatmo.net/api/devicelist?access_token=" . $params['access_token'];
$api_url_int = "https://api.netatmo.net/api/getmeasure?access_token=" . $params['access_token'] . "&device_id=" . $device_id . "&scale=max&type=Temperature,CO2,Humidity,Pressure,Noise&date_end=last";
$api_url_ext = "https://api.netatmo.net/api/getmeasure?access_token=" . $params['access_token'] . "&device_id=" . $device_id . "&module_id=" . $module_id . "&scale=max&type=Temperature,CO2,Humidity,Pressure,Noise&date_end=last";
    
if ($display_list_devices == true) 
    	$data_liste = file_get_contents($api_liste);
else {
    	$data_int = json_decode(file_get_contents($api_url_int), true);
    	$data_ext = json_decode(file_get_contents($api_url_ext), true);
}
    
// Configuration des variables 
$temperature_int = $data_int['body'][0]['value'][0][0];
$co2_int = $data_int['body'][0]['value'][0][1];
$humidity_int = $data_int['body'][0]['value'][0][2];
$pressure_int = $data_int['body'][0]['value'][0][3];
$noise_int = $data_int['body'][0]['value'][0][4];
$temperature_ext = $data_ext['body'][0]['value'][0][0];
$humidity_ext = $data_ext['body'][0]['value'][0][2];
	
// Export données au format Json pour l'application Pebble Cards (http://keanulee.com/pebblecards/) sur la montre Pebble
// Le format ci-dessous peut être modifié selon les informations souhaitées (voir variables disponibles ci-dessus)
// Pour afficher toutes les informations dans Pebble Cards, maintenez le bouton select enfoncé
if ($display_list_devices == true) {
	echo 'Ci-dessous, la liste des stations paramétrées sur votre compte<br />';
	echo 'Cette liste permet de récupérer les adresses Mac de vos stations et modules précédées de _id<br />';
	echo 'Pensez à paramétrer la variable $display_list_devices à false une fois la configuration terminée<br />';
	echo '<br />';
	echo $data_liste;
}
else 
	echo '{"content":"NETATMO\nInt : '.$temperature_int.' C\nExt : '.$temperature_ext.' C\nHum : '.$humidity_ext.' %\nCo2 : '.$co2_int.' ppm\nPres : '.$pressure_int.' mb\nSon : '.$noise_int.' db","refresh_frequency":'.$refresh_frequency.'}';
?>
