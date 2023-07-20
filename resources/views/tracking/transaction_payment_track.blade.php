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
<style type="text/css">
.logo{
width: 100px;
height: 100px;
/*border-top-left-radius: 100px;
border-top-right-radius: 100px;
border-bottom-right-radius: 100px;
border-bottom-left-radius: 100px;*/
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
      <h6> Address : {{$data->branch->address ?? ''}}</h6>
      <h6> Phone Number : @if($data->branch) {{$data->branch->phone_number ?? ''}} {{isset($data->branch->alt_phone_number) ? ' - '.$data->branch->alt_phone_number  : ''}}@endif</h6>
      <h6><b> Tax No.</b> : {{$info->tax_number ?? ''}}</h6>
    </div>
<div style="text-align: right;">
    {{-- <img src="{{url('uploads/'.$data->qr_image)}}" style="width: 120px;height: 120px;position: absolute;top: 0px;right: 0px;"> --}}
  <div style="width: 120px;height: 120px;position: absolute;top: 0px;right: 0px;">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(135)->generate(url('trackTransaction?qr='.$data->qr_string)); !!}</div>      
    <small style="position: absolute;top: 120px;right: 20px;">Query Status</small>
  </div>
<div>
<div style="text-align: left;display:inline;
float:left;">
SLN: {{ $data->snl ? $data->snl : 'TRA00'.$data->id }}
{{-- <span>{!! DNS1D::getBarcodeHTML($data->snl, 'C128') !!}</span> --}}
<img src="data:image/png;base64,{!! DNS1D::getBarcodePNG($data->snl ? $data->snl : 'TRA00'.$data->id, "C128B") !!}" alt="barcode" style="position: relative;
top: 25px;
right: 128px;" />
</div>
  <div style="text-align: center;">
    <h1 style="position: relative;right: 171;">Payment Voucher</h1>
  </div>
<table class="table table-bordered">
  <tbody>
    <tr>
      <td>Date</td>
      <td colspan="3">{{$data->created_at}}</td>

    </tr>
    <tr>
      <td>Transaction Detail</td>
      <td colspan="3">{{$data->title}}</td>
    </tr>
    <tr>
      <td>Tax Amount</td>
      <td colspan="3">{{$data->tax_amount}}</td>
    </tr>
    <tr>
      <td>Amount</td>
      <td colspan="3">{{$data->amount}}</td>
    </tr>
    <tr>
      <td>Amount In Words</td>
      <td colspan="3" style="color: blue;"><h4>@php
      $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
            echo \Str::upper($f->format($data->amount));
          @endphp</h4></td>
    </tr>
    <tr>
      <td>Notes</td>
      <td colspan="3">{{$data->notes ?? ''}}</td>
    </tr>
  </tbody>
</table>
</body>
</html>
