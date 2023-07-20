<?php

use App\Models\DashboardChart;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Encore\Admin\Facades\Admin;
        $period = CarbonPeriod::create(Carbon::now()->subMonth()->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
        $days = [];
        $requests_data = [];
        foreach ($period as $date) {
            $days[] = $date->format('Y-m-d');
        }
        $requests_data = DashboardChart::whereChartType(DashboardChart::REUQESTS)->whereAdminUserId(Admin::user()->id)->get();
        
?>
<style type="text/css">
.loader-chart {
  border: 2px solid #f3f3f3;
  border-radius: 50%;
  border-top: 2px solid #3498db;
  width: 30px;
  height: 30px;
  margin: auto;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
    
</style>
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Total Services Amount per Month</h3>


    </div>
    <div  style="text-align: center;">
        
        <button id="update_requests_chart" class="btn btn-success">Update</button>
    </div>
 
    <!-- /.box-header -->
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <canvas id="myChart" style="display: block; width: 1200px !important; height: 400px !important;"></canvas>

<script>
$(function () {
    var ctx = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [@foreach($days as $day)  "{{$day }}" , @endforeach],
            datasets: [
            @foreach($requests_data as $req_data)
            {
                label: '# of '+"{{$req_data->title}}",
                data: [@foreach($req_data->counts as $total_req) {{$total_req }} ,  @endforeach],
                borderColor: [
                    '{{$req_data->chart_color}}'
                ],
                borderWidth: 2,
                fill:false
            },
            @endforeach
            ]
        },
        options: {
             animation: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });
});
</script>
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.box-body -->
</div>
<script type="text/javascript">
    $(document).on('click', '#update_requests_chart', function(){
        $.ajax({
            method: 'get',
            url: 'update_requests_chart',
            dataType : 'json',
    beforeSend: function (xhr) {
      $( "<div class='loader-chart' id='searching-loader'></div>").appendTo("#update_requests_chart");
      $("html, body").animate( { scrollTop: $(document).height() }, 100);
    },            
            success: function (data) {
                $('#searching-loader').remove();
                if (data.status) {
                    location.reload();
                }
        }
    });


});

</script>
