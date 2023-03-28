<html>

<body>
  <?php
  include_once './services.inc';
  
  $settingService = new SettingsFormService();
  if (!empty($_POST))
  {
    $settingService->saveSettings($_POST[NWS_WEATHER_STATION_ID], $_POST[EMAIL_ADDRESS_SETTING], $_POST[WEATHER_DESCRIPTIONS], $_POST[MAX_WIND_SPEED], $_POST[MAX_GUST_SPEED]);
  }
  ?>

  <?php foreach ($settingService->getErrors() as $error) {
    echo "<div class='p-1 alert detract'>" . $error . "</div>";
  } ?>

  <form method="post">
    <h2>General</h2>
    <div class="row">
      <div class="col-md-4">NWS Weather Station ID</div>
      <div class="col-md">
        <input class="" type="text" name="<?php echo NWS_WEATHER_STATION_ID; ?>"
          value="<?php echo $settingService->getWeatherStationId(); ?>" required="required" />
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">Email Address</div>
      <div class="col-md">
        <input class="" type="text" name="<?php echo EMAIL_ADDRESS_SETTING; ?>"
          value="<?php echo $settingService->getEmailAddress(); ?>" required="required" />
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">Weather Text Descriptions</div>
      <div class="col-md">
        <input type="text" name="<?php echo WEATHER_DESCRIPTIONS; ?>"
          value="<?php echo $settingService->getWeatherTextDescriptions(); ?>" />
      </div>
    </div>

    <h2>Wind</h2>
    <div class="row">
      <div class="col-md-4">Max Wind Speed <?php echo $settingService->getSpeedUnitText(); ?></div>
      <div class="col-md">
        <input class="" type="number" name="<?php echo MAX_WIND_SPEED; ?>"
          value="<?php echo $settingService->getMaxWindSpeed(); ?>" />
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">Max Gust Speed <?php echo $settingService->getSpeedUnitText(); ?></div>
      <div class="col-md">
        <input class="" type="number" name="<?php echo MAX_GUST_SPEED; ?>"
          value="<?php echo $settingService->getMaxGustSpeed(); ?>" />
      </div>
    </div>

    <button class="buttons" type="submit">Save Settings</button>
  </form>
</body>

</html>