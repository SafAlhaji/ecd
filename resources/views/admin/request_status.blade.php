<div class="box box-{{ $color }}">
    <div class="box-header with-border">
        <h3 class="box-title">{{ $title }}</h3>

        <div class="box-tools pull-right">
            <select class="form-control form-control-sm" id="requests_status">
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
                  <th scope='col'>All </th>
                  <th scope='col'>PENDING</th>
                  <th scope='col'>IN_EMBASSY</th>
                  <th scope='col'>At_Office</th>
                  <th scope='col'>COMPELETED</th>
              </tr>
          </thead>
          <tbody>
            <tr id="count_request_status">
                <td>{{$response['all']}}</td>
                <td>{{$response['pending']}}</td>
                <td>{{$response['in_embassy']}}</td>
                <td>{{$response['at_office']}}</td>
                <td>{{$response['compeleted']}}</td>                        
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
    $(document).on('change', '#requests_status', function(){
        var payment_type = document.getElementById('requests_status').value;
        $.ajax({
            method: 'get',
            url: './admin/requests_status/'+payment_type,
            dataType : 'json',
            data:$(this).serialize(),
            success: function (data) {
                $("#count_request_status").find('td').remove();
                $("#count_request_status").append("<td>"+ data.all + "</td><td>"+ data.pending + "</td><td>"+ data.in_embassy + "</td><td>"+ data.at_office + "</td><td>"+ data.compeleted + "</td>");
            }
        });        
    });
</script>
