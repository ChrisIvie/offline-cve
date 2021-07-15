<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    </script>
</head>
<nav class="navbar sticky-top navbar-expand-lg navbar-light bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand text-warning" href="https://pottr.io/">Pottr</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav text-warning">
        <li class="nav-item">
          <a class="nav-link active text-light" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item dropdown text-light">
          <a class="nav-link dropdown-toggle text-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Tools</a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                 <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="https://pottr.io/">Failed SSH logins</a></li>
            </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link text-light" aria-current="page" href="https://pottr.io">About</a>
        </li>
      </ul>
      <form method="get" action="search.php" class="form-inline my-2 my-lg-0">
        <input name="stringsearch" class="form-control mr-sm-2" type="search" placeholder="SSH, chrome, ubuntu, etc" aria-label="Search">
        <button class="class=btn btn-warning btn-outline-dark my-2 my-sm-0" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>


<body>



<h3>
Pottr:<small class="container-fluid text-muted">Real time threats</small>
</h3>




<br>

<?php 
/// main database
$db = new SQLite3('../cve.db');
// Count most database rows in main database
$cverows = $db->query("SELECT DISTINCT COUNT(*) as count FROM cveinfo");
while ($cverow = $cverows->fetchArray()){
  $numRows = $cverow['count'];
}


// Start of vertical button group
echo '<div class="btn-group-vertical">';
  echo '<button type="button" class="btn btn-warning btn-outline-dark">';
    echo '<kbd>' . 'CVEs in set:' . '</kbd>'.' <span class="badge badge-success">'. $numRows .'</span>';
    echo '<span class="sr-only">unread messages</span>';
  echo '</button>';
  $res = $db->query('SELECT CVEsinSet FROM cveinfo GROUP BY CVEID ORDER BY CVEURL DESC LIMIT 1');
  while ($row = $res->fetchArray()) {   
    $CVEnumof = json_decode($row['CVEsinSet']);
  }

  //Feed Timestamp
  $res = $db->query('SELECT timestap FROM cveinfo GROUP BY CVEID ORDER BY lastmodidate DESC LIMIT 1');
  while ($row = $res->fetchArray()) {   
    $timestamp = json_decode($row['timestap']);
    echo '<button type="button" class="btn btn-sm btn-warning btn-outline-dark">';
    $twosec = gmdate("Y/m/d H:i:s", strtotime($timestamp));
    echo '<kbd>' . 'Last update:' . '</kbd>'.' <span class="badge badge-success">'. $twosec . " MST" . '</span>';
    echo '<span class="sr-only">unread messages</span>';
    echo '</button>';
  }

  //Start of dropdowns
  echo '<div class="btn-group dropright">'; 
    echo '<button type="button" class="btn btn-warning btn-outline-dark" >';
    //Count Feeds
    $resurl = $db->query('SELECT  COUNT(DISTINCT cveurl) as count FROM cveinfo DESC LIMIT 21');
    while ($row = $resurl->fetchArray()) {   
      $cveurl = $row['count'];
      echo '<kbd>' . 'Feeds:' . '</kbd>';
    }
    echo '<span class="sr-only">unread messages</span>';
    echo '</button>';

    echo '<button type="button" class="btn btn-warning btn-outline-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    echo '<span class="badge badge-success">'. $cveurl .'</span></br>';
    echo '<span class="sr-only">Toggle Dropright</span>';
    echo '</button>';

    echo '<div class="dropdown-menu dropdown-menu-right">';
      //Feed source
      $resource = $db->query('SELECT DISTINCT cveurl FROM cveinfo ORDER BY cveurl DESC LIMIT 21');
      while ($row = $resource->fetchArray()) {   
        $feedsource = json_decode($row['cveurl']);
        //echo $feedsource;
        $feedfile = substr($feedsource, 40, -4); 
        echo '<button type="button" class="btn btn-warning btn-outline-dark" >';
        //echo '<h5 class="card-title">' . $feedfile . '</h5>';
        echo '<h6><kbd>' . $feedfile . '</kbd></h6>';
        echo '</button>';
        //echo '<button type="button" class="btn btn-block btn-warning btn-outline-dark" >';
        //echo '<kbd>' . 'CVEs:' . '</kbd>' .'<span class="badge badge-success">'. $CVEnumof .'</span></br>';
        //echo '</button>';
        //echo '<button type="button" class="btn btn-warning btn-outline-dark" >';
        //echo '<kbd>' . 'Timestamp:' . '</kbd>' .'<span class="badge badge-success">'. $twosec .'</span></br>';
        //echo '</button>';
        //echo '<hr class="solid">';
      }
echo '</div>';
echo '</div>';


// Count most daily database rows
$dbdaily = new SQLite3('../cve-daily.db');
$cverowsdaily = $dbdaily->query("SELECT DISTINCT COUNT(*) as count FROM cveinfo");
while ($cverowdaily = $cverowsdaily->fetchArray()){
  $numRowsdaily = $cverowdaily['count'];
}
// Current CVEs - daily CVEs = Total amount of CVEs added
$newrows24 =   $numRows - $numRowsdaily;
echo '<button type="button" class="btn btn-warning btn-outline-dark" >';
echo '<kbd>' . 'New CVEs (daily):' . '</kbd>'.' <span class="badge badge-success">'. '+' . $newrows24 .'</span>';
echo '</button>';



