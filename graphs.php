<?php
    session_start();
    $page_title = "Siret Map";
    $active_map = "";
    $active_incidents = "";
    $active_account = "";
    include "header.php";
?>

<div id="graph">
</div>

<script>
    /* We get to this page through a link in the Map, when showing details of a sensor.
      Make that link appear on the bottom of the sensor details window.
      Link will be as follows: http://localhost/siret-frontend/graphs.php?parameter=<param>&location=<locatie>
      If you can make 2 links, send "parameter" as well.
      Otherwise, send only location and we will draw both ph and turbidity
    */

    var data = [
        {
            x: ['2019-01-04 14:10:26.000000', '2019-01-04 14:10:29.000000', '2019-01-04 14:10:30.000000'],
            y: [9, 10, 8],
            type: 'scatter'
        }
    ];

    Plotly.newPlot('graph', data);
    
</script>

<?php
    include "footer.php";
?>