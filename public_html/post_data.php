<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "localhost:8888/REST_API/insert_app");
curl_setopt($ch, CURLOPT_POST, 1);


curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('app_id' => '126767144061774', 'secret' => '21db65a65e204cca7b5afcbad91fea60')));

// receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);

var_dump($server_output);
curl_close($ch);
