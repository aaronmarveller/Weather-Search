<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
    <script src="myscript.js"></script>
</head>
<body>
<div id="searchModule">
    <h1 id="searchTitle" align="center">Weather Search</h1>
    <form id="myForm" name="myForm" method="post" action="#" onsubmit="return isCorrect();">
        <div id="leftBlock">
            <table>
                <tr>
                    <td class="formFont">Street</td>
                    <td><input type="text" id="street" name="street" class="info" value="<?php echo $_POST['street']?>"/></td>
                </tr>
                <tr>
                    <td class="formFont">City</td>
                    <td><input type="text" id="city" name="city" class="info" value="<?php echo $_POST['city']?>"/></td>
                </tr>
                <tr>
                    <td colspan="2" class="formFont">State
                        <select id="state" name="state">
                            <option>State</option>
                            <optgroup label="----------------"></optgroup>
                        </select>
                    </td>
                </tr>
                <script>
                    initializeSelect();
                </script>
                <?php
                if(isset($_POST['state'])) {
                    echo "<script>";
                    echo "
                        var select = document.getElementById('state');
                        for(var i=0; i<select.length; i++){
                            if(select[i].innerText=='".$_POST['state']."'){
                                select[i].selected = true;
                            }
                        }";
                    echo "</script>";
                }
                ?>
            </table>
        </div>
        <div id="middleLine"></div>
        <div id="rightBlock" class="formFont">
            <input type="checkbox" id="checkbox" name="checkbox" onclick="lockForm();"> Current Location
            <input type="hidden" id="lat" name="lat">
            <input type="hidden" id="lng" name="lng">
            <input type="hidden" id="current_city" name="current_city">
        </div>
        <?php
        if($_POST["checkbox"] == "on"||$_POST["checkbox"] == "true") {
            echo "<script>
                       lockForm();
                       document.getElementById('checkbox').checked = true;
                   </script>";
        }
        ?>
        <div id="bottomBlock">
            <input type="submit" id="searchButton" name="submit" value="search">
            <input type="button" id="clearButton" name="clear" value="clear" onclick="clearForm();">
        </div>
    </form>
