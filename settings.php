<html>

<body>
  <?php
  // require_once './source/services.inc';
  require_once '/home/fpp/media/plugins/fpp-weather-monitor-plugin/common.inc';

  // $settingService = new SettingsFormService();
  // if (!empty($_POST)) {
  //   $settingService->saveSettings($_POST[NWS_WEATHER_STATION_ID], $_POST[EMAIL_ADDRESS_SETTING], $_POST[WEATHER_DESCRIPTIONS], $_POST[MAX_WIND_SPEED], $_POST[MAX_GUST_SPEED]);
  // }

  if (!empty($_POST))
  {
      $this->setWeatherStationIdSetting($_POST[NWS_WEATHER_STATION_ID]);
      $this->setEmailAddressSetting($_POST[EMAIL_ADDRESS_SETTING]);
      $this->setWeatherTextDescriptionsSetting($_POST[WEATHER_DESCRIPTIONS]);
      $this->setMaxWindSpeedSetting($_POST[MAX_WIND_SPEED]);
      $this->setMaxGustSpeedSetting($_POST[MAX_GUST_SPEED]);
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
          value="<?php echo $settingService->getWeatherStationIdSetting(); ?>" required="required" />
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">Email Address</div>
      <div class="col-md">
        <input class="" type="text" name="<?php echo EMAIL_ADDRESS_SETTING; ?>"
          value="<?php echo $settingService->getEmailAddressSetting(); ?>" required="required" />
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">Weather Text Descriptions</div>
      <div class="col-md">
        <input type="text" name="<?php echo WEATHER_DESCRIPTIONS; ?>"
          value="<?php echo $settingService->getWeatherTextDescriptionsSetting(); ?>" />
      </div>
    </div>

    <h2>Wind</h2>
    <div class="row">
      <div class="col-md-4">
        Max Wind Speed
        <?php echo $settingService->getSpeedUnitText(); ?>
      </div>
      <div class="col-md">
        <input class="" type="number" name="<?php echo MAX_WIND_SPEED; ?>"
          value="<?php echo $settingService->getMaxWindSpeedSetting(); ?>" required="required" />
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">
        Max Gust Speed
        <?php echo $settingService->getSpeedUnitText(); ?>
      </div>
      <div class="col-md">
        <input class="" type="number" name="<?php echo MAX_GUST_SPEED; ?>"
          value="<?php echo $settingService->getMaxGustSpeedSetting(); ?>" required="required" />
      </div>
    </div>

    <button class="buttons" type="submit">Save Settings</button>
  </form>
</body>

</html>