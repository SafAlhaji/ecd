<?php
use App\Models\Branch;
use App\Models\ThirdParty;
use Encore\Admin\Facades\Admin;
      if (Admin::user()->isAdministrator()) {
          $branches = Branch::pluck('title as text', 'id');
      }
      if (!Admin::user()->isAdministrator()) {
          $branches =ThirdParty::find(Admin::user()->id)->branches()->get()->pluck('title', 'id');
      }
?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<a class="btn btn-sm btn-success" onclick="print_page_view('{!! url('net_income_report?'.request()->getQueryString()) !!}')">Print</a>
<form action="{{ url('admin/net_income') }}" method="get" pjax-container>
  {{-- <div class="modal-body"> --}}
    {{-- <div class="form"> --}}
      <div class="form-group" style="padding: 23px;">
        <label class="col-sm-2 control-label">Brnach</label>
        <div class="col-sm-10">
          <select class="form-control netincome_branch_id" name="branch_id" style="width: 100%;">
            <option></option>
            @foreach($branches as $select => $option)
            <option value="{{$select}}" {{ (string)$select === (string)request()->branch_id ? 'selected' : '' }}>{{$option}}</option>
            @endforeach
          </select>
        </div>
      </div>  
      <div class="form-group" style="padding: 23px;">
        <label class="col-sm-2 control-label">Service Provider</label>
        <div class="col-sm-10">
          <select class="form-control netincome_service_provider" name="service_provider" style="width: 100%;">
            <option></option>
            @foreach(App\Models\ServiceProvider::pluck('title','id') as $select => $option)
            <option value="{{$select}}" {{ (string)$select === (string)request()->service_provider ? 'selected' : '' }}>{{$option}}</option>
            @endforeach
          </select>
        </div>
      </div>       
      <div class="form-group" style="padding: 25px;">
        <label class="col-sm-2 control-label">Date Range</label>
        <div class="col-sm-10">
        <input type="text" name="daterange" id="daterange" value="" style="width: 50%;"/>
        </div>
      </div>                
      <div class="form-group" style="padding: 23px;">
        <label class="col-sm-2 control-label">Staff</label>
        <div class="col-sm-10">
          <select class="form-control netincome_username" name="username" style="width: 100%;">
            <option></option>
            @foreach(App\Models\ThirdParty::pluck('username','id') as $select => $option)
            <option value="{{$select}}" {{ (string)$select === (string)request()->username ? 'selected' : '' }}>{{$option}}</option>
            @endforeach
          </select>
        </div>
      </div>   
      <div class="form-group" style="padding: 23px;">
        <label class="col-sm-2 control-label">Service</label>
        <div class="col-sm-10">
          <select class="form-control netincome_service_id" name="service_id" style="width: 100%;">
            <option></option>
            @foreach(App\Models\Service::pluck('title','id') as $select => $option)
            <option value="{{$select}}" {{ (string)$select === (string)request()->service_id ? 'selected' : '' }}>{{$option}}</option>
            @endforeach
          </select>
        </div>
      </div>                           
    {{-- </div> --}}
  {{-- </div> --}}
  <div class="modal-footer">
    <div class="btn-group pull-left">
      <button class="btn btn-info submit btn-sm"><i class="fa fa-search"></i>&nbsp;&nbsp;Search</button>
    </div>    
    <div class="btn-group pull-left " style="margin-left: 10px;">
      <a href="{{url('admin/net_income')}}" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;&nbsp;Reset</a>
    </div>
  </div>
</form>
<table class="table" id="net-income-report">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Service - Profission</th>
      <th scope="col">QTY</th>
      @if(!isset(request()->branch_id))      
      <th scope="col">Branch</th>
      @endif
      <th scope="col">Embassy Charge</th>
      <th scope="col">EDC charge (No Vat)</th>
      <th scope="col">Tax Amount</th>
      <th scope="col">Total</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $id = 1;
    $total_income=0;
    $net_total=0;
    $total_tax=0;
    ?>
    @foreach($grouped_requests as $req)
    <?php
    $total_income += $req->sum_amount;
    $branch = $req->branch_id ? Branch::find($req->branch_id)->title : '-';
    $net_total += $req->sum_service_charge;
    $total_tax += $req->sum_tax_amount;
    $service_title =  $req->service ? $req->service->title : '';
    $profession_title =  $req->profession ? $req->profession->title : '';
    ?>    
    <tr>
    @if($req->service_id && $req->profession_id) 
      <th scope="row">{{$id++}}</th>
      <td>{{ $service_title.' - '.$profession_title }}</td>
      <td>{{$req->count_service}}</td>
      @if(!isset(request()->branch_id))      
      <td>{{$branch}}</td>
      @endif
      <td>{{number_format($req->sum_embassy_charge,2)}}</td>
      <td>{{number_format($req->sum_service_charge,2)}}</td>
      <td>{{number_format($req->sum_tax_amount,2)}}</td>
      <td>{{number_format($req->sum_amount,2)}}</td>
    </tr>
    @endif
    @endforeach
    <tr>
      <td colspan="5"><b>Total Income</b></td>
      <td><b>{{number_format($total_income,2)}}</b></td>
    </tr>    
    <tr>
      <td colspan="5"><b>Total Expense</b></td>

      <td><b>{{$expense ? $expense->sum_amount : 0}}</b></td>
    </tr>     
    <tr>
      <td colspan="5"><b>Sub Total</b></td>
      <td><b>{{number_format($total_income - ($expense ? $expense->sum_amount : 0),2)}}</b></td>
    </tr>     
    <tr>
      <td colspan="5"><b>Net Total</b></td>
      <?php
      $net_tax = $total_tax - ($expense ? $expense->sum_tax_amount : 0);
      ?>
      <td><b>{{number_format($net_total - $net_tax,2)}}</b></td>
    </tr>     
  </tbody>
</table>
<script>
  $(document).ready(function() {
    $(".netincome_branch_id").select2({
       placeholder: 'Select Branch'
    });
    $(".netincome_service_provider").select2({
       placeholder: 'Select ServiceProvider'
    });
    $(".netincome_username").select2({
       placeholder: 'Select Staff'
    });
 $(".netincome_service_id").select2({
       placeholder: 'Select Service'
    });

    // $(function() {
  $('#daterange').daterangepicker({
    opens: 'left',
    autoUpdateInput: false
  }, function(start, end, label) {
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  });
   $('#daterange').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
  });
      $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val("");
  });
// });
  });
  // $(".netincome_branch_id").select2();

</script>
