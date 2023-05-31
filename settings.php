<?php

require_once '/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/SettingService.php';

$settingRepository = new SettingRepository();
$settingService = new SettingService($settingRepository);

$errors = array();

if (!empty($_POST)) {
  array_push($errors, $settingService->createUpdateSetting(NWS_WEATHER_STATION_ID, $_POST[NWS_WEATHER_STATION_ID]));
  array_push($errors, $settingService->createUpdateSetting(EMAIL_ADDRESS_SETTING, $_POST[EMAIL_ADDRESS_SETTING]));
  array_push($errors, $settingService->createUpdateSetting(WEATHER_DESCRIPTIONS, $_POST[WEATHER_DESCRIPTIONS]));
  array_push($errors, $settingService->createUpdateSetting(MAX_WIND_SPEED, $_POST[MAX_WIND_SPEED]));
  array_push($errors, $settingService->createUpdateSetting(MAX_GUST_SPEED, $_POST[MAX_GUST_SPEED]));
  array_push($errors, $settingService->createUpdateSetting(NWS_ALERT_TYPES, $_POST[NWS_ALERT_TYPES]));
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
    echo "<div class='p-1 alert bg-success text-white font-weight-bold'>Configuration saved successfully.</div>";
  }
  ?>

  <div class="row my-3">
    <div class="col-md-2 text-center">Donate</div>
    <div class="col-md">
      Enjoy using this plugin? Please consider making a donation to support the future development of this plugin.
      <div>
        <a href="https://www.paypal.com/donate/?hosted_button_id=GXFQ3GT6DRZFN" target="_blank">
          <button class="buttons">Make Donation</button></a>
      </div>
    </div>
  </div>

  <form method="post">
    <div class="row my-3">
      <div class="col-md-2 text-center">NWS Weather Station ID</div>
      <div class="col-md">
        <input type="text" name="<?php echo NWS_WEATHER_STATION_ID; ?>"
          value="<?php echo $settingService->getSetting(NWS_WEATHER_STATION_ID); ?>" required="required" />
        <div class="text-muted">
          Enter the identifier for the weather station closest to your location.
          Enter "0000" to automatically populate the closest weather station using the latitude and longitude entered
          on <a href="/settings.php#settings-system">Status/Control > FPP Settings > System tab</a>.
          Auto-populate station ID is done in the background and will not immediately update this screen.
        </div>
      </div>
    </div>

    <div class="row my-3">
      <div class="col-md-2 text-center">NWS Weather Alert Zone</div>
      <div class="col-md">
        <input type="text" disabled="disabled" name="<?php echo NWS_WEATHER_ALERT_ZONE; ?>"
          value="<?php echo $settingService->getSetting(NWS_WEATHER_ALERT_ZONE); ?>" />
        <div class="text-muted">
          This field is automatically populated based on the Weather Station ID and is displayed for
          informational purposes.
        </div>
      </div>
    </div>

    <div class="row my-3">
      <div class="col-md-2 text-center">Email Address</div>
      <div class="col-md">
        <input type="email" name="<?php echo EMAIL_ADDRESS_SETTING; ?>"
          value="<?php echo $settingService->getSetting(EMAIL_ADDRESS_SETTING); ?>" required="required" />
        <div class="text-muted">
          Enter the email address that will be used to identify your API calls made to the National Weather Service API.
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
          Separate descriptions with a semi colon.
        </div>
      </div>
    </div>

    <h2>Wind Speed</h2>

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

    <h2>Watches and Warning Alerts</h2>

    <div class="row my-3">
      <div class="col-md-2 text-center">
        Watches and Warnings
      </div>
      <div class="col-md">
        <?php
        foreach ($settingService->getAlertTypes() as $alertType) {
          $checked = "";
          $selectedAlertTypes = $settingService->getSetting(NWS_ALERT_TYPES);
          if (strpos($selectedAlertTypes, $alertType) !== false) {
            $checked = "checked='checked'";
          }
          ?>
          <div>
            <input type="checkbox" name="<?php echo NWS_ALERT_TYPES; ?>[]" value="<?php echo $alertType; ?>" <?php echo $checked; ?> />
          </div>
          <?php
        }
        ?>
        <div class="text-muted">
          Select each of the Watches and Warnings that, if active, the monitor will stop your show.
        </div>
      </div>
    </div>


    <button class="buttons my-3" type="submit">Save Settings</button>
  </form>
</body>

</html>