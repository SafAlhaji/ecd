<div class="box box-{{ $color }}">
    <div class="box-header with-border">
        <h3 class="box-title">{{ $title }}</h3>

        <div class="box-tools pull-right">
            <select class="form-control form-control-sm" id="requests_payment_type">
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
                  <th scope='col'></th>
                  <th scope='col'>All Requests</th>
                  <th scope='col'>Cash</th>
                  <th scope='col'>Bank</th>
                  <th scope='col'>Credit</th>
                </tr>
              </thead>
              <tbody>
                <tr class="count">
                  <td>Count</td>
                    <td>{{$response['all']['count'] ?? 0}}</td>
                    <td>{{$response['cash']['count'] ?? 0}}</td>
                    <td>{{$response['bank']['count'] ?? 0}}</td>
                    <td>{{$response['credit']['count'] ?? 0}}</td>                        
                </tr>
                <tr class="amount">
                    <td>Amount</td>
                    <td>{{$response['all']['amount'] ?? 0}}</td>
                    <td>{{$response['cash']['amount'] ?? 0}}</td>
                    <td>{{$response['bank']['amount'] ?? 0}}</td>
                    <td>{{$response['credit']['amount'] ?? 0}}</td>                    
                </tr>
              </tbody>                
            </table>
        </div>
    </div>
    <!-- /.box-body -->
</div>
<script type="text/javascript">
    $(document).on('change', '#requests_payment_type', function(){
        var payment_type = document.getElementById('requests_payment_type').value;
        $.ajax({
            method: 'get',
            url: './admin/requests_payment_type/'+payment_type,
            dataType : 'json',
            data:$(this).serialize(),
            success: function (data) {
                $(".count").find('td').remove();
                $(".count").append("<td>Count</td><td>"+ data.all.count + "</td><td>"+ data.cash.count + "</td><td>"+ data.bank.count + "</td><td>"+ data.credit.count + "</td>");
                $(".amount").find('td').remove();
                $(".amount").append("<td>Amount</td><td>"+ data.all.amount + "</td><td>"+ data.cash.amount + "</td><td>"+ data.bank.amount + "</td><td>"+ data.credit.amount + "</td>");
            }
        });        
});
</script>
