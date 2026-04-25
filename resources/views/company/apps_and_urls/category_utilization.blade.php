@push('script-page')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", {packages: ["corechart"]});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Category', 'Count'],
                ['App', {{ $appCount }}],
                ['URL', {{ $urlCount }}],
            ]);

            var options = {
                pieHole: 0.4,
                backgroundColor: 'transparent',
                pieSliceText: 'percentage',
                colors: ['#3366cc', '#dc3912'],
                legend: 'none',
                chartArea: {
                    width: '100%',
                    height: '100%'
                }
            };

            var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
            chart.draw(data, options);

            // Display center total
            const total = data.getFilteredRows([{column: 1}])
                .map(i => data.getValue(i, 1))
                .reduce((a, b) => a + b, 0);
            document.getElementById('donut-center').innerText = total;
        }
    </script>
@endpush

<div class="chart_box h-100">
    <div class="d-flex justify-content-between">
        <h2 class="text-dark fw-semibold fs-4">Category Utilization</h2>
        <img src="{{ asset('assets/assestsnew/grapicon.svg') }}" alt="">
    </div>

    <div style="width: 100%; max-width: 600px; margin: auto; position: relative;">
        <div id="donutchart" style="width: 100%; height: 350px;"></div>

        <!-- Center total value -->
        <div class="dount_chart_app" id="donut-center"
             style="position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%);
                    font-size: 24px; font-weight: bold; color: #333;">
        </div>

        <!-- Custom Manual Legend Layout -->
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-top: 20px;">
            <div style="display: flex; gap: 20px;">
                <span>
                    <span class="legend-color"
                          style="background-color:#3366cc; width: 14px; height: 14px; display:inline-block; margin-right:6px; border-radius: 3px;"></span>
                    App
                </span>
                <span>
                    <span class="legend-color"
                          style="background-color:#dc3912; width: 14px; height: 14px; display:inline-block; margin-right:6px; border-radius: 3px;"></span>
                    URL
                </span>
            </div>
        </div>
    </div>
</div>
</div>
