<?php

use App\Models\Branch;
use App\Models\ServiceProvider;
use App\Models\ThirdParty;
use Encore\Admin\Facades\Admin;
      if (Admin::user()->isAdministrator()) {
          $branches = Branch::pluck('title as text', 'id');
      }
      if (!Admin::user()->isAdministrator()) {
          $branches =ThirdParty::find(Admin::user()->id)->branches()->get()->pluck('title', 'id');
      }
$info = \App\Models\OrganizationDetails::find(1);
$date_range_request = request()->daterange ? explode(' - ', request()->daterange) : [];
$date_range='';
if (count($date_range_request) > 0) {
$date_range = 'From '.$date_range_request[0].' To '.$date_range_request[1];
}
?>
<html lang="en">
<head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

</head>
<style>
table {
  border: 2px solid black !important;
}
 td {
  font-size : 23px;
  border: 2px solid black !important;
/*padding: 0.5rem !important;*/
}
 tr th {
  border: 2px solid black !important;
}
.logo{
width: 113px;
height: 113px;
border: 2px solid white;
float: left;
position: absolute;top: 0px;left: 0px;
}
.org-details{
  position: relative;
left: 17%;
}
</style>

<body>
  <div >
    <img class="logo" src="{{url('uploads/'.$info->logo_1)}}">
  </div>
      <div class="org-details">
      <h2><b>{{$info->title}}</b></h2>
      <h3>{{$info->activity_title}}</h3>
      <h6> Web : {{$info->url}}</h6>
      @if(isset(request()->branch_id))
      <h6> Address : {{isset(request()->branch_id) ? Branch::find(request()->branch_id)->address : ''}}</h6>
      <h6> Phone Number : {{isset(request()->branch_id) ? Branch::find(request()->branch_id)->phone_number : ''}}</h6>      
      @else
      <h6>Branch:<b> All Branches</b></h6>
      @endif
      @if(isset(request()->service_provider))
      <h6>Service Provider : {{ServiceProvider::find(request()->service_provider)->title ?? ''}}</h6>
      @else
      <h6>Service Provider :<b> All Provideres </b></h6>
      @endif      
      <h6><b> Tax No.</b> : {{$info->tax_number ?? ''}}</h6>
    </div>
    <h3 style="text-align: center;">Daily Report {{$date_range}}</h3>
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
      <th scope="col">EDC charge</th>
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
    @forelse($grouped_requests as $req)
    {{-- @dd($req); --}}
    <?php
    $total_income += $req->sum_amount;
    $branch = $req->branch_id ? Branch::find($req->branch_id)->title : '-';
    $net_total += $req->sum_service_charge;
    $total_tax += $req->sum_tax_amount;
    ?>   
    @if($req->service_id && $req->profession_id) 
    <tr>
      <th scope="row">{{$id++}}</th>
      <td>{{$req->service->title.' - '.$req->profession->title }}</td>
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
      <td><b>{{number_format($total_income -  ($expense ? $expense->sum_amount : 0),2)}}</b></td>
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