</div>
<div id="result">
    <?php if((isset($_POST["street"])||$_POST["checkbox"])&&!isset($_POST["time"])){ ?>

        <!--    Result Module -->
        <?php
        //    API_KEY of GCP & Dark Forecast API
        $google_key = "AIzaSyB_aPHXc4uBz7Af1tNogaYNN4wR4tppsqU";
        $forecast_key = "eb42c2c75974c69253bc989791d8f331";

        //    size of the icon
        $x = "25px";

        //    get the location
        if ($_POST["checkbox"]) {
            $lat = $_POST["lat"];
            $lng = $_POST["lng"];
            $city_name = $_POST["current_city"];
        } else {
            $add_url = rawurlencode("https://maps.googleapis.com/maps/api/geocode/xml?") .
                urlencode("address=" . $_POST["street"] . "," . $_POST["city"] . "," . $_POST["state"] . "&key=$google_key");
            $google_xml = simplexml_load_file($add_url) or die("");

            if ($google_xml->result) {
                $lat = (string)$google_xml->result->geometry->location->lat;
                $lng = (string)$google_xml->result->geometry->location->lng;
                $city_name = $_POST["city"];
            } else {
                echo "<script>createAlertBox();</script>";
                return;
            }
        }

        //    get the weather info

        $weather_url = "https://api.forecast.io/forecast/$forecast_key/$lat,$lng?exclude=minutely,hourly,alerts,flags";
        $json_obj = json_decode(file_get_contents($weather_url));

        $timezone = $json_obj->{"timezone"};

        $current_weather = $json_obj->currently;

        $temperature = $current_weather->{"temperature"};
        $temp_icon = "https://cdn3.iconfinder.com/data/icons/virtual-notebook/16/button_shape_oval-512.png";

        $summary = $current_weather->{"summary"};

        $humidity = $current_weather->{"humidity"};
        $humid_icon = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-16-512.png";

        $pressure = $current_weather->{"pressure"};
        $press_icon = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-25-512.png";

        $wind_speed = $current_weather->{"windSpeed"};
        $wind_icon = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-27-512.png";

        $visibility = $current_weather->{"visibility"};
        $vis_icon = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-30-512.png";

        $cloud_cover = $current_weather->{"cloudCover"};
        $cloud_icon = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-28-512.png";

        $ozone = $current_weather->{"ozone"};
        $ozone_icon = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-24-512.png";


        //    create weather div
        echo "<div id='resultModule'>";
        echo "<p>$city_name</p>";
        echo "<p>$timezone</p>";
        echo "<p>$temperature <img src='$temp_icon' width='12px'></p><p>F</p>";
        echo "<p>$summary</p>";
        echo "
                <table id='icon'>
                    <tr>
                        <td><img src='$humid_icon' width='$x' title='humidity'></td>
                        <td><img src='$press_icon' width='$x' title='pressure'></td>
                        <td><img src='$wind_icon' width='$x' title='wind speed'></td>
                        <td><img src='$vis_icon' width='$x' title='visibility'></td>
                        <td><img src='$cloud_icon' width='$x' title='cloud cover'></td>
                        <td><img src='$ozone_icon' width='$x' title='ozone'></td>
                    </tr>
                    <tr>
                        <td>$humidity</td>
                        <td>$pressure</td>
                        <td>$wind_speed</td>
                        <td>$visibility</td>
                        <td>$cloud_cover</td>
                        <td>$ozone</td>
                    </tr>
                </table>
            ";
        echo "</div>";
        ?>

        <!--    Result Table -->
        <?php

        //    size of the icon
        $x = "40px";

        $future_weather = $json_obj->daily->data;

        $weather_info = array();
        for ($i = 0; $i < count($future_weather); $i++) {
            array_push($weather_info, array());
        }


        $weather_title = array("Date", "Status", "Summary", "TemperatureHigh", "TemperatureLow", "Wind Speed");
        $icon_map = array(
            "clear-day" => "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-12-512.png",
            "clear-night" => "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-12-512.png",
            "rain" => "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-04-512.png",
            "snow" => "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-19-512.png",
            "sleet" => "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-07-512.png",
            "wind" => "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-27-512.png",
            "fog" => "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-28-512.png",
            "cloudy" => "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-01-512.png",
            "partly-cloudy-day" => "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-02-512.png",
            "partly-cloudy-night" => "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-02-512.png"
        );
        $time_para = array();
        for ($i = 0; $i < count($weather_info); $i++) {
            array_push($weather_info[$i], date('Y-m-d', $future_weather[$i]->{"time"}),
                $icon_map[$future_weather[$i]->{"icon"}],
                $future_weather[$i]->{"summary"},
                $future_weather[$i]->{"temperatureHigh"},
                $future_weather[$i]->{"temperatureLow"},
                $future_weather[$i]->{"windSpeed"});
            array_push($time_para, $future_weather[$i]->{"time"});
        }


        echo "<div id='resultTable'>";
        echo "<div id='table'><table><tr>";
        foreach ($weather_title as $title) {
            echo "<td>$title</td>";
        }
        echo "</tr>";

        foreach ($weather_info as $i => $row_info) {
            echo "<tr>";
            foreach ($row_info as $j => $col_info) {
                if ($j == 1) { //icon
                    echo "<td><img src='$col_info' width='$x'></td>";
                } elseif ($j == 2) { //summary
                    echo "<td>
            <a style='text-decoration: none;color: white;cursor: pointer' onclick='submitTime($time_para[$i],$lat,$lng);'>
                        $col_info
                    </a></td>";
                } else {
                    echo "<td>$col_info</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table></div>";
        echo "</div>";
        ?>

    <?php } ?>

    <?php if(isset($_POST["time"])){ ?>
        <!--        Result Detail-->
        <?php
        $post_day = $_POST["time"];
        $forecast_key = "eb42c2c75974c69253bc989791d8f331";
        $lat = $_POST["lat"];
        $lng = $_POST["lng"];
        $weather_detail_url = "https://api.forecast.io/forecast/$forecast_key/$lat,$lng,$post_day?exclude=minutely";
        $json_detail_obj = json_decode(file_get_contents($weather_detail_url));

        $icon_detail_map = array(
            "clear-day" => "https://cdn3.iconfinder.com/data/icons/weather-344/142/sun-512.png",
            "clear-night" => "https://cdn3.iconfinder.com/data/icons/weather-344/142/sun-512.png",
            "rain" => "https://cdn3.iconfinder.com/data/icons/weather-344/142/rain-512.png",
            "snow" => "https://cdn3.iconfinder.com/data/icons/weather-344/142/snow-512.png",
            "sleet" => "https://cdn3.iconfinder.com/data/icons/weather-344/142/lightning-512.png",
            "wind" => "https://cdn3.iconfinder.com/data/icons/the-weather-is-nice-today/64/weather_10-512.png",
            "fog" => "https://cdn3.iconfinder.com/data/icons/weather-344/142/cloudy-512.png",
            "cloudy" => "https://cdn3.iconfinder.com/data/icons/weather-344/142/cloud-512.png",
            "partly-cloudy-day" => "https://cdn3.iconfinder.com/data/icons/weather-344/142/sunny-512.png",
            "partly-cloudy-night" => "https://cdn3.iconfinder.com/data/icons/weather-344/142/sunny-512.png"
        );

        function precipValue($value){
            if($value<=0.001){
                return "N/A";
            } elseif ($value<=0.015){
                return "Very Light";
            } elseif ($value<=0.05){
                return "Light";
            } elseif ($value<=0.1){
                return "Moderate";
            } else {
                return "heavy";
            }
        }

        $jet_lag_map = array(
            "America/New_York" => -5,
            "America/Chicago" => -6,
            "America/Denver" => -7,
            "America/Phoenix" => -8,
            "America/Los_Angeles" => -8,
            "America/Anchorage" => -9,
            "Pacific/Honolulu" => -11
        );

        $timezone = $json_detail_obj->{"timezone"};

        $temp_icon = "https://cdn3.iconfinder.com/data/icons/virtual-notebook/16/button_shape_oval-512.png";

        $current_weather_detail = $json_detail_obj->currently;

        $summary_detail = $current_weather_detail->{"summary"};

        $detail_icon = $icon_detail_map[$current_weather_detail->{"icon"}];

        $detail_temp = round($current_weather_detail->{"temperature"});

        $detail_precip = precipValue($current_weather_detail->{"precipIntensity"});

        $detail_rain_chance = round($current_weather_detail->{"precipProbability"}*100);

        $detail_wind_speed = $current_weather_detail->{"windSpeed"};

        $detail_humidity = round($current_weather_detail->{"humidity"}*100);

        $detail_visib = $current_weather_detail->{"visibility"};

        $detail_sunrise = (round(($json_detail_obj->daily->data[0]->{"sunriseTime"})*1.0/3600) + $jet_lag_map[$timezone] + 1)%12;
        $detail_sunset = (round(($json_detail_obj->daily->data[0]->{"sunsetTime"})*1.0/3600) + $jet_lag_map[$timezone] + 1)%12;

        echo "<h1 style='text-align: center;margin-bottom: -20px;'>Daily Weather Detail</h1>";
        echo "<div id='resultDetail'>";
        echo "<p id='detailSummary'>$summary_detail</p>";
        echo "<img src='$detail_icon' id='detailIcon' width='250px'>";
        echo "<p id='detailTemp'>$detail_temp<img src='$temp_icon' width='15px'></p>";
        echo "<p id='F'>F</p>";

        echo "
        <div id='detailTable'>
            <table>
                <tr>
                    <td>Precipitation:</td>
                    <td>$detail_precip</td>
                </tr>                
                <tr>
                    <td>Chance of Rain:</td>
                    <td>$detail_rain_chance <a>%</a></td>
                </tr>                
                <tr>
                    <td>Wind Speed:</td>
                    <td>$detail_wind_speed <a>mph</a></td>
                </tr>                
                <tr>
                    <td>Humidity:</td>
                    <td>$detail_humidity <a>%</a></td>
                </tr>                
                <tr>
                    <td>Visibility:</td>
                    <td>$detail_visib <a>mi</a></td>
                </tr>                
                <tr>
                    <td>Sunrise / Sunset:</td>
                    <td>$detail_sunrise <a>AM/</a> $detail_sunset <a>PM</a></td>
                </tr>
            </table>
        </div>
    ";
        echo "</div>";
        ?>

        <!--    Result Hourly Weather-->
        <?php
        $down_arrow = "https://cdn4.iconfinder.com/data/icons/geosm-e-commerce/18/point-down-512.png";
        $current_weather_hourly = $json_detail_obj->hourly->data;
        echo "<h1 style='text-align: center;margin-bottom: -8px;'>
            Day's Hourly Weather
          </h1>";
        echo "<img src='$down_arrow' class='arrow' onclick='showChart();'>";
        echo "<div id='resultChart'></div>";
        echo "<script>";
        echo "
        google.charts.load('current', {'packages':['corechart', 'line']});
        google.charts.setOnLoadCallback(drawChart);
    
        function drawChart() {
    
          var data = new google.visualization.DataTable();
          data.addColumn('number', 'int');
          data.addColumn('number', 'T');
    
          data.addRows([";

        for($i=0; $i<count($current_weather_hourly); $i++){
            echo "[$i,";
            echo $current_weather_hourly[$i]->{"temperature"};
            if($i==count($current_weather_hourly)-1) {
                echo "]";
            } else {
                echo "],";
            }
        }

        echo "
          ]);
    
          var logOptions = {
            colors: ['#A4CFD5'],
            width: 600,
            height: 150,
             hAxis: {
              title: 'Time',
            },
            vAxis: {
              title: 'Temperature',
              textPosition: 'none'
            }
          };
    
          var logChart = new google.visualization.LineChart(document.getElementById('resultChart'));
          logChart.draw(data, logOptions);
        }";
        echo "</script>";
        ?>

    <?php } ?>
</div>
</body>
</html>