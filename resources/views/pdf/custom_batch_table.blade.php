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
  /*font-size: 9px;*/

  /*border: 2px solid black !important;*/
}
 td ,tr,th{
  font-size: 0.7rem;
  font-family: "Times New Roman", Times, serif;
  border: 2px solid black !important;
/*padding: 0.5rem !important;*/
}
</style>
<body>
<br><br><br><br><br><br><br><br><br>
@php
$subject = explode('_', $collection->title);
@endphp
<div style="text-align: center;">
<h4 >Expatriate's Digital Center,{{$branch->title}},{{$branch->address}}</h4>
<h5 >Subject : {{ $subject[2] }}</h5>
<div >Date Of Payment :{{\Carbon\Carbon::parse($collection->batch_date)->format('d-m-Y')}},B/R: {{$collection->bank_ref}}</div>
</div>
<div style="text-align: right">
Batch : {{ $collection->title }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @php
echo "Date:".\Carbon\Carbon::now()->format('d-m-Y H:i A');
@endphp


</div>
  <div >
<table style="width: 100%;text-align: center;">
  <thead>
    <tr>
      <th scope="col" style="width: 30px;">sl no.</th>
     @if(in_array(\App\Models\Batch::Request_NO, $items_display))
      <th scope="col" style="width: 50px;">Request No.</th>
      @endif
      @if(in_array(\App\Models\Batch::FULL_NAME, $items_display))
      <th scope="col">Full Name</th>
      @endif
      @if(in_array(\App\Models\Batch::PASSPORT_NO, $items_display))
      <th scope="col">Passport No.</th>
      @endif
      @if(in_array(\App\Models\Batch::SERVICE, $items_display))
      <th scope="col" style="width: 70px;">Service</th>
      @endif
      @if(in_array(\App\Models\Batch::PHONE_NO, $items_display))
      <th scope="col" style="width: 70px;">Phone No.</th>
      @endif
      @if(in_array(\App\Models\Batch::Enrollment_No, $items_display))
      <th scope="col">Enrollment no.</th>
      @endif
      @if(in_array(\App\Models\Batch::RENEW_NOTE, $items_display))
      <th scope="col">Renew Note</th>
      @endif
      @if(in_array(\App\Models\Batch::Status, $items_display))
      <th scope="col">Status</th>
      @endif
      <th scope="col"  style="width: 45px;">Total</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $id = 0;
    ?>
    @foreach($collection->requests()->get()->sortBy('service_id') as $data)
    <tr>
      <td>{{++$id}}</td>
      @if(in_array(\App\Models\Batch::Request_NO, $items_display))
      <td>{{$data->snl}}</td>
      @endif
      @if(in_array(\App\Models\Batch::FULL_NAME, $items_display))
      <td>{{$data->customer->full_name}}</td>
      @endif
      @if(in_array(\App\Models\Batch::PASSPORT_NO, $items_display))
      <td>{{$data->customer->passport_number}}</td>
      @endif
      @if(in_array(\App\Models\Batch::SERVICE, $items_display))
      <td>{{$data->service->title}}</td>
      @endif
      @if(in_array(\App\Models\Batch::PHONE_NO, $items_display))
      <td>{{$data->customer->phone_number}}</td>
      @endif
      @if(in_array(\App\Models\Batch::Enrollment_No, $items_display))
      <td>{{$data->embassy_serial_number}}</td>
      @endif
      @if(in_array(\App\Models\Batch::RENEW_NOTE, $items_display))
      <td>{{$data->renew_note}}</td>
      @endif
      @if(in_array(\App\Models\Batch::Status, $items_display))
      <td>{{\App\Models\RequestStatus::request_status[$data->request_status_id]  ?? ''}}</td>
      @endif
      <td>{{$data->embassy_charge}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
</div>
<br><br><br>
<table style="width: 100%;text-align: center;">
  <tbody>
        @php
    @endphp
    @foreach($collection->requests()->get()->groupBy('service_id') as $key => $value)
    @foreach($value->groupBy('embassy_charge') as $req)
    <tr>
      <td>{{\App\Models\Service::find($key)->title}}({{$req[0]->embassy_charge}} X {{$req->count()}})</td>
      <td>{{$req->sum('embassy_charge')}}</td>
    </tr>
    @endforeach
    @endforeach

    <tr>
      <td>Net Total</td>
      <td><b>{{$collection->requests()->get()->sum('embassy_charge')}}</b></td>
    </tr>

  </tbody>
</table>
</body>
</html>
