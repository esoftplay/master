<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!defined('_GMAP_KEY'))
{
  echo 'Maaf, anda harus menentukan variable constants untuk _GMAP_KEY di '._ROOT.'config.php, untuk mendapatkan gmap key silahkan kunjungi <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">https://developers.google.com/maps/documentation/javascript/get-api-key</a>';
  die();
}
if (empty($_GET['id']) && empty($_GET['latlong']) && empty($_GET['zoom']))
{
	echo 'Seharusnya anda menentukan $_GET[\'id\'] atau $_GET[\'latlong\'] untuk menentukan area yang akan anda pilih point location nya<br />';
	echo 'Atau anda bisa mempelajari penggunaan function <code><a href="http://dev.esoftplay.com/user_location" target="_blank">user_location</a>($click_ids, $id_area, $alert=\'\');</code>';
	die();
}
$name    = @$_GET['id'];
$latlong = @$_GET['latlong'];
$zoom    = @intval($_GET['zoom']);

$address = 'Indonesia';
$holder  = 'masukkan nama kecamatan atau kota';
if (!empty($name))
{
  $address = $name;
}
$lat = 0;
$lng = 0;
if (!empty($latlong) && preg_match('~([\-0-9\.]+),([\-0-9\.]+)~s', $latlong, $m))
{
  $lat = @trim($m[1]);
  $lng = @trim($m[2]);
}
if (empty($zoom))
{
  $zoom = !empty($lat) ? 19 : 20; // Why 17? Because it looks good.
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Click your exact position</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <link href="<?php echo _URL; ?>/templates/admin/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
      #my-form{
        position: absolute;
        top: 65px;
        text-align: center;
        width: 100%;
      }
    </style>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=<?php echo _GMAP_KEY; ?>&sensor=false&libraries=places"></script>
    <script type="text/javascript">
      function initialize()
      {
        var mapOptions = {
          zoom: <?php echo $zoom; ?>,
          center: new google.maps.LatLng(<?php echo $lat;?>,<?php echo $lng;?>)
        };
        var map     = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        var infoWin = new google.maps.InfoWindow();
        infoWin.setContent('Posisi saat ini');
        var marker  = new google.maps.Marker({map: map, draggable:true});
        <?php
        if(empty($lat) || empty($lng))
        {
          ?>
          geocoder = new google.maps.Geocoder();
          geocoder.geocode( { 'address': "<?php echo $address;?>"}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              var place = results[0];
              if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
              } else {
                map.setCenter(place.geometry.location);
                map.setZoom(<?php echo $zoom;?>);  // Why 17? Because it looks good.
              }
            } else {
              alert('Geocode was not successful for the following reason: ' + status);
            }
          });
          <?php
        }else{
          ?>
          var LatLng = new google.maps.LatLng(<?php echo $lat;?>,<?php echo $lng;?>);
          marker.setPosition(LatLng);
          marker.setVisible(true);
          marker.setMap(map);
          <?php
        }
        ?>
        google.maps.event.addListener(map, 'dblclick', function(event) {
          clearTimeout(wclick);
          var LatLng = new google.maps.LatLng(event.latLng.lat(),event.latLng.lng());
          map.setCenter(LatLng);
          marker.setPosition(LatLng);
          marker.setVisible(false);
        });
        google.maps.event.addListener(map, 'click', function(event) {
          wclick = window.setTimeout(function() {
            a('latitude').value = event.latLng.lat();
            a('longitude').value = event.latLng.lng();
            var LatLng = new google.maps.LatLng(event.latLng.lat(),event.latLng.lng());
            marker.setPosition(LatLng);
            marker.setVisible(true);
            var f = setInterval(function () {
              $('#btn_picker').toggleClass("btn-warning")
            },
            100);
            window.setTimeout(function () {
              clearInterval(f);
              $('#btn_picker').removeClass("btn-warning")
            },
            1000)
          },200);
        });
        google.maps.event.addListener(marker, 'drag', function() {
          a('latitude').value = marker.position.lat();
          a('longitude').value = marker.position.lng();
        });
      };
      function a(txt)
      {
        return document.getElementById(txt);
      };
      function pick()
      {
        var b = a('latitude').value;
        var c = a('longitude').value;
        if(b == '' || b == '0')
        {
          alert('Klik posisi yang anda maksudkan di dalam peta untuk menentukan Latitude dan Longitude!');
        }else
        if(c == '' || c == '0')
        {
          alert('Klik posisi yang anda maksudkan di dalam peta untuk menentukan Longitude!');
        }else{
          window.opener.setPosition(b, c);
          window.close();
        }
        return false;
      }
      var wclick;
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
    <div id="my-form" class="form-inline">
      <?php
      if (empty($lat))
      {
        $lat = '';
      }
      if (empty($lng))
      {
        $lng = '';
      }
      ?>
      <input type="text" class="form-control" id="latitude" placeholder="Latitude" value="<?php echo $lat;?>" readonly />
      <input type="text" class="form-control" id="longitude" placeholder="Longitude" value="<?php echo $lng;?>" readonly />
      <button onclick="return pick();" class="btn btn-default" id="btn_picker">Pilih Posisi!</button>
      <div class="help-block">scroll untuk zoom, double click untuk fokus area, single click untuk tandai</div>
    </div>
    <script src="<?php echo _URL; ?>templates/admin/bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
<?php
die;