// Count most recent database rows (weekly)
$dbweekly = new SQLite3('../cve-weekly.db');
$cverowsweekly = $dbweekly->query("SELECT DISTINCT COUNT(*) as count FROM cveinfo");
while ($cverowweekly = $cverowsweekly->fetchArray()){
  $numRowsweekly = $cverowweekly['count'];
}
// Current CVEs - weekly CVEs = Total amount of CVEs added
$newrowsweek = $numRows - $numRowsweekly;
echo '<button type="button" class="btn btn-warning btn-outline-dark" >';
  //echo '<h5 class="card-title">' . $feedfile . '</h5>';
echo '<kbd>' . 'New CVEs (weekly):' . '</kbd>'.' <span class="badge badge-success">'. '+' . $newrowsweek .'</span>';
echo '</button>';

echo '</div>';
echo '</br>';
echo '</br>';



$testingthisshit = (int)$_GET["count"];
if(empty($testingthisshit)){
  $res = $db->query("SELECT * FROM cveinfo GROUP BY CVEID ORDER BY lastmodidate DESC LIMIT 5");
} else {
  $res = $db->query("SELECT * FROM cveinfo GROUP BY CVEID ORDER BY lastmodidate DESC LIMIT $testingthisshit");
}
// Create tables, generate CVE's
while ($row = $res->fetchArray()) {   
  // Grab and store data into vars
  $cveid = json_decode($row['CVEID']);
  $description = json_decode($row['description']);
  $lastmod = json_decode($row['lastmodidate']);
  $basesev = json_decode($row['baseseverity']);
  $refurl = json_decode($row['refurl']);
  $vectorstring = json_decode($row['vectorstring']);
  $attackvector = json_decode($row['attackvector']);
  $attackcomplexity = json_decode($row['attackcomplexity']);
  $privilegesrequired = json_decode($row['privilegesrequired']);
  $userinteraction = json_decode($row['userinteraction']);
  $scope = json_decode($row['scope']);
  $confidentialityimpact = json_decode($row['confidentialityimpact']);
  $integrityimpact = json_decode($row['integrityimpact']);
  $availabilityimpact = json_decode($row['availabilityimpact']);
  $basescore = json_decode($row['basescore']);
  $onesec = gmdate("Y/m/d H:i:s", strtotime($lastmod));
  $refurl = json_decode($row['refurl']);
  
  // Start of tables
  echo '<div class="table-responsive-lg justify-content-md-center">';
  echo '<table class="table-responsive table-dark table-striped table-bordered">';
  echo '<thead>';
  //echo "<kbd>" . $onesec . "</kbd>";
  echo '<tr>';
  if ($basesev == "HIGH") {
    echo '<th scope="col">'. "<h3><kbd>" . $cveid . "</kbd></h3>" . " " .  '<h4><kbd class="text-warning">' . 'Severity: ' . $basesev . "</kbd></h4>";
    echo "<h5><kbd>". "Base Score: " . $basescore . "</kbd></h5>";
    echo ' <a class="" data-toggle="collapse" href="#collapseme'. $cveid .'" role="button" aria-expanded="false" aria-controls="collapseExample">'. "<h5><kbd>" . $vectorstring . "</kbd></h5>" .'</a>';
    echo '<div class="collapse" id="collapseme'. $cveid .'">';
      echo '<div class="card card-body">';
        //Attack Vector information
        if ($attackvector == "NETWORK") {
          echo '<h5><a href="#av'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The vulnerable component is bound to the network stack and the set of possible attackers extends beyond the other options listed below, up to and including the entire Internet. Such a vulnerability is often termed “remotely exploitable” and can be thought of as an attack being exploitable at the protocol level one or more network hops away (e.g., across one or more routers). An example of a network attack is an attacker causing a denial of service (DoS) by sending a specially crafted TCP packet across a wide area network (e.g., CVE‑2004‑0230)."> <kbd>'. "Attack Vector: " . $attackvector . '</kbd></a></h5>';
        } if ($attackvector == "ADJACNT_NETWORK"){
          echo '<h5><a href="#av'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The vulnerable component is bound to the network stack, but the attack is limited at the protocol level to a logically adjacent topology. This can mean an attack must be launched from the same shared physical (e.g., Bluetooth or IEEE 802.11) or logical (e.g., local IP subnet) network, or from within a secure or otherwise limited administrative domain (e.g., MPLS, secure VPN to an administrative network zone). One example of an Adjacent attack would be an ARP (IPv4) or neighbor discovery (IPv6) flood leading to a denial of service on the local LAN segment (e.g., CVE‑2013‑6014)."> <kbd>'. "Attack Vector: " . $attackvector . '</kbd></a></h5>';
        } if ($attackvector === "LOCAL") {
          echo '<h5><a href="#av'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The vulnerable component is not bound to the network stack and the attacker’s path is via read/write/execute capabilities. Either: 1. The attacker exploits the vulnerability by accessing the target system locally (e.g., keyboard, console), or remotely (e.g., SSH); or 2. The attacker relies on User Interaction by another person to perform actions required to exploit the vulnerability (e.g., using social engineering techniques to trick a legitimate user into opening a malicious document)."> <kbd>'. "Attack Vector: " . $attackvector . '</kbd></a></h5>';
        } if ($attackvector == "PHYSICAL"){
          echo '<h5><a href="#av'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The attack requires the attacker to physically touch or manipulate the vulnerable component. Physical interaction may be brief (e.g., evil maid attack[^1]) or persistent. An example of such an attack is a cold boot attack in which an attacker gains access to disk encryption keys after physically accessing the target system. Other examples include peripheral attacks via FireWire/USB Direct Memory Access (DMA)."> <kbd>'. "Attack Vector: " . $attackvector . '</kbd></a></h5>';
        }
        //Attack Complexity infomation
        if ($attackcomplexity == "LOW"){
          echo '<h5><a href="#ac'. $cveid .'" data-toggle="tooltip" data-placement="right" title="Specialized access conditions or extenuating circumstances do not exist. An attacker can expect repeatable success when attacking the vulnerable component."> <kbd>'. "Attack Vector: " . $attackcomplexity . '</kbd></a></h5>';
        }  if ($attackcomplexity == "HIGH"){
          echo '<h5><a href="#ac'. $cveid .'" data-toggle="tooltip" data-placement="right" title="A successful attack depends on conditions beyond the attackers control. That is, a successful attack cannot be accomplished at will, but requires the attacker to invest in some measurable amount of effort in preparation or execution against the vulnerable component before a successful attack can be expected.[^2] For example, a successful attack may depend on an attacker overcoming any of the following conditions: 1. The attacker must gather knowledge about the environment in which the vulnerable target/component exists. For example, a requirement to collect details on target configuration settings, sequence numbers, or shared secrets. 2. The attacker must prepare the target environment to improve exploit reliability. For example, repeated exploitation to win a race condition, or overcoming advanced exploit mitigation techniques. 3. The attacker must inject themselves into the logical network path between the target and the resource requested by the victim in order to read and/or modify network communications (e.g., a man in the middle attack)."> <kbd>'. "Attack Vector: " . $attackcomplexity . '</kbd></a></h5>';
        }
        //Priv required info
        if ($privilegesrequired == "NONE") {
          echo '<h5><a href="#p'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The attacker is unauthorized prior to attack, and therefore does not require any access to settings or files of the the vulnerable system to carry out an attack."> <kbd>'. "Privileges Required: " . $privilegesrequired . '</kbd></a></h5>';
        } if ($privilegesrequired == "LOW") {
          echo '<h5><a href="#p'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The attacker requires privileges that provide basic user capabilities that could normally affect only settings and files owned by a user. Alternatively, an attacker with Low privileges has the ability to access only non-sensitive resources."> <kbd>'. "Privileges Required: " . $privilegesrequired . '</kbd></a></h5>';
        } if ($privilegesrequired == "HIGH"){
          echo '<h5><a href="#p'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The attacker requires privileges that provide significant (e.g., administrative) control over the vulnerable component allowing access to component-wide settings and files."> <kbd>'. "Privileges Required: " . $privilegesrequired . '</kbd></a></h5>';
        }
        //User Interaction info
        if ($userinteraction == "NONE"){
          echo '<h5><a href="#ui'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The vulnerable system can be exploited without interaction from any user."> <kbd>'. "User Interaction: " . $privilegesrequired . '</kbd></a></h5>';
        } if ($userinteraction == "REQUIRED"){
          echo '<h5><a href="#ui'. $cveid .'" data-toggle="tooltip" data-placement="right" title="Successful exploitation of this vulnerability requires a user to take some action before the vulnerability can be exploited. For example, a successful exploit may only be possible during the installation of an application by a system administrator."> <kbd>'. "User Interaction: " . $privilegesrequired . '</kbd></a></h5>';
        }
        //Scope infro
        if ($scope == "UNCHANGED"){
          echo '<h5><a href="#scope'. $cveid .'" data-toggle="tooltip" data-placement="right" title="An exploited vulnerability can only affect resources managed by the same security authority. In this case, the vulnerable component and the impacted component are either the same, or both are managed by the same security authority."> <kbd>'. "Scope: " . $scope . '</kbd></a></h5>';
        } if ($scope == "CHANGED"){
          echo '<h5><a href="#scope'. $cveid .'" data-toggle="tooltip" data-placement="right" title="An exploited vulnerability can affect resources beyond the security scope managed by the security authority of the vulnerable component. In this case, the vulnerable component and the impacted component are different and managed by different security authorities."> <kbd>'. "Scope: " . $scope . '</kbd></a></h5>';
        }
        //Integrity impact info
        if ($integrityimpact == "HIGH"){
          echo '<h5><a href="#ii'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is a total loss of integrity, or a complete loss of protection. For example, the attacker is able to modify any/all files protected by the impacted component. Alternatively, only some files can be modified, but malicious modification would present a direct, serious consequence to the impacted component."> <kbd>'. "Integrity impact: " . $integrityimpact . '</kbd></a></h5>';
        } if ($integrityimpact == "LOW"){
          echo '<h5><a href="#ii'. $cveid .'" data-toggle="tooltip" data-placement="right" title="Modification of data is possible, but the attacker does not have control over the consequence of a modification, or the amount of modification is limited. The data modification does not have a direct, serious impact on the impacted component."> <kbd>'. "Integrity impact: " . $integrityimpact . '</kbd></a></h5>';
        } if ($integrityimpact == "NONE"){
          echo '<h5><a href="#ii'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is no loss of integrity within the impacted component."> <kbd>'. "Integrity impact: " . $integrityimpact . '</kbd></a></h5>';
        }
        if ($confidentialityimpact == "HIGH"){
          echo '<h5><a href="#ii'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is a total loss of confidentiality, resulting in all resources within the impacted component being divulged to the attacker. Alternatively, access to only some restricted information is obtained, but the disclosed information presents a direct, serious impact. For example, an attacker steals the administrators password, or private encryption keys of a web server."> <kbd>'. "Confidentiality impact: " . $confidentialityimpact . '</kbd></a></h5>';
        }
        if ($confidentialityimpact == "LOW"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is some loss of confidentiality. Access to some restricted information is obtained, but the attacker does not have control over what information is obtained, or the amount or kind of loss is limited. The information disclosure does not cause a direct, serious loss to the impacted component."> <kbd>'. "Confidentiality impact: " . $confidentialityimpact . '</kbd></a></h5>';
        } if ($confidentialityimpact == "NONE"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is no loss of confidentiality within the impacted component."> <kbd>'. "Confidentiality impact: " . $confidentialityimpact . '</kbd></a></h5>';
        }
        if ($availabilityimpact == "HIGH"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is a total loss of availability, resulting in the attacker being able to fully deny access to resources in the impacted component; this loss is either sustained (while the attacker continues to deliver the attack) or persistent (the condition persists even after the attack has completed). Alternatively, the attacker has the ability to deny some availability, but the loss of availability presents a direct, serious consequence to the impacted component (e.g., the attacker cannot disrupt existing connections, but can prevent new connections; the attacker can repeatedly exploit a vulnerability that, in each instance of a successful attack, leaks a only small amount of memory, but after repeated exploitation causes a service to become completely unavailable)."> <kbd>'. "Availability impact: " . $availabilityimpact . '</kbd></a></h5>';
        } if ($availabilityimpact == "LOW"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="Performance is reduced or there are interruptions in resource availability. Even if repeated exploitation of the vulnerability is possible, the attacker does not have the ability to completely deny service to legitimate users. The resources in the impacted component are either partially available all of the time, or fully available only some of the time, but overall there is no direct, serious consequence to the impacted component."> <kbd>'. "Availability impact: " . $availabilityimpact . '</kbd></a></h5>';
        } if ($availabilityimpact == "NONE"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is no impact to availability within the impacted component."> <kbd>'. "Availability impact: " . $availabilityimpact . '</kbd></a></h5>';
        }
      echo '</div>';
    echo '</div>';
    echo "<h5><kbd>" . '<a href="'. $refurl .'">Source</a>'. "</kbd></h5>";
    echo "<h5><kbd>" . "Last Modified: " . $onesec . "</kbd></h5>";
    echo '</th>';
  } else {
  if ($basesev == "CRITICAL") {
    echo '<th scope="col">'. "<h3><kbd>" . $cveid . "</kbd></h3>" . " " .  '<h4><kbd class="text-danger">' . 'Severity: ' . $basesev . "</kbd></h4>";
     echo "<h5><kbd>". "Base Score: " . $basescore . "</kbd></h5>";
    echo ' <a class="" data-toggle="collapse" href="#collapseme'. $cveid .'" role="button" aria-expanded="false" aria-controls="collapseExample">'. "<h5><kbd>" . $vectorstring . "</kbd></h5>" .'</a>';
    echo '<div class="collapse" id="collapseme'. $cveid .'">';
      echo '<div class="card card-body">';
        //Attack Vector information
        if ($attackvector == "NETWORK") {
          echo '<h5><a href="#av'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The vulnerable component is bound to the network stack and the set of possible attackers extends beyond the other options listed below, up to and including the entire Internet. Such a vulnerability is often termed “remotely exploitable” and can be thought of as an attack being exploitable at the protocol level one or more network hops away (e.g., across one or more routers). An example of a network attack is an attacker causing a denial of service (DoS) by sending a specially crafted TCP packet across a wide area network (e.g., CVE‑2004‑0230)."> <kbd>'. "Attack Vector: " . $attackvector . '</kbd></a></h5>';
        } if ($attackvector == "ADJACNT_NETWORK"){
          echo '<h5><a href="#av'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The vulnerable component is bound to the network stack, but the attack is limited at the protocol level to a logically adjacent topology. This can mean an attack must be launched from the same shared physical (e.g., Bluetooth or IEEE 802.11) or logical (e.g., local IP subnet) network, or from within a secure or otherwise limited administrative domain (e.g., MPLS, secure VPN to an administrative network zone). One example of an Adjacent attack would be an ARP (IPv4) or neighbor discovery (IPv6) flood leading to a denial of service on the local LAN segment (e.g., CVE‑2013‑6014)."> <kbd>'. "Attack Vector: " . $attackvector . '</kbd></a></h5>';
        } if ($attackvector === "LOCAL") {
          echo '<h5><a href="#av'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The vulnerable component is not bound to the network stack and the attacker’s path is via read/write/execute capabilities. Either: 1. The attacker exploits the vulnerability by accessing the target system locally (e.g., keyboard, console), or remotely (e.g., SSH); or 2. The attacker relies on User Interaction by another person to perform actions required to exploit the vulnerability (e.g., using social engineering techniques to trick a legitimate user into opening a malicious document)."> <kbd>'. "Attack Vector: " . $attackvector . '</kbd></a></h5>';
        } if ($attackvector == "PHYSICAL"){
          echo '<h5><a href="#av'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The attack requires the attacker to physically touch or manipulate the vulnerable component. Physical interaction may be brief (e.g., evil maid attack[^1]) or persistent. An example of such an attack is a cold boot attack in which an attacker gains access to disk encryption keys after physically accessing the target system. Other examples include peripheral attacks via FireWire/USB Direct Memory Access (DMA)."> <kbd>'. "Attack Vector: " . $attackvector . '</kbd></a></h5>';
        }
        //Attack Complexity infomation
        if ($attackcomplexity == "LOW"){
          echo '<h5><a href="#ac'. $cveid .'" data-toggle="tooltip" data-placement="right" title="Specialized access conditions or extenuating circumstances do not exist. An attacker can expect repeatable success when attacking the vulnerable component."> <kbd>'. "Attack Vector: " . $attackcomplexity . '</kbd></a></h5>';
        }  if ($attackcomplexity == "HIGH"){
          echo '<h5><a href="#ac'. $cveid .'" data-toggle="tooltip" data-placement="right" title="A successful attack depends on conditions beyond the attackers control. That is, a successful attack cannot be accomplished at will, but requires the attacker to invest in some measurable amount of effort in preparation or execution against the vulnerable component before a successful attack can be expected.[^2] For example, a successful attack may depend on an attacker overcoming any of the following conditions: 1. The attacker must gather knowledge about the environment in which the vulnerable target/component exists. For example, a requirement to collect details on target configuration settings, sequence numbers, or shared secrets. 2. The attacker must prepare the target environment to improve exploit reliability. For example, repeated exploitation to win a race condition, or overcoming advanced exploit mitigation techniques. 3. The attacker must inject themselves into the logical network path between the target and the resource requested by the victim in order to read and/or modify network communications (e.g., a man in the middle attack)."> <kbd>'. "Attack Vector: " . $attackcomplexity . '</kbd></a></h5>';
        }
        //Priv required info
        if ($privilegesrequired == "NONE") {
          echo '<h5><a href="#p'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The attacker is unauthorized prior to attack, and therefore does not require any access to settings or files of the the vulnerable system to carry out an attack."> <kbd>'. "Privileges Required: " . $privilegesrequired . '</kbd></a></h5>';
        } if ($privilegesrequired == "LOW") {
          echo '<h5><a href="#p'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The attacker requires privileges that provide basic user capabilities that could normally affect only settings and files owned by a user. Alternatively, an attacker with Low privileges has the ability to access only non-sensitive resources."> <kbd>'. "Privileges Required: " . $privilegesrequired . '</kbd></a></h5>';
        } if ($privilegesrequired == "HIGH"){
          echo '<h5><a href="#p'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The attacker requires privileges that provide significant (e.g., administrative) control over the vulnerable component allowing access to component-wide settings and files."> <kbd>'. "Privileges Required: " . $privilegesrequired . '</kbd></a></h5>';
        }
        //User Interaction info
        if ($userinteraction == "NONE"){
          echo '<h5><a href="#ui'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The vulnerable system can be exploited without interaction from any user."> <kbd>'. "User Interaction: " . $privilegesrequired . '</kbd></a></h5>';
        } if ($userinteraction == "REQUIRED"){
          echo '<h5><a href="#ui'. $cveid .'" data-toggle="tooltip" data-placement="right" title="Successful exploitation of this vulnerability requires a user to take some action before the vulnerability can be exploited. For example, a successful exploit may only be possible during the installation of an application by a system administrator."> <kbd>'. "User Interaction: " . $privilegesrequired . '</kbd></a></h5>';
        }
        //Scope infro
        if ($scope == "UNCHANGED"){
          echo '<h5><a href="#scope'. $cveid .'" data-toggle="tooltip" data-placement="right" title="An exploited vulnerability can only affect resources managed by the same security authority. In this case, the vulnerable component and the impacted component are either the same, or both are managed by the same security authority."> <kbd>'. "Scope: " . $scope . '</kbd></a></h5>';
        } if ($scope == "CHANGED"){
          echo '<h5><a href="#scope'. $cveid .'" data-toggle="tooltip" data-placement="right" title="An exploited vulnerability can affect resources beyond the security scope managed by the security authority of the vulnerable component. In this case, the vulnerable component and the impacted component are different and managed by different security authorities."> <kbd>'. "Scope: " . $scope . '</kbd></a></h5>';
        }
        //Integrity impact info
        if ($integrityimpact == "HIGH"){
          echo '<h5><a href="#ii'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is a total loss of integrity, or a complete loss of protection. For example, the attacker is able to modify any/all files protected by the impacted component. Alternatively, only some files can be modified, but malicious modification would present a direct, serious consequence to the impacted component."> <kbd>'. "Integrity impact: " . $integrityimpact . '</kbd></a></h5>';
        } if ($integrityimpact == "LOW"){
          echo '<h5><a href="#ii'. $cveid .'" data-toggle="tooltip" data-placement="right" title="Modification of data is possible, but the attacker does not have control over the consequence of a modification, or the amount of modification is limited. The data modification does not have a direct, serious impact on the impacted component."> <kbd>'. "Integrity impact: " . $integrityimpact . '</kbd></a></h5>';
        } if ($integrityimpact == "NONE"){
          echo '<h5><a href="#ii'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is no loss of integrity within the impacted component."> <kbd>'. "Integrity impact: " . $integrityimpact . '</kbd></a></h5>';
        }
        if ($confidentialityimpact == "HIGH"){
          echo '<h5><a href="#ii'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is a total loss of confidentiality, resulting in all resources within the impacted component being divulged to the attacker. Alternatively, access to only some restricted information is obtained, but the disclosed information presents a direct, serious impact. For example, an attacker steals the administrators password, or private encryption keys of a web server."> <kbd>'. "Confidentiality impact: " . $confidentialityimpact . '</kbd></a></h5>';
        }
        if ($confidentialityimpact == "LOW"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is some loss of confidentiality. Access to some restricted information is obtained, but the attacker does not have control over what information is obtained, or the amount or kind of loss is limited. The information disclosure does not cause a direct, serious loss to the impacted component."> <kbd>'. "Confidentiality impact: " . $confidentialityimpact . '</kbd></a></h5>';
        } if ($confidentialityimpact == "NONE"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is no loss of confidentiality within the impacted component."> <kbd>'. "Confidentiality impact: " . $confidentialityimpact . '</kbd></a></h5>';
        }
        if ($availabilityimpact == "HIGH"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is a total loss of availability, resulting in the attacker being able to fully deny access to resources in the impacted component; this loss is either sustained (while the attacker continues to deliver the attack) or persistent (the condition persists even after the attack has completed). Alternatively, the attacker has the ability to deny some availability, but the loss of availability presents a direct, serious consequence to the impacted component (e.g., the attacker cannot disrupt existing connections, but can prevent new connections; the attacker can repeatedly exploit a vulnerability that, in each instance of a successful attack, leaks a only small amount of memory, but after repeated exploitation causes a service to become completely unavailable)."> <kbd>'. "Availability impact: " . $availabilityimpact . '</kbd></a></h5>';
        } if ($availabilityimpact == "LOW"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="Performance is reduced or there are interruptions in resource availability. Even if repeated exploitation of the vulnerability is possible, the attacker does not have the ability to completely deny service to legitimate users. The resources in the impacted component are either partially available all of the time, or fully available only some of the time, but overall there is no direct, serious consequence to the impacted component."> <kbd>'. "Availability impact: " . $availabilityimpact . '</kbd></a></h5>';
        } if ($availabilityimpact == "NONE"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is no impact to availability within the impacted component."> <kbd>'. "Availability impact: " . $availabilityimpact . '</kbd></a></h5>';
        }
      echo '</div>';
    echo '</div>';
    echo "<h5><kbd>" . '<a href="'. $refurl .'">Source</a>'. "</kbd></h5>";
    echo "<h5><kbd>" . "Last Modified: " . $onesec . "</kbd></h5>";
    echo '</th>';
  } else 
  if ($basesev = "MEDIUM"){
    echo '<th scope="col">'. "<h3><kbd>" . $cveid . "</kbd></h3>" . " " .  '<h4><kbd>' . 'Severity: ' . $basesev . "</kbd></h4>";
     echo "<h5><kbd>". "Base Score: " . $basescore . "</kbd></h5>";
    echo ' <a class="" data-toggle="collapse" href="#collapseme'. $cveid .'" role="button" aria-expanded="false" aria-controls="collapseExample">'. "<h5><kbd>" . $vectorstring . "</kbd></h5>" .'</a>';
    echo '<div class="collapse" id="collapseme'. $cveid .'">';
      echo '<div class="card card-body">';
        //Attack Vector information
        if ($attackvector == "NETWORK") {
          echo '<h5><a href="#av'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The vulnerable component is bound to the network stack and the set of possible attackers extends beyond the other options listed below, up to and including the entire Internet. Such a vulnerability is often termed “remotely exploitable” and can be thought of as an attack being exploitable at the protocol level one or more network hops away (e.g., across one or more routers). An example of a network attack is an attacker causing a denial of service (DoS) by sending a specially crafted TCP packet across a wide area network (e.g., CVE‑2004‑0230)."> <kbd>'. "Attack Vector: " . $attackvector . '</kbd></a></h5>';
        } if ($attackvector == "ADJACNT_NETWORK"){
          echo '<h5><a href="#av'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The vulnerable component is bound to the network stack, but the attack is limited at the protocol level to a logically adjacent topology. This can mean an attack must be launched from the same shared physical (e.g., Bluetooth or IEEE 802.11) or logical (e.g., local IP subnet) network, or from within a secure or otherwise limited administrative domain (e.g., MPLS, secure VPN to an administrative network zone). One example of an Adjacent attack would be an ARP (IPv4) or neighbor discovery (IPv6) flood leading to a denial of service on the local LAN segment (e.g., CVE‑2013‑6014)."> <kbd>'. "Attack Vector: " . $attackvector . '</kbd></a></h5>';
        } if ($attackvector === "LOCAL") {
          echo '<h5><a href="#av'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The vulnerable component is not bound to the network stack and the attacker’s path is via read/write/execute capabilities. Either: 1. The attacker exploits the vulnerability by accessing the target system locally (e.g., keyboard, console), or remotely (e.g., SSH); or 2. The attacker relies on User Interaction by another person to perform actions required to exploit the vulnerability (e.g., using social engineering techniques to trick a legitimate user into opening a malicious document)."> <kbd>'. "Attack Vector: " . $attackvector . '</kbd></a></h5>';
        } if ($attackvector == "PHYSICAL"){
          echo '<h5><a href="#av'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The attack requires the attacker to physically touch or manipulate the vulnerable component. Physical interaction may be brief (e.g., evil maid attack[^1]) or persistent. An example of such an attack is a cold boot attack in which an attacker gains access to disk encryption keys after physically accessing the target system. Other examples include peripheral attacks via FireWire/USB Direct Memory Access (DMA)."> <kbd>'. "Attack Vector: " . $attackvector . '</kbd></a></h5>';
        }
        //Attack Complexity infomation
        if ($attackcomplexity == "LOW"){
          echo '<h5><a href="#ac'. $cveid .'" data-toggle="tooltip" data-placement="right" title="Specialized access conditions or extenuating circumstances do not exist. An attacker can expect repeatable success when attacking the vulnerable component."> <kbd>'. "Attack Vector: " . $attackcomplexity . '</kbd></a></h5>';
        }  if ($attackcomplexity == "HIGH"){
          echo '<h5><a href="#ac'. $cveid .'" data-toggle="tooltip" data-placement="right" title="A successful attack depends on conditions beyond the attackers control. That is, a successful attack cannot be accomplished at will, but requires the attacker to invest in some measurable amount of effort in preparation or execution against the vulnerable component before a successful attack can be expected.[^2] For example, a successful attack may depend on an attacker overcoming any of the following conditions: 1. The attacker must gather knowledge about the environment in which the vulnerable target/component exists. For example, a requirement to collect details on target configuration settings, sequence numbers, or shared secrets. 2. The attacker must prepare the target environment to improve exploit reliability. For example, repeated exploitation to win a race condition, or overcoming advanced exploit mitigation techniques. 3. The attacker must inject themselves into the logical network path between the target and the resource requested by the victim in order to read and/or modify network communications (e.g., a man in the middle attack)."> <kbd>'. "Attack Vector: " . $attackcomplexity . '</kbd></a></h5>';
        }
        //Priv required info
        if ($privilegesrequired == "NONE") {
          echo '<h5><a href="#p'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The attacker is unauthorized prior to attack, and therefore does not require any access to settings or files of the the vulnerable system to carry out an attack."> <kbd>'. "Privileges Required: " . $privilegesrequired . '</kbd></a></h5>';
        } if ($privilegesrequired == "LOW") {
          echo '<h5><a href="#p'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The attacker requires privileges that provide basic user capabilities that could normally affect only settings and files owned by a user. Alternatively, an attacker with Low privileges has the ability to access only non-sensitive resources."> <kbd>'. "Privileges Required: " . $privilegesrequired . '</kbd></a></h5>';
        } if ($privilegesrequired == "HIGH"){
          echo '<h5><a href="#p'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The attacker requires privileges that provide significant (e.g., administrative) control over the vulnerable component allowing access to component-wide settings and files."> <kbd>'. "Privileges Required: " . $privilegesrequired . '</kbd></a></h5>';
        }
        //User Interaction info
        if ($userinteraction == "NONE"){
          echo '<h5><a href="#ui'. $cveid .'" data-toggle="tooltip" data-placement="right" title="The vulnerable system can be exploited without interaction from any user."> <kbd>'. "User Interaction: " . $privilegesrequired . '</kbd></a></h5>';
        } if ($userinteraction == "REQUIRED"){
          echo '<h5><a href="#ui'. $cveid .'" data-toggle="tooltip" data-placement="right" title="Successful exploitation of this vulnerability requires a user to take some action before the vulnerability can be exploited. For example, a successful exploit may only be possible during the installation of an application by a system administrator."> <kbd>'. "User Interaction: " . $privilegesrequired . '</kbd></a></h5>';
        }
        //Scope infro
        if ($scope == "UNCHANGED"){
          echo '<h5><a href="#scope'. $cveid .'" data-toggle="tooltip" data-placement="right" title="An exploited vulnerability can only affect resources managed by the same security authority. In this case, the vulnerable component and the impacted component are either the same, or both are managed by the same security authority."> <kbd>'. "Scope: " . $scope . '</kbd></a></h5>';
        } if ($scope == "CHANGED"){
          echo '<h5><a href="#scope'. $cveid .'" data-toggle="tooltip" data-placement="right" title="An exploited vulnerability can affect resources beyond the security scope managed by the security authority of the vulnerable component. In this case, the vulnerable component and the impacted component are different and managed by different security authorities."> <kbd>'. "Scope: " . $scope . '</kbd></a></h5>';
        }
        //Integrity impact info
        if ($integrityimpact == "HIGH"){
          echo '<h5><a href="#ii'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is a total loss of integrity, or a complete loss of protection. For example, the attacker is able to modify any/all files protected by the impacted component. Alternatively, only some files can be modified, but malicious modification would present a direct, serious consequence to the impacted component."> <kbd>'. "Integrity impact: " . $integrityimpact . '</kbd></a></h5>';
        } if ($integrityimpact == "LOW"){
          echo '<h5><a href="#ii'. $cveid .'" data-toggle="tooltip" data-placement="right" title="Modification of data is possible, but the attacker does not have control over the consequence of a modification, or the amount of modification is limited. The data modification does not have a direct, serious impact on the impacted component."> <kbd>'. "Integrity impact: " . $integrityimpact . '</kbd></a></h5>';
        } if ($integrityimpact == "NONE"){
          echo '<h5><a href="#ii'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is no loss of integrity within the impacted component."> <kbd>'. "Integrity impact: " . $integrityimpact . '</kbd></a></h5>';
        }
        if ($confidentialityimpact == "HIGH"){
          echo '<h5><a href="#ii'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is a total loss of confidentiality, resulting in all resources within the impacted component being divulged to the attacker. Alternatively, access to only some restricted information is obtained, but the disclosed information presents a direct, serious impact. For example, an attacker steals the administrators password, or private encryption keys of a web server."> <kbd>'. "Confidentiality impact: " . $confidentialityimpact . '</kbd></a></h5>';
        }
        if ($confidentialityimpact == "LOW"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is some loss of confidentiality. Access to some restricted information is obtained, but the attacker does not have control over what information is obtained, or the amount or kind of loss is limited. The information disclosure does not cause a direct, serious loss to the impacted component."> <kbd>'. "Confidentiality impact: " . $confidentialityimpact . '</kbd></a></h5>';
        } if ($confidentialityimpact == "NONE"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is no loss of confidentiality within the impacted component."> <kbd>'. "Confidentiality impact: " . $confidentialityimpact . '</kbd></a></h5>';
        }
        if ($availabilityimpact == "HIGH"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is a total loss of availability, resulting in the attacker being able to fully deny access to resources in the impacted component; this loss is either sustained (while the attacker continues to deliver the attack) or persistent (the condition persists even after the attack has completed). Alternatively, the attacker has the ability to deny some availability, but the loss of availability presents a direct, serious consequence to the impacted component (e.g., the attacker cannot disrupt existing connections, but can prevent new connections; the attacker can repeatedly exploit a vulnerability that, in each instance of a successful attack, leaks a only small amount of memory, but after repeated exploitation causes a service to become completely unavailable)."> <kbd>'. "Availability impact: " . $availabilityimpact . '</kbd></a></h5>';
        } if ($availabilityimpact == "LOW"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="Performance is reduced or there are interruptions in resource availability. Even if repeated exploitation of the vulnerability is possible, the attacker does not have the ability to completely deny service to legitimate users. The resources in the impacted component are either partially available all of the time, or fully available only some of the time, but overall there is no direct, serious consequence to the impacted component."> <kbd>'. "Availability impact: " . $availabilityimpact . '</kbd></a></h5>';
        } if ($availabilityimpact == "NONE"){
          echo '<h5><a href="#ci'. $cveid .'" data-toggle="tooltip" data-placement="right" title="There is no impact to availability within the impacted component."> <kbd>'. "Availability impact: " . $availabilityimpact . '</kbd></a></h5>';
        }
      echo '</div>';
    echo '</div>';
    echo "<h5><kbd>" . '<a href="'. $refurl .'">Source</a>'. "</kbd></h5>";
    echo "<h5><kbd>" . "Last Modified: " . $onesec . "</kbd></h5>";
    echo '</th>';

  }
  
};

  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  echo '<tr>';
  echo '<td colspan="3"><p class="h5">' . "Description: " . $description . '</td>';
  echo '</tr>';
  echo '</tbody>';
  echo '</table>';
  echo '</div>';
  echo '<br>';

}
?>

<form method="get" action="index.php">
    <div class="form-row align-items-center">
      <div class="col-auto">
      <div class="form-group">
      <select name="count" class="form-control" id="exampleFormControlSelect1">
        <option>10</option>
        <option>25</option>
        <option>50</option>
        <option>100</option>
        <option>1000</option>
      </select>
    </div>
  </div>
  <div class="col-auto">
      <button type="submit" class="btn btn-warning btn-outline-dark">Load More</button>
  </div>
</form>

