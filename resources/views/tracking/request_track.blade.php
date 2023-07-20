
<?php
$info = \App\Models\OrganizationDetails::find(1);
$invoice_data = \App\Models\InvoiceSetup::find(1);

?>

<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">
<style type="text/css">
body {
    color: #000;
    overflow-x: hidden;
    height: 100%;
    background-repeat: no-repeat
}

.card {
    z-index: 0;
    background-color: #ECEFF1;
    padding-bottom: 20px;
    margin-top: 90px;
    margin-bottom: 90px;
    border-radius: 10px
}

.top {
    padding-top: 40px;
    padding-left: 13% !important;
    padding-right: 13% !important
}

#progressbar {
    margin-bottom: 30px;
    overflow: hidden;
    color: #455A64;
    padding-left: 0px;
    margin-top: 30px
}

#progressbar li {
    list-style-type: none;
    font-size: 13px;
    width: 20%;
    float: left;
    position: relative;
    font-weight: 400
}

#progressbar .step0:before {
    font-family: FontAwesome;
    content: "\f10c";
    color: #fff
}

#progressbar li:before {
    width: 40px;
    height: 40px;
    line-height: 45px;
    display: block;
    font-size: 20px;
    background: #C5CAE9;
    border-radius: 50%;
    margin: auto;
    padding: 0px
}

#progressbar li:after {
    content: '';
    width: 100%;
    height: 12px;
    background: #C5CAE9;
    position: absolute;
    left: 0;
    top: 16px;
    z-index: -1
}

#progressbar li:last-child:after {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
    position: absolute;
    left: -50%
}

#progressbar li:nth-child(2):after,
#progressbar li:nth-child(3):after,
#progressbar li:nth-child(4):after {
    left: -50%
}

#progressbar li:first-child:after {
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
    position: absolute;
    left: 50%
}

#progressbar li:last-child:after {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px
}

#progressbar li:first-child:after {
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px
}

#progressbar li.active:before,
#progressbar li.active:after {
    background: #651FFF
}

#progressbar li.active:before {
    font-family: FontAwesome;
    content: "\f00c"
}

.icon {
    width: 60px;
    height: 60px;
    margin-right: 15px
}

.icon-content {
    padding-bottom: 20px
}

@media screen and (max-width: 992px) {
    .icon-content {
        width: 20%
    }
}
.pricing {
    background-color: #ddd3;
    padding: 2vh 12%;
    font-weight: 400;
    line-height: 2.5
}

.pricing .col-3 {
    padding: 0
}

.total {
    padding: 2vh 12%;
    font-weight: bold
}

.total .col-3 {
    padding: 0
}
</style>
    <title>Track Request</title>
  </head>
  <body>
<div class="container px-1 px-md-4 py-5 mx-auto">
    <div class="card">
        <div class="title" style="text-align: center;font-size: 3rem">Request Reciept</div>
        <div class="row d-flex justify-content-between px-3 top">
             <div class="row">
            <div class="col-6"> <span>{{$info->title}}</span> </div>
            <div class="col-6"> <span>{{$info->activity_title}}</span> </div>
            <div class="col-6"> <span> Web : {{$info->url}}</span> </div>
            <div class="col-6"> <span>Address : {{$customer_request->branch->address ?? ''}}</span> </div>
            <div class="col-6"> <span>Phone Number : @if($customer_request->branch) {{$customer_request->branch->phone_number ?? ''}}@endif</span> </div>
            <div class="col-6"> <span> Tax No.: {{$info->tax_number ?? ''}}</span> </div>
            <div class="col-6"> <span> Date: {{$customer_request->request_created_at ?? ''}}</span> </div>
        </div>
