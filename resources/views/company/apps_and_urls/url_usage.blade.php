@push('script-page')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", {packages: ["corechart"]});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ["URL", "Usage (Hours)", {role: "style"}],
                    @if(count($urlTimeData) > 0)
                    @foreach($urlTimeData as $item)
                ["{{ Str::limit($item['url'], 30) }}", {{ $item['hours'] }}, "#D4EDEA"],
                    @endforeach
                    @else
                ["", 0, "#FFFFFF"]
                @endif
            ]);

            var view = new google.visualization.DataView(data);
            view.setColumns([
                0,
                1,
                {
                    calc: "stringify",
                    sourceColumn: 1,
                    type: "string",
                    role: "annotation"
                },
                2
            ]);

            var options = {
                backgroundColor: "transparent",
                bar: {groupWidth: "80%"},
                legend: {position: "none"},
                chartArea: {width: "85%", height: "70%"},
                hAxis: {
                    title: "Usage in Hours",
                    minValue: 0,
                    gridlines: {color: "#ccc"},
                    textStyle: {color: "#333"}
                },
                vAxis: {
                    textStyle: {fontSize: 12}
                }
            };

            var chart = new google.visualization.BarChart(document.getElementById("barchart"));
            chart.draw(view, options);
        }
    </script>
@endpush

<div class="col-lg-12 col-md-12 col-xl-8">
    <div class="chart_box">
        <div class="d-flex justify-content-between">
            <h2 class="text-dark fw-semibold fs-4">URL Usage</h2>
            <img src="{{ asset('assets/assestsnew/grapicon.svg') }}" alt="">
        </div>
        <div id="barchart"
             style="width: 100%; height: 500px; overflow-x: hidden; overflow-y: hidden;"></div>
    </div>
</div>
