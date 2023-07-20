<div class="box box-{{ $color }}">
    <div class="box-header with-border">
        <h3 class="box-title">{{ $title }}</h3>

        <div class="box-tools pull-right">
            <select class="form-control form-control-sm" id="net_profit">
                <option value="1">Day</option>
                <option value="2">Month</option>
                <option value="3">Year</option>
            </select>
        </div>
    </div>

    <!-- /.box-header -->
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th scope='col'>Net Income</th>
                  <th scope='col'>Net Expenses</th>
                  <th scope='col'>Net Profit</th>
                </tr>
              </thead>
              <tbody>
                <tr id="net_profit_status">
                    <td>{{$response['net_income']}}</td>
                    <td>{{$response['net_expenses']}}</td>
                    <td>{{$response['net_profit']}}</td>                    
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>                
              </tbody>                
            </table>
        </div>
    </div>
    <!-- /.box-body -->
</div>
<script type="text/javascript">
    $(document).on('change', '#net_profit', function(){
        var payment_type = document.getElementById('net_profit').value;
        $.ajax({
            method: 'get',
            url: './admin/net_profit/'+payment_type,
            dataType : 'json',
            data:$(this).serialize(),
            success: function (data) {
                $("#net_profit_status").find('td').remove();
                $("#net_profit_status").append("<td>"+ data.net_income + "</td><td>"+ data.net_expenses + "</td><td>"+ data.net_profit + "</td>");
            }
        });        
});
</script>
