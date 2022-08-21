<?php
// Authentifizierung
$sAuth = 1;                          // 0 (Aus) / 1 (Ein)
$username = "<my-cool-username>";    // used if script authentication is enabled 
$password = "<my-secret-password>";

// Cloudflare DNS API V4 - Update-Zugangsdaten
$email = "<my-emailadress>";                  // Cloudflare Login
$apiKey = "<my-cloudflare-api-key>";          // Cloudflare API Key

// DNS Record options
$enableProxy = false;  // Toggle Cloudflare proxying for the address | false=aus / true=ein
$ttl = 1;              // TTL in seconds (1 for auto, or a value greater than 120)

// Prüfe Login & Passwort
if ($sAuth == 1) {
  if ($_REQUEST['user'] != $username || $_REQUEST['pass'] != $password) {
    exit ("Wrong credentials!");
  }
}

// Check minimum parameter requirements
if (!$_REQUEST['domain'] || !$_REQUEST['ipv4']) {
  exit ("Ähm, es ist ein Fehler aufgetreten!? Laufe im Kreis und schrei' um Hilfe!");
}

$zone = explode('.', $_REQUEST['domain'], 2)[1];  // Domain
$dnsRecord = $_REQUEST['domain'];                 // Subdomain 
$ip4 = $_REQUEST['ipv4'];                         // IPv4
if ($_REQUEST['ipv6']) {
  $ip6 = $_REQUEST['ipv6'];                       // IPv6
}

{ // Zone holen
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 20,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "X-Auth-Email: ".$email,
    "X-Auth-Key: ".$apiKey
  ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  exit ("cURL Error #:" . $err);
} else {
  $obj = json_decode($response,true);
  if( $obj["success"]!=1)
  {
    exit ("GetZoneId Failed");
  }
  $zones =($obj['result']);
  foreach($zones as $zoneResult) {
    if($zoneResult['name']==$zone)
        $zoneId = $zoneResult['id'];
  }
  if ($zoneId =="") {
    exit ("Zone not found!");
  }
}
}

{ // ID holen
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/".$zoneId."/dns_records",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 20,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Cache-Control: no-cache",
    "X-Auth-Email: ".$email,
    "X-Auth-Key: ".$apiKey
  ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  exit ("cURL Error #:" . $err);
} else {
  $obj = json_decode($response,true);
  if( $obj["success"]!=1) {
      exit ("GetRecordID Failed");
  }
  $zones =($obj['result']);
  foreach($zones as $zoneResult) {
      if($zoneResult['name'] == $dnsRecord)
          $recordId[$zoneResult['type']] = $zoneResult['id'];
  }
}
}

{ // Eintrag aktualisieren
function updateCloudflare($zid, $rid, $eml, $aky, $d, $opr) {
  if ($rid!="") {
    $rid = "/".$rid;
    if ($opr!="DELETE") {
        $opr = "PUT";
    }
  }
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/".$zid."/dns_records".$rid,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => $opr,
    CURLOPT_HTTPHEADER => array(
      "Cache-Control: no-cache",
      "Content-Type: application/json",
      "X-Auth-Email:".$eml,
      "X-Auth-Key: ".$aky
    )
  ));
  if ($opr!="DELETE") {
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($d));
  }
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);
  if ($err) {
    exit ("cURL Error #:" . $err);
  } else {
    return json_decode($response);
  }
} // end function
// Mach Deinen Job...!
$data = array (
  'type' => 'A',
  'name' => $dnsRecord,
  'content' => $ip4,
  'proxiable' => true,
  'proxied' => $enableProxy,
  'ttl' => $ttl,
  'locked' => false,
  'zone_id' => $zoneId,
  'zone_name' => $zone
);
updateCloudflare($zoneId, $recordId['A'], $email, $apiKey, $data, "POST");
if ($ip6) {
  $data['type'] = 'AAAA';
  $data['content'] = $ip6;
  updateCloudflare($zoneId, $recordId['AAAA'], $email, $apiKey, $data, "POST");
} elseif ($recordId['AAAA']) { // Oh mein Gott - Du hast das Internet gelöscht! Ahh!
  updateCloudflare($zoneId, $recordId['AAAA'], $email, $apiKey, $data, "DELETE");
}
} Awesome!
?>
