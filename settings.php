<html>

<body>
  <?php
  include_once '/opt/fpp/www/common.php';
  // include_once 'services.inc';
  
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
  $emailAddressFeedback = "test"; // saveEmailAddress($emailAddress);
// $errors = array{"test" , "another one"};
  // $errors = array('first', 'second');
  $errors = array();
  ?>

  <?php foreach ($errors as $error) {
    echo "<div class='p-1 alert detract'>" . $error . "</div>";
  } ?>

  <form method="post">
    <div class="form-group">
      <label for="stationId">Weather Station ID</label>
      <input class="form-control" type="text" name="stationId" value="" required="required" />
      <small id="stationIdHelp" class="form-text text-muted">
        Enter the NWS station ID that is nearest to you.
        This field, if left blank, will use the location set on the
        <a href="/settings.php#settings-system">FPP Settings > System tab</a> to automatically populate
        this field.
      </small>
    </div>
    <button class="buttons" type="submit">Save Settings</button>
  </form>
  <form method="post">
    <div class="form-group">
      <label for="emailAddress">Email Address</label>
      <input class="form-control" type="text" name="emailAddress" value="" required="required"
        placeholder="falconuser@example.com" />
      <small id="emailAddressHelp" class="form-text text-muted">
        Email address is used to uniquely identify you to the NWS API and for you to be contacted if there is a security
        event.
      </small>
    </div>
    <button class="buttons" type="submit">Save Settings</button>

      <!-- 
        todo additional fields to add to form
        
        weather condition string text
        wind speed 
        wind gust 
        min temperature
        max temperature
     -->
    <div class="form-group">
      <label for="weatherTextDescriptions">Weather Text Descriptions</label>
      <input class="form-control" type="text" name="weatherTextDescriptions" value="" />
      <small id="weatherTextDescriptionsHelp" class="form-text text-muted">
        Enter the weather text descriptions, separated by semi-colons, should stop your show.
      </small>
    </div>
    <div class="form-group">
      <label for="windSPeed">Wind Speed</label>
      <input class="form-control" type="number" name="windSpeed" value="" />
      <small id="windSpeedHelp" class="form-text text-muted">
        Enter the wind speed, which the show should be stopped. Leave blank to not stop show for wind
      </small>
    </div>
    <div class="form-group">
      <label for="emailAddress">Wind Gust</label>
      <input class="form-control" type="text" name="emailAddress" value="" required="required"
        placeholder="falconuser@example.com" />
      <small id="emailAddressHelp" class="form-text text-muted">
        Email address is used to uniquely identify you to the NWS API and for you to be contacted if there is a security
        event.
      </small>
    </div>
    <div class="form-group">
      <label for="emailAddress">Min Temperature</label>
      <input class="form-control" type="text" name="emailAddress" value="" required="required"
        placeholder="falconuser@example.com" />
      <small id="emailAddressHelp" class="form-text text-muted">
        Email address is used to uniquely identify you to the NWS API and for you to be contacted if there is a security
        event.
      </small>
    </div>
    <div class="form-group">
      <label for="emailAddress">Max Temperature</label>
      <input class="form-control" type="text" name="emailAddress" value="" required="required"
        placeholder="falconuser@example.com" />
      <small id="emailAddressHelp" class="form-text text-muted">
        Email address is used to uniquely identify you to the NWS API and for you to be contacted if there is a security
        event.
      </small>
    </div>
  </form>
</body>

</html>