<?php
$info = \App\Models\OrganizationDetails::find(1);
$invoice_data = \App\Models\InvoiceSetup::find(1);

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
/* tr {
  border: 2px solid black !important;
}*/
.logo{
width: 170px;
height: 170px;
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
{{-- onload="window.print();" onfocus="setTimeout(window.close(), 3000) --}}
<body>

  <div >
    <img class="logo" src="{{url('uploads/'.$info->logo_1)}}">
  </div>
      <div class="org-details">
      <h2><b>{{$info->title}}</b></h2>
      <h3>{{$info->activity_title}}</h3>
      <h6> Web : {{$info->url}}</h6>
      <h6> Address : {{$data->branch->address ?? ''}}</h6>
      <h6> Phone Number : @if($data->branch) {{$data->branch->phone_number ?? ''}}@endif</h6>
      <h6><b> Tax No.</b> : {{$info->tax_number ?? ''}}</h6>
    </div>
<div style="text-align: right;">
  <div style="width: 160px;height: 160px;position: absolute;top: 0px;right: 0px;">
    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->generate($data->zatca_qr_text()); !!}
{{-- <img src="data:image/png;base64,{{DNS2D::getBarcodePNG($data->zatca_qr_text(), 'QRCODE')}}"> --}}
  </div>   
    {{-- <small style="position: absolute;top: 140px;right: 32px;">Query Status</small> --}}
  </div>
<div>
<div style="text-align: left;display:inline;
float:left;">
SLN: {{ $data->snl }}
{{-- <span>{!! DNS1D::getBarcodeHTML($data->snl, 'C128') !!}</span> --}}
<img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($data->snl, "C128B")}}" alt="barcode" style="position: relative;
top: 25px;
right: 113px;" />
<span style="position: relative;

left: 10px;font-size: 35px"> 
    {{$info->invoice_title ?? ''}}
  </span>
  <br>
<span style="position: relative;

left: 60%;font-size: 35px"> 
    
    {{$info->invoice_title_ar ?? ''}}
  </span>
</div>

<div style="text-align: right;display:inline;
float: right;
position: relative;
bottom: 25px;">
@php
echo "Date Request:".$data->request_created_at."<br>";
echo "Print Date:".\Carbon\Carbon::now()->format('d-m-Y H:i A')."<br>";;
@endphp
</div>

</div>
<br><br><br>
<table class="table table-bordered">
  <tbody>
    <tr>
      <td style="width: 20%">{{\Str::upper('Receipt Id:')}}</td>
      <td style="width: 25%">{{$data->snl}}</td>
      <td style="width: 25%">{{\Str::upper('Branch:')}}</td>
      <td style="width: 30%"> {{$data->branch->title ?? 'Admin Branch'}} </td>
    </tr>
    <tr>
      <td>{{\Str::upper('Name:')}}</td>
      <td colspan="3" >{{$data->customer->full_name ?? ''}}</td>
    </tr>
    <tr>
      <td>{{\Str::upper('type of Service')}}:</td>
      <td>{{$data->service->title ?? ''}}</td>
      <td>{{\Str::upper('Mobile Number:')}}</td>
      <td>{{$data->customer->phone_number ?? ''}} - {{$data->customer->alt_phone_number ?? ''}}</td>
    </tr>
    <tr>
      <td>{{\Str::upper('Passport No.')}}:</td>

      @if($data->request_type_id == 1)
      <td>{{$data->customer->passport_number ?? ''}}</td>
      <td>{{\Str::upper('Profession:')}}</td>
      <td>{{$data->profession->title ?? ''}}</td>
      @else
      <td colspan="3">{{$data->customer->passport_number ?? ''}}</td>
      @endif
    </tr>
    <tr>
      <td>{{\Str::upper('Passport Fee :')}}</td>
      <td colspan="3">{{$data->embassy_charge ?? ''}} {!! $invoice_data->currency ? $invoice_data->currency : '' !!}</td>
    </tr>
    <tr>
      <td>{{\Str::upper('Service Charge')}}: </td>
      <td colspan="1">{{$data->service_charge ?? ''}} {!! $invoice_data->currency ? $invoice_data->currency : '' !!}</td>
      <td>{{\Str::upper('Tax Amount ')}}: </td>
      <td colspan="1">{{$data->tax_amount ?? ''}} {!! $invoice_data->currency ? $invoice_data->currency : '' !!}</td>
    </tr>
    <tr>
      <td>{{\Str::upper('Total Fee')}}:</td>
       <td colspan="3" >{{$data->amount}} {!! $invoice_data->currency ? $invoice_data->currency : '' !!}</td>
    </tr>
    <tr>
       
      <td>{{\Str::upper('Delivery On (SMS):')}}</td>
      <td>{{\Carbon\Carbon::parse($data->delivery_date_time)->format('d-m-Y')}}</td>
      <td>{{\Str::upper('Signature receiver:')}}</td>
      <td>USER : {{ isset($data->username) ? $data->username->name : 'Admin'  }} </td>
    </tr>
  </tbody>
</table>
<div  style="width: 100%;">
  <h3>{!! $invoice_data ? $invoice_data->title : '' !!}</h3>
    {{-- <img src="{{url('uploads/receipt_img.jpeg')}}"> --}}
    </div>
<div>

    </div>
</body>
</html>
