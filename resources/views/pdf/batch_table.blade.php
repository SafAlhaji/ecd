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
 td ,tr,th{
  font-size: 13px;
  border: 2px solid black !important;
/*padding: 0.5rem !important;*/
}
</style>
<body>
<br><br><br>
  <h3>Batch : {{ $collection->title }}</h3>
  <div style="text-align: right">
@php
echo "Date:".\Carbon\Carbon::now()->format('d-m-Y H:i A');
@endphp
</div>
  <div >
<table  style="width: 100%;text-align: center;" class="table">
  <thead>
    <tr>
      <th scope="col">sl no.</th>
      <th scope="col">Request No.</th>
      <th scope="col">Full Name</th>
      <th scope="col">Passport No.</th>
      <th scope="col">Service</th>
      <th scope="col">Total</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $id = 0;
    ?>
    @foreach($collection->requests as $data)
    <tr>
      <td>{{++$id}}</td>
      <td>{{$data->snl}}</td>
      <td>{{$data->customer->full_name}}</td>
      <td>{{$data->customer->passport_number}}</td>
      <td>{{$data->service->title}}</td>
      <td>{{$data->embassy_charge}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
</div>
<br><br><br>
<table style="width: 100%;text-align: center;" class="table">
  <tbody>
    @php
    print_r($collection->requests()->get()->groupBy('service_id','profession_id'));
    @endphp
    @foreach($collection->requests()->get()->groupBy('service_id','profession_id') as $key => $value)
    <tr>
      <td>{{\App\Models\Service::find($key)->title}}({{$value[0]->embassy_charge}} X {{$value->count()}})</td>
      <td>{{$value->sum('embassy_charge')}}</td>
    </tr>
    @endforeach
    <tr>
      <td>Net Total</td>
      <td>{{$collection->requests()->get()->sum('embassy_charge')}}</td>
    </tr>

  </tbody>
</table>
</body>
</html>
