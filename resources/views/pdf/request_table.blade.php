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
  font-size: 9px;
  border: 2px solid black !important;
/*padding: 0.5rem !important;*/
}
</style>
<body>
  <div class="table-responsive-sm">
<table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">Request No.</th>
      <th scope="col">Request Date</th>
      <th scope="col">Full Name</th>
      <th scope="col">Service</th>
      <th scope="col">Total</th>
      <th scope="col">Status</th>
      <th scope="col">Enrollment no.</th>
      <th scope="col">Embassy</th>
      {{-- <th scope="col">Request QrCode</th> --}}

    </tr>
  </thead>
  <tbody>
    @foreach($collection as $data)
    <tr>
      <td>{{$data->snl}}</td>
      <td>{{\Carbon\Carbon::parse($data->request_created_at)->format('d-m-Y')}}</td>
      <td>{{$data->customer->full_name}}</td>
      <td>{{$data->service->title}}</td>
      <td>{{$data->amount}}</td>
      <td>{{\App\Models\RequestStatus::request_status[$data->request_status_id]}}</td>
      <td>{{$data->embassy_serial_number}}</td>
      <td>{{$data->embassy->title}}</td>
      {{-- <td><img src="{{public_path('uploads/'.$data->qr_image)}}" style="width: 50px;height: 50px"></td> --}}
    </tr>
    @endforeach
  </tbody>
</table>
</div>
</body>
</html>
