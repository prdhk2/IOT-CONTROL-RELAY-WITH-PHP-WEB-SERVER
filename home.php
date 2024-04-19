<?php
    require "template.php";
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>ESP32 WITH MYSQL DATABASE</title>    
    
    <link rel="stylesheet" href="style/css/styles.css"> <!-- custom styles -->

</head>

<body>
    <!-- navbar -->
    <div class="topnav">
        <nav class="navbar fixed-top  navbar-dark ml-auto ">
            <a class="navbar-brand" href="home.php"><i class="fa fa-home"></i> Control Relay Biodigester</a>
        </nav>
    </div>
    <!-- navbar end -->

    <!-- content start -->
<div class="content">
<center>
    <h2 class="mt-4">CONTROL PANEL</h2>
    <br>
    <div class="table">
        <table class="styled-table" id= "table_id">
            <thead>
                <tr>
                    <th>Switch</th>
                    <th>Device</th>
                    <th>GPIO</th>
                    <th colspan="2">Countdown (hour)</th>
                </tr>
            </thead>
            <tbody  id="tbody_table_record">
                <tr>
                    <td class="bdr">
                        <label class="switch">
                            <input type="checkbox" id="ESP32_01_TogLED_01" onclick="GetTogBtnLEDState('ESP32_01_TogLED_01')">
                            <div class="sliderTS"></div>
                        </label>
                    </td>
                    <td class="bdr">Kompor 1</td>
                    <td class="bdr">GPIO 12</td>
                    <td class="bdr">-</td>
                    <td class="bdr">
                        <button class="btn btn-warning" onclick=""><i class="fa fa-edit mr-2"></i>Edit</button>
                    </td>
                </tr>
                <tr>
                    <td class="bdr">
                        <label class="switch">
                            <input type="checkbox" id="ESP32_01_TogLED_02" onclick="GetTogBtnLEDState('ESP32_01_TogLED_02')">
                            <div class="sliderTS"></div>
                        </label> 
                    </td>
                    <td class="bdr">Kompor 2</td>
                    <td class="bdr">GPIO 13</td>
                    <td class="bdr">-</td>
                    <td class="bdr">
                        <button class="btn btn-warning" onclick=""><i class="fa fa-edit mr-2"></i>Edit</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <br>
            <button class="btn btn-warning" onclick="window.open('recordtable.php', '_blank');">Open Record Table</button>
            <h3 style="font-size: 0.7rem;"></h3>

            
        
    </center>
   <!-- content end -->
    <script>
      //------------------------------------------------------------
      document.getElementById("ESP32_01_Temp").innerHTML = "NN"; 
      document.getElementById("ESP32_01_Humd").innerHTML = "NN";
      document.getElementById("ESP32_01_Status_Read_DHT11").innerHTML = "NN";
      document.getElementById("ESP32_01_LTRD").innerHTML = "NN";
      //------------------------------------------------------------
      
      Get_Data("esp32_01");
      
      setInterval(myTimer, 5000);
      
      //------------------------------------------------------------
      function myTimer() {
        Get_Data("esp32_01");
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function Get_Data(id) {
				if (window.XMLHttpRequest) {
          // code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp = new XMLHttpRequest();
        } else {
          // code for IE6, IE5
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            const myObj = JSON.parse(this.responseText);
            if (myObj.id == "esp32_01") {
              document.getElementById("ESP32_01_Temp").innerHTML = myObj.temperature;
              document.getElementById("ESP32_01_Humd").innerHTML = myObj.humidity;
              document.getElementById("ESP32_01_Status_Read_DHT11").innerHTML = myObj.status_read_sensor_dht11;
              document.getElementById("ESP32_01_LTRD").innerHTML = "Time : " + myObj.ls_time + " | Date : " + myObj.ls_date + " (dd-mm-yyyy)";
              if (myObj.LED_01 == "ON") {
                document.getElementById("ESP32_01_TogLED_01").checked = true;
              } else if (myObj.LED_01 == "OFF") {
                document.getElementById("ESP32_01_TogLED_01").checked = false;
              }
              if (myObj.LED_02 == "ON") {
                document.getElementById("ESP32_01_TogLED_02").checked = true;
              } else if (myObj.LED_02 == "OFF") {
                document.getElementById("ESP32_01_TogLED_02").checked = false;
              }
            }
          }
        };
        xmlhttp.open("POST","getdata.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("id="+id);
			}
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function GetTogBtnLEDState(togbtnid) {
        if (togbtnid == "ESP32_01_TogLED_01") {
          var togbtnchecked = document.getElementById(togbtnid).checked;
          var togbtncheckedsend = "";
          if (togbtnchecked == true) togbtncheckedsend = "ON";
          if (togbtnchecked == false) togbtncheckedsend = "OFF";
          Update_LEDs("esp32_01","LED_01",togbtncheckedsend);
        }
        if (togbtnid == "ESP32_01_TogLED_02") {
          var togbtnchecked = document.getElementById(togbtnid).checked;
          var togbtncheckedsend = "";
          if (togbtnchecked == true) togbtncheckedsend = "ON";
          if (togbtnchecked == false) togbtncheckedsend = "OFF";
          Update_LEDs("esp32_01","LED_02",togbtncheckedsend);
        }
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function Update_LEDs(id,lednum,ledstate) {
				if (window.XMLHttpRequest) {
          // code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp = new XMLHttpRequest();
        } else {
          // code for IE6, IE5
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            //document.getElementById("demo").innerHTML = this.responseText;
          }
        }
        xmlhttp.open("POST","updateLEDs.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("id="+id+"&lednum="+lednum+"&ledstate="+ledstate);
			}
      //------------------------------------------------------------
    </script>
  </body>
</html>