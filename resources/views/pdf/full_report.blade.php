<?php
$info = \App\Models\OrganizationDetails::find(1);
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
  /*font-size: 12px;*/
   border-collapse: collapse;
  /*border: 2px solid black !important;*/
}
td ,tr,th{
  font-size: 1em;
  border: 2px solid black !important;
/*padding: 0.5rem !important;*/
}
</style>
<body onload="window.print()">
<div>
    <h2><b>{{$info->title}}</b></h2>
    <h3>{{$info->activity_title}}</h3>
</div>

@if(collect($collection)->groupBy('branch_id')->count() > 1 )
<h4>All Branches</h4>
@endif
@if(collect($collection)->groupBy('branch_id')->count() == 1)
<h4>{{\App\Models\Branch::find($collection->first()->branch_id)->title}}</h4>
@endif
<div style="text-align: center ">
@php
if(count($collection) > 0){
echo "<b>From:</b>".\Carbon\Carbon::parse(collect($collection)->sortBy('request_created_at')->first()->request_created_at)->format('d-m-Y H:i A'); echo " <b>To:</b>".\Carbon\Carbon::parse(collect($collection)->sortBy('request_created_at')->last()->request_created_at)->format('d-m-Y H:i A');
}else{
$requests_all = json_decode(request()->all()['collection']);
$start = $requests_all->request_created_at->start ? \Carbon\Carbon::parse($requests_all->request_created_at->start)->format('d-m-Y H:i A')  : \Carbon\Carbon::now()->format('d-m-Y H:i A');

$end = $requests_all->request_created_at->end ? \Carbon\Carbon::parse($requests_all->request_created_at->end)->format('d-m-Y H:i A') : \Carbon\Carbon::now()->format('d-m-Y H:i A');
echo "<b>From:</b>".$start; echo " <b>To:</b>".$end ;  
}

@endphp
</div>
<div style="text-align: right">
@php
echo "Date:".\Carbon\Carbon::now()->format('d-m-Y H:i A');
@endphp
</div>
  <div >
