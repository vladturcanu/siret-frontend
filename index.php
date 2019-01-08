<?php
    session_start();
    include "variables.php";
    $page_title = "Siret Map";
    $active_map = "active";
    include "header.php";
?>

<div id="map">
</div>

<script>
    var sensorData = [];

    function getSensorData() {
        /* Get sensor data from server */
        $.get("https://serene-cove-78266.herokuapp.com/get_data")
            .done(function(data) {
                /* Save data to global array */
                sensorData = data;

                /* In order to see the sensor data, open the browser console (F12) and browse through the object */
                console.log(sensorData);

                /* Might need to wait until this point to start displaying the map. This is async. You only have the sensor data once you reach this point. */
            });
    }

    getSensorData();

    /* TODO: Write code to display map using data in sensorData. Map will be drawn in the div#map */
</script>
<?php
    include "footer.php";
?>