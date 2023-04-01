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
  foreach ($errors as $error) {
    if ($error !== true) {
      echo "<div class='p-1 alert detract'>" . $error . "</div>";
    }
  }
  ?>
  <form method="post">
    <div>
      <h2>Donate</h2>
      <div class="">Donate</div>
      <div class="">
        Enjoy using this plugin? Please make a donation to support the future of this plugin.
      </div>
    </div>

<div class="row py-2">
<div class="col-md-2 text-center">NWS Weather Station</div>
<div>
<input type="text">
<div class="text-muted">
Help information about the setting.
</div>
</div>
</div>

<div class="form-group">
<label>NWS Weather Station</label>
<input type="text">
<div>This is some information about the weather station</div>
</div>

    <div>
      <h2 class="pt-2">General</h2>
      <div class="">NWS Weather Station ID</div>
      <div class="">
        <input class="" type="text" name="<?php echo NWS_WEATHER_STATION_ID; ?>"
          value="<?php echo $settingService->getSetting(NWS_WEATHER_STATION_ID); ?>" required="required" />
        <div>
          If left blank, station ID will automatically be populated from the GPS coordinates entered at Status/Control >
          FPP Settings > System.
        </div>
      </div>
      <div class="">Email Address</div>
      <div class="">
        <input class="" type="text" name="<?php echo EMAIL_ADDRESS_SETTING; ?>"
          value="<?php echo $settingService->getSetting(EMAIL_ADDRESS_SETTING); ?>" required="required" />
        <div>
          Enter the email address that will be used to identify your API calls made to the National Weather Service API.
          If ther are issues or concerns with the APIs calls that are made, you will be notified by the NWS at the email
          entered.
        </div>
      </div>
    </div>
    <div>
      <div class="">Weather Text Descriptions</div>
      <div class="">
        <input type="text" name="<?php echo WEATHER_DESCRIPTIONS; ?>"
          value="<?php echo $settingService->getSetting(WEATHER_DESCRIPTIONS); ?>" />
      </div>
    </div>

    <div>
      <h2 class="pt-2">Wind</h2>
   </div>
   <div class="pb-2">
      <div class="">
        Max Wind Speed
        <?php echo $settingService->getSpeedUnitText(); ?>
      </div>
      <div class="">
        <input class="" type="number" name="<?php echo MAX_WIND_SPEED; ?>"
          value="<?php echo $settingService->getSetting(MAX_WIND_SPEED); ?>" required="required" />
        <div>
          Enter the wind speed that if exceeded, your show will be stopped by the monitor.
        </div>
      </div>

      <div class="">
        Max Gust Speed
        <?php echo $settingService->getSpeedUnitText(); ?>
      </div>
      <div class="">
        <input class="" type="number" name="<?php echo MAX_GUST_SPEED; ?>"
          value="<?php echo $settingService->getSetting(MAX_GUST_SPEED); ?>" required="required" />
        <div>
          Enter the gust speed, that if exceeded, your show will be stopped by the monitor.
        </div>
      </div>
    </div>

    <button class="buttons my-2" type="submit">Save Settings</button>
  </form>
</body>

</html>
