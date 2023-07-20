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
  body{

    font-family: serif;
    font-weight: bold;
  }
 td {
  font-size: 18px;
  border: 3px solid black !important;
}
.logo{
width: 100px;
height: 100px;
border-radius: 50%;
float: left;
}
.qr_code{
  width: 100px;height: 100px;position: relative;bottom: 70px;
}
</style>
<body>
  <div >
    <img class="logo" src="{{public_path('uploads/'.$info->logo_1)}}">
      </div>
      <div>
    <div>
      {{$info->title}}
    </div>
    <div>
      {{$info->activity_title}}
    </div>
    <div>
      Passport query Link : {{$info->url}}
    </div>
    </div>
<div style="text-align: right;">
    <img class="qr_code" src="{{public_path('uploads/requests_qrCode/request_num1_swmm7.png')}}">
    <small>Query Status</small>
  </div>
  <div>
<div style="text-align: left;display:inline;
float:left;">
SLN: 123456
{!!DNS1D::getBarcodeHTML(123456, 'C128')!!}
</div>
<div style="text-align: right;display:inline;
float: right;
position: relative;
top: 29px;">
@php
echo "Copy date:".\Carbon\Carbon::now()->format('d-m-Y H:i A');
@endphp
</div>
</div>
<table class="table table-bordered">
  <tbody>
    <tr>
      <td>{{\Str::upper('Receipt Id')}}:</td>
      <td>2585541258</td>
      <td>Branch:</td>
      <td>Main</td>
    </tr>
    <tr>
      <td>Name:</td>
      <td>Jacob</td>
      <td>Mobile Number:</td>
      <td>258741236985</td>
    </tr>
    <tr>
      <td>Service:</td>
      <td>Renew</td>
      <td>Service Of Type : </td>
      <td colspan="2">Out Side</td>
    </tr>
    <tr>
      <td>Passport Number:</td>
      <td>as 321345467613</td>
      <td>Profession:</td>
      <td>Worker</td>
    </tr>
    <tr>
      <td>Passport Fee : </td>
      <td colspan="3">222</td>
    </tr>
    <tr>
      <td>Service Charge : </td>
      <td colspan="3">100</td>
    </tr>
    <tr>
      <td>Total Charge Of Service : </td>
      <td colspan="3">322</td>
    </tr>
    <tr>
      <td>Delivery On (SMS):</td>
      <td>321345467613</td>
      <td>Signature receiver:</td>
      <td>USER :ADMIN</td>
    </tr>
  </tbody>
</table>

<div>
    <img src="{{public_path('uploads/receipt_img.jpeg')}}" style="width: auto;height: auto">
    </div>


</body>
</html>