<table class="table " style="width: 100%;text-align: center;" >
  <thead>
    <tr>
      <th scope="col" style="width: 30px;">sl no.</th>
     @if(in_array(\App\Models\Requests::Request_NO, $items_display))
      <th scope="col" style="width: 50px;">Request No.</th>
      @endif
     @if(in_array(\App\Models\Requests::Request_Date, $items_display))
      <th scope="col" style="width: 50px;">Request Date</th>
      @endif
      @if(in_array(\App\Models\Requests::FULL_NAME, $items_display))
      <th scope="col">Full Name</th>
      <th scope="col">Phone Number</th>
      <th scope="col">Document No.</th>
      @endif
      @if(in_array(\App\Models\Requests::SERVICE, $items_display))
      <th scope="col" style="width: 70px;">Service</th>
      @endif
      @if(in_array(\App\Models\Requests::Service_Location, $items_display))
      <th scope="col">Service Location</th>
      @endif
      @if(in_array(\App\Models\Requests::Status, $items_display))
      <th scope="col" style="width: 70px;">Status</th>
      @endif
      @if(in_array(\App\Models\Requests::Enrollment_No, $items_display))
      <th scope="col">Enrollment no.</th>
      @endif
      @if(in_array(\App\Models\Requests::Embassy, $items_display))
      <th scope="col">Embassy</th>
      @endif
      @if(in_array(\App\Models\Requests::Batch_Ref_No, $items_display))
      <th scope="col">Batch Ref No.</th>
      @endif
      @if(in_array(\App\Models\Requests::service_charge, $items_display))
      <th scope="col">Service Charge</th>
      @endif
      @if(in_array(\App\Models\Requests::embassy_charge, $items_display))
      <th scope="col">Embassy Charge</th>
      @endif
      @if(in_array(\App\Models\Requests::TAX_AMOUNT, $items_display))
      <th scope="col">Tax Amount</th>
      @endif
      @if(in_array(\App\Models\Requests::Total, $items_display))
      <th scope="col">Total</th>
      @endif
      @if(in_array(\App\Models\Requests::USERNAME, $items_display))
      <th scope="col">USER</th>
      @endif
    </tr>
  </thead>
  <tbody>
    <?php
    $id = 0;
    ?>
    @if(count($collection) > 0)
    @foreach($collection as $req)
    <tr>
      <td>{{++$id}}</td>
      @if(in_array(\App\Models\Requests::Request_NO, $items_display))
      <td>{{ $req->snl }}</td>
      @endif
      @if(in_array(\App\Models\Requests::Request_Date, $items_display))
      <td>{{\Carbon\Carbon::parse($req->request_created_at)->format('d-m-Y')}}</td>
      @endif
      @if(in_array(\App\Models\Requests::FULL_NAME, $items_display))
      <td>{{$req->customer->full_name ?? ''}}</td>
      <td>{{$req->customer->phone_number ?? ''}}</td>
      <td>{{$req->customer->passport_number ?? ''}}</td>
      @endif
      @if(in_array(\App\Models\Requests::SERVICE, $items_display))
      <td>{{$req->service->title ?? ''}}</td>
      @endif
      @if(in_array(\App\Models\Requests::Service_Location, $items_display))
      <td>{{$req->service_type->title ?? ''}}</td>
      @endif
      @if(in_array(\App\Models\Requests::Status, $items_display))
      <td>{{\App\Models\RequestStatus::request_status[$req->request_status_id] ?? ''}}</td>
      @endif
      @if(in_array(\App\Models\Requests::Enrollment_No, $items_display))
      <td>{{$req->embassy_serial_number}}</td>
      @endif
      @if(in_array(\App\Models\Requests::Embassy, $items_display))
      <td>{{$req->embassy->title ?? ''}}</td>
      @endif
      @if(in_array(\App\Models\Requests::Batch_Ref_No, $items_display))
      <td>{{$req->batch->title ?? ''}}</td>
      @endif
      @if(in_array(\App\Models\Requests::service_charge, $items_display))
      <td>{{$req->service_charge}}</td>
      @endif
      @if(in_array(\App\Models\Requests::embassy_charge, $items_display))
      <td>{{$req->embassy_charge}}</td>
      @endif
      @if(in_array(\App\Models\Requests::TAX_AMOUNT, $items_display))
      <th scope="col">{{$req->tax_amount}}</th>
      @endif
      @if(in_array(\App\Models\Requests::Total, $items_display))
      <td>{{$req->amount}}</td>
      @endif
      @if(in_array(\App\Models\Requests::USERNAME, $items_display))
      <td>{{$req->username->username ?? ''}}</td>
      @endif
    </tr>
    @endforeach
    @endif
  </tbody>
</table>
</div>
<?php

            $services = collect($collection)->groupBy('service_id')->toArray();
            foreach ($services as $key => $value) {
                $service_title = \App\Models\Service::find($key)->title;
                $service_count = count($value);
                $detail_table[] =  ['title' => $service_title,'count' => $service_count];
            }
?>
<div></div>
<br><br><br>
<table style='width:20% !important;padding: 20px' class="table">
  <thead>
    <tr>
      <th scope='col'>Service Title</th>
      <th scope='col'>Count</th>
    </tr>
  </thead>
  <tbody>
    @if(isset($detail_table) && count($detail_table) > 0)
    @foreach($detail_table as $detail)
    <tr>
      <td>{{$detail['title']}}</td>
      <td>{{$detail['count']}}</td>
    </tr>
    @endforeach
    @endif
  </tbody>
</table>
<?php

            $services = collect($collection)->groupBy('service_id')->toArray();
           $amount = round(collect($collection)->sum('amount'),2) ?? 00;
            $embassy_charge = round(collect($collection)->sum('embassy_charge'),2) ?? 00;
            $service_charge = round(collect($collection)->sum('service_charge'),2) ?? 00;
            $tax_amount = round(collect($collection)->sum('tax_amount'),2) ?? 00;
            $total_requests = collect($collection)->count() ;
?>
<div style='padding: 10px;text-align:left;font-weight: bold'><div style='color:red;'>Requests Count: {{$total_requests}} </div><div style='color:green;'>Service Charge : {{$service_charge}} </div><div style='color:orange;'>Embassy Charge : {{$embassy_charge}} </div>
<div style='color:blue;'> Tax Amount: {{$tax_amount}}</div>
            <div style='color:black;'> Total: {{$amount}}</div></div>
</body>
</html>
