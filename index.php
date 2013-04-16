<?php

  // STEP 1: Authentication

  // create a curl handle with the login url
  $curlHandle = curl_init("http://api.project-ginsberg.com:2404/users/login");

  // construct an associative array with the username and password
  // you can see that this contains Mr. Ginsberg's username and password
  $data = array(
    "username" => "allen@project-ginsberg.com",
    "password" => "howl"
  );

  // encode the associative array into json
  $jsonData = json_encode($data);

  // set the request type to post and the postfields to our json data
  curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST,   "POST");
  curl_setopt($curlHandle, CURLOPT_POSTFIELDS,      $jsonData);
  curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER,  true);

  // When sending json, we should set the content type to JSON,
  // and fill out the content-length field
  curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonData))
  );

  // finally, after having set up the call to the API, we run it
  $result = curl_exec($curlHandle);

  // each call to the API returns an HTTP status code,
  // which we can use to determine if the call was succesful
  $status = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

  // and if your call should return some json data, you can decode it from
  // the $result variable
  $resultObject = json_decode($result);

  // close your curl handles
  curl_close($curlHandle);

  // STEP 2: Fetching the User's Account

  // initialise a second curl handle, this time with the endpoint being Mr. Ginsberg's uid,
  // this will fetch Mr. Ginsberg's record from the users collection
  $curlHandle = curl_init("http://api.project-ginsberg.com:2404/users/".$resultObject->uid);

  // but we can't let just anyone fetch Mr. Ginsbergs email address, and first and last name,
  // so we have to send along a cookie to authenticate with the API
  // if you print out the $resultObject's properties, you'll see that the process of
  // logging in generated a cookie session id, which we are now going to send along
  // with the call
  curl_setopt($curlHandle, CURLOPT_COOKIE, "sid=".$resultObject->id."; ");
  // you don't need to send the authentication cookie along with every call,
  // but you do need to send it along for most calls

  $output = curl_exec($curlHandle);
  $status = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
  curl_close($curlHandle);

  // STEP 3: Fetch a Project Ginsberg Health Record

  // initialise a third curl handle
  // this time the endpoint is the obj-fitness collection, but there's also a query being sent
  // in the URL in the form of JSON
  $curlHandle = curl_init("http://api.project-ginsberg.com:2404/obj-fitness?{\"uid\":\"".$resultObject->uid."\"}");

  // you won't be able to search collections by uid or see the uid's in them unless you send authentication
  curl_setopt($curlHandle, CURLOPT_COOKIE, "sid=".$resultObject->id."; ");
  $output = curl_exec($curlHandle);
  $status = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
  echo("<h4>".$status."</h4>");
  curl_close($curlHandle);

  // STEP 4: Posting a Record

  // compile your fields into an associative array
  $testFitnessData = array(
    "uid" => $resultObject->uid,
    "startTimestamp" => time(),
    "endTimestamp" => time() + 60,
    "type" => "Snowboarding"
  );

  // encode it into json
  $testFitnessDataJson = json_encode($testFitnessData);

  // initialise a final curl handler, the endpoint being the collection
  // to which you want to post
  $curlHandle = curl_init("http://api.project-ginsberg.com:2404/obj-fitness");

  // you will not be able to associate a record with a user unless you send authentication
  curl_setopt($curlHandle, CURLOPT_COOKIE,          "sid=".$resultObject->id."; ");

  curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST,   "POST");
  curl_setopt($curlHandle, CURLOPT_POSTFIELDS,      $testFitnessDataJson);
  curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER,  true);
  curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($testFitnessDataJson))
  );

  // finally, commit the post
  $output = curl_exec($curlHandle);
  $status = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
  curl_close($curlHandle);
?>
