<?php
    /* We get to this page through a link in the Map, when showing details of a sensor. */
    session_start();
    $page_title = "Siret Map";
    $active_map = "";
    $active_incidents = "";
    $active_account = "";
    include "header.php";
?>

<?php
    if (isset($_GET["parameter"]) &&
        isset($_GET["location"]) &&
        $_GET["parameter"] != "" &&
        $_GET["location"] != "") 
    {
        $parameter = $_GET["parameter"];
        $location = $_GET["location"];

        if ($parameter == "ph") {
            $display_parameter = "<span class='label bg-blue'>pH</span>";
        } else {
            $display_parameter = "<span class='label bg-green'>turbidity</span>";
        }
    } else {
        $error = "Please specify in your GET request the location of the sensor and the measured parameter.";
    }
?>
       
<?php if (isset($error)): ?>

    <p class="error-msg"><?= $error ?></p>

<?php else: ?>
    <div class="container">
        <h3 class="page-title">Measured <?= $display_parameter; ?> values near <span class="blue"><?= ucfirst($location); ?></span></h3>
    </div>
    <div id="please-wait">Please wait. Fetching data...</div>
    <div id="graph">
    </div>

    <script>
        function plotData() {
            var parameter = "<?= $parameter ?>";
            var location = "<?= $location ?>";

            var postData = JSON.stringify({
                "parameter": parameter,
                "location": location
            });

            /* Fetch data */
            $.post("https://serene-cove-78266.herokuapp.com/get_sensor_data", postData)
                .done(function(data) {
                    if (data["error"]) {
                        $("#please-wait").html("<p class='error-msg'>"+data["error"]+"</p>");
                    } else {
                        /* Arrange data for graph */
                        var graphData = {
                            x: [],
                            y: [],
                            type: 'scatter'
                        };

                        for (var i = 0; i < data.length; i++) {
                            var dataPoint = data[i];

                            graphData["x"].push(dataPoint['timestamp']['date']);
                            graphData["y"].push(dataPoint['value']);
                        }

                        var plotlyData = [graphData];
                        Plotly.newPlot('graph', [graphData]);

                        $("#please-wait").html("");
                    }
                })
                .fail(function() {
                    $("#please-wait").html("<p class='error-msg'>Could not establish connection with the server. Please refresh the page.</p>");
                });
        }

        plotData();
    </script>

<?php endif; ?>

<?php
    include "footer.php";
?>