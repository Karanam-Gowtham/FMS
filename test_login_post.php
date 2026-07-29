<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/mini/FMS/admin/admins.php?dept=AI_DS");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "userid=gowtham.lite@gmail.com&password=123&signIn=");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
curl_close($ch);
echo $response;
?>