<table class="table table-bordered">
  <tbody>
    <tr>
      <td style="width: 20%">{{\Str::upper('Receipt Id:')}}</td>
      <td style="width: 25%">{{$customer_request->snl}}</td>
      <td style="width: 25%">{{\Str::upper('Branch:')}}</td>
      <td style="width: 30%"> {{$customer_request->branch->title ?? 'Admin Branch'}} </td>
    </tr>
    <tr>
      <td>{{\Str::upper('Name:')}}</td>
      <td colspan="3" >{{$customer_request->customer->full_name ?? ''}}</td>
    </tr>
    <tr>
      <td>{{\Str::upper('type of Service')}}:</td>
      <td>{{$customer_request->service->title ?? ''}}</td>
      <td>{{\Str::upper('Mobile Number:')}}</td>
      <td>{{$customer_request->customer->phone_number ?? ''}} - {{$customer_request->customer->alt_phone_number ?? ''}}</td>
    </tr>
    <tr>
      <td>{{\Str::upper('Passport No.')}}:</td>

      @if($customer_request->request_type_id == 1)
      <td>{{$customer_request->customer->passport_number ?? ''}}</td>
      <td>{{\Str::upper('Profession:')}}</td>
      <td>{{$customer_request->profession->title ?? ''}}</td>
      @else
      <td colspan="3">{{$customer_request->customer->passport_number ?? ''}}</td>
      @endif
    </tr>
    <tr>
      <td>{{\Str::upper('Passport Fee :')}}</td>
      <td colspan="3">{{$customer_request->embassy_charge ?? ''}} {!! $invoice_data->currency ? $invoice_data->currency : '' !!}</td>
    </tr>
    <tr>
      <td>{{\Str::upper('Service Charge ')}}: </td>
      <td colspan="1">{{$customer_request->service_charge ?? ''}} {!! $invoice_data->currency ? $invoice_data->currency : '' !!}</td>
      <td>{{\Str::upper('Tax Amount ')}}: </td>
      <td colspan="1">{{$customer_request->tax_amount ?? ''}} {!! $invoice_data->currency ? $invoice_data->currency : '' !!}</td>
    </tr>
    <tr>
      <td>{{\Str::upper('Total Fee')}}:</td>
       <td colspan="3" >{{$customer_request->amount}} {!! $invoice_data->currency ? $invoice_data->currency : '' !!}</td>
    </tr>
  </tbody>
</table>  
        </div>
{{--     <div class="pricing">
        <div class="row">
            <div class="col-9"> <span id="name">{{\Str::upper('Passport Fee :')}}</span> </div>
            <div class="col-3"> <span id="price">{{ $customer_request->service_charge }} SAR</span> </div>
        </div>
        <div class="row">
            <div class="col-9"> <span id="name">{{\Str::upper('Service Charge : ')}}</span> </div>
            <div class="col-3"> <span id="price">{{ $customer_request->embassy_charge }} SAR</span> </div>
        </div>
    </div>
    <div class="total">
        <div class="row">
            <div class="col-9"></div>
            <div class="col-9"> <span id="name">{{\Str::upper('Total Fee: ')}}</span> </div>
            <div class="col-3"><big>{{ $customer_request->amount }} SAR</big></div>
        </div>
    </div> --}}
        <div class="row d-flex justify-content-center">
            <div class="col-12">
                <ul id="progressbar" class="text-center">
                    <li class="active step0">Pending</li>
                    @for($i=3;$i <= 5;$i++)
                    <li class="step0 {{($customer_request->request_status_id >= $i) ? 'active' : ''}}">{{\App\Models\RequestStatus::request_status[$i] ?? ''}}</li>
                    @endfor
                    </ul>
            </div>
        </div>
{{--         <div class="row justify-content-between top">
            <div class="row d-flex icon-content"> <img class="icon" src="{{asset('images/pending.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">Pending</p>
                </div>
            </div>
            <div class="row d-flex icon-content"> <img class="icon" src="{{asset('images/processing.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">Processing </p>
                </div>
            </div>
            <div class="row d-flex icon-content"> <img class="icon" src="{{asset('images/in_embassy.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">In Embassy</p>
                </div>
            </div>
            <div class="row d-flex icon-content"> <img class="icon" src="{{asset('images/at_office.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">At Office</p>
                </div>
            </div>
            <div class="row d-flex icon-content"> <img class="icon" src="{{asset('images/completed.png')}}">
                <div class="d-flex flex-column">
                    <p class="font-weight-bold">Completed</p>
                </div>
            </div>
        </div> --}}
    </div>
</div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" crossorigin="anonymous"></script>
  </body>
</html>
