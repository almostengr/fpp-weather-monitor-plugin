<?php
require_once '/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/SettingService.php';

$settingService = new SettingService();
$errors = array();

if (!empty($_POST)) {
  array_push($errors, $settingService->createUpdateSetting(NWS_WEATHER_STATION_ID, $_POST[NWS_WEATHER_STATION_ID]));
  array_push($errors, $settingService->createUpdateSetting(EMAIL_ADDRESS_SETTING, $_POST[EMAIL_ADDRESS_SETTING]));
  array_push($errors, $settingService->createUpdateSetting(WEATHER_DESCRIPTIONS, $_POST[WEATHER_DESCRIPTIONS]));
  array_push($errors, $settingService->createUpdateSetting(MAX_WIND_SPEED, $_POST[MAX_WIND_SPEED]));
  array_push($errors, $settingService->createUpdateSetting(MAX_GUST_SPEED, $_POST[MAX_GUST_SPEED]));
}
?>

<html>

<body>
  <?php
  $succeeded = 0;
  foreach ($errors as $error) {
    if ($error !== true && !empty($error)) {
      echo "<div class='p-1 alert bg-danger text-white font-weight-bold'>" . $error . "</div>";
      continue;
    }
    $succeeded++;
  }

  if (!empty($_POST) && sizeof($errors) == $succeeded) {
    echo "<div class='p-1 alert bg-success text-white font-weight-bold'>Settings saved successfully</div>";
  }
  ?>

  <form method="post">
    <div class="row my-3">
      <div class="col-md-2 text-center">Donate</div>
      <div class="col-md">
        Enjoy using this plugin? Please make a donation to support the future of this plugin.
      </div>
    </div>

    <div class="row my-3">
      <div class="col-md-2 text-center">NWS Weather Station ID</div>
      <div class="col-md">
        <input type="text" name="<?php echo NWS_WEATHER_STATION_ID; ?>"
          value="<?php echo $settingService->getSetting(NWS_WEATHER_STATION_ID); ?>" required="required" />
        <div class="text-muted">
          Identifier for the weather station closest to your location.
          Enter "0000" to automatically populate the closest weather station using the GPS coordinates entered
          on <a href="/settings.php#settings-system">Status/Control > FPP Settings > System tab</a>.
        </div>
      </div>
    </div>

    <div class="row my-3">
      <div class="col-md-2 text-center">Email Address</div>
      <div class="col-md">
        <input type="text" name="<?php echo EMAIL_ADDRESS_SETTING; ?>"
          value="<?php echo $settingService->getSetting(EMAIL_ADDRESS_SETTING); ?>" required="required" />
        <div class="text-muted">
          Enter the email address that will be used to identify your API calls made to the National Weather Service API.
          If there are issues or concerns with the APIs calls that are made or
          if there is a security event, you will be notified by the NWS at the email address entered.
        </div>
      </div>
    </div>

    <div class="row my-3">
      <div class="col-md-2 text-center">Weather Text Descriptions</div>
      <div class="col-md">
        <input type="text" name="<?php echo WEATHER_DESCRIPTIONS; ?>"
          value="<?php echo $settingService->getSetting(WEATHER_DESCRIPTIONS); ?>" class="form-control" />
        <div class="text-muted">
          Enter the weather descriptions, that if current, the monitor will stop your show.
        </div>
        <div class="text-muted">
          List of descriptions include, but are not limited to, "showers", "rain", "thunderstorms",
          "clear", "sunny", and "partly cloudy".
        </div>
      </div>

      <div class="row my-3">
        <div class="col-md-2 text-center">
          Max Wind Speed
          <?php echo $settingService->getSpeedUnitText(); ?>
        </div>
        <div class="col-md">
          <input type="number" name="<?php echo MAX_WIND_SPEED; ?>"
            value="<?php echo $settingService->getSetting(MAX_WIND_SPEED); ?>" required="required" />
          <div class="text-muted">
            Enter the maximum wind speed that, if exceeded, the monitor will stop your show.
          </div>
        </div>
      </div>

      <div class="row my-3">
        <div class="col-md-2 text-center">
          Max Wind Gust Speed
          <?php echo $settingService->getSpeedUnitText(); ?>
        </div>
        <div class="col-md">
          <input type="number" name="<?php echo MAX_GUST_SPEED; ?>"
            value="<?php echo $settingService->getSetting(MAX_GUST_SPEED); ?>" required="required" />
          <div class="text-muted">
            Enter the maximum wind gust speed that, if exceeded, the monitor will stop your show.
          </div>
        </div>
      </div>

      <button class="buttons my-3" type="submit">Save Settings</button>
  </form>
</body>

</html>