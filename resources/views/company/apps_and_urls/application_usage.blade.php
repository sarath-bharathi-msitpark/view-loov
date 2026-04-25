@push('script-page')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load("current", {packages: ["corechart"]});

        document.addEventListener("DOMContentLoaded", function () {
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn("string", "Application");
                data.addColumn("number", "Hours");
                data.addColumn({type: "string", role: "style"});
                data.addColumn({type: "string", role: "annotation"});

                @if(count($screenTimeData) > 0)
                data.addRows([
                        @foreach($screenTimeData as $item)
                    ["{{ $item['application'] }}", {{ $item['hours'] }}, "#D4EDEA", "{{ $item['application'] }}"],
                    @endforeach
                ]);
                @else
                data.addRow(["", 0, "#FFFFFF", ""]);
                @endif

                const rowCount = data.getNumberOfRows();
                const rowHeight = 40; // approx per row
                const chartHeight = rowCount * rowHeight;

                var options = {
                    title: "Application Screen Time (Hours)",
                    bar: {groupWidth: "80%"},
                    legend: {position: "none"},
                    chartArea: {
                        width: "80%",
                        height: chartHeight + "px"
                    },
                    height: chartHeight + 80, // total chart height
                    hAxis: {
                        title: "Hours",
                        format: '#.##',
                        gridlines: {count: 5},
                        minValue: 0
                    },
                    vAxis: {
                        textStyle: {fontSize: 12}
                    }
                };

                var chart = new google.visualization.BarChart(document.getElementById("barchart_values"));
                chart.draw(data, options);
            }
        });
    </script>
@endpush

<div class="col-xl-6 col-lg-12">
    <div class="chart_box">
        <div class="d-flex justify-content-between">
            <h2 class="text-dark fw-semibold fs-4">Application Usage</h2>
            <img src="{{ asset('assets/assestsnew/grapicon.svg') }}" alt="">
        </div>
        <div id="barchart_values"
             style="width: 100%; height: 400px; overflow-x: hidden; overflow-y: auto;"></div>
    </div>
</div>
