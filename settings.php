<html>

<body>
  <?php
  include_once '/opt/fpp/www/common.php';
  require_once 'common.functions.inc';

  // todo reference
  

  // todo
/// api.weather.gov/points/gps,coordinates   // get gps coordinates from fpp settings page
// then get the properties->observationStations URL from the response
// to go url
  

  function saveEmailAddress(string $emailAddress)
  {
    $isValid = validateEmailAddress($emailAddress);

    writeEmailAddress($emailAddress);
  }

  function validateEmailAddress(string $emailAddress)
  {
    return filter_var($emailAddress, FILTER_VALIDATE_EMAIL);
  }

  // $locationCallSignFeedback = saveLocationCallSign($location);
  $emailAddressFeedback = ""; // saveEmailAddress($emailAddress);
  ?>

  <form method="post">
    <div class="form-group">
      <label for="stationId">Weather Station ID</label>
      <input class="form-control" type="text" name="stationId" value="" required="required" placeholder="KMGM" />
      <small id="stationIdHelp" class="form-text text-muted">
        Enter the NWS station ID that is nearest to you.
        This field, if left blank, will use the location set on the
        <a href="/settings.php#settings-system">FPP Settings > System tab</a> to automatically populate
        this field.
      </small>
    </div>
    <button type="submit">Save Settings</button>
  </form>
  <form method="post">
    <div class="form-group">
      <label for="emailAddress">Email Address</label>
      <input class="form-control" type="text" name="emailAddress" value="" required="required"
        placeholder="falconuser@example.com" />
      <div id="emailAddressFeedback" class="<?php echo $emailAddressFeedback; ?>">Invalid email address</div>
      <small id="emailAddressHelp" class="form-text text-muted">
        Email address is used to uniquely identify you to the NWS API and for you to be contacted if there is a security
        event.
      </small>
    </div>
    <button type="submit">Save Settings</button>
  </form>
</body>

</html>