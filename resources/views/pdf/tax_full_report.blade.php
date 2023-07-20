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
  /*font-size: 9px;*/
  /*border: 2px solid black !important;*/
     border-collapse: collapse;
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
<div style="text-align: center ">
@php
echo "<b>From:</b>".\Carbon\Carbon::parse(collect($collection)->sortBy('created_at')->first()->created_at)->format('d-m-Y H:i A'); echo " <b>To:</b>".\Carbon\Carbon::parse(collect($collection)->sortBy('created_at')->last()->created_at)->format('d-m-Y H:i A');
@endphp
</div>
<div style="text-align: right">
@php
echo "Date:".\Carbon\Carbon::now()->format('d-m-Y H:i A');
@endphp
</div>
  <div >
<table style="width: 100%;text-align: center;" >
  <thead>
    <tr>
      <th scope="col" style="width: 30px;">sl no.</th>
      <th scope="col" style="width: 50px;">Transaction No.</th>
      <th scope="col" style="width: 50px;">Date</th>
      <th scope="col" style="width: 50px;">Amount</th>
      <th scope="col" style="width: 50px;">Tax Amount</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $id = 0;
    ?>
    @foreach($collection as $trans)
    <tr>
      <td>{{++$id}}</td>
      @if($trans->request)
      <td>{{$trans->request->snl}}</td>
      @else
      <td>{{$trans->snl}}</td>
      @endif
      @if($trans->request)
      <td>{{$trans->request->request_created_at}}</td>
      @else
      <td>{{date_format(date_create($trans->created_at), 'Y-m-d')}}</td>
      @endif
      @if($trans->request)
      <td>{{$trans->amount}}</td>
      @else
      <td>{{$trans->amount - $trans->tax_amount}}</td>
      @endif
      <td>{{$trans->tax_amount}}</td>

    </tr>
    @endforeach
  </tbody>
</table>
</div>
<div></div>
<br><br><br>
<?php
            session(['footerqueryprint' => $collection]);
            $total_requests_get = collect($collection)->where('transaction_type', \App\Models\TransactionsHistory::MONEY_IN);
            $total_expenses = collect(session()->get('footerqueryprint'))->where('transaction_type', \App\Models\TransactionsHistory::MONEY_OUT);
            $total_requests_amount = $total_requests_get->sum('amount');
            $total_requests_tax = $total_requests_get->sum('tax_amount');
            $total_expensess_amount = $total_expenses->sum('amount');
            $total_expensess_tax = $total_expenses->sum('tax_amount');
            $net_tax = $total_requests_tax - $total_expensess_tax;
            session()->forget('footerqueryprint');
?>
<div style='padding: 10px;text-align:left;font-weight: bold'><div style='color:red;'>Total Requests Amount: {{$total_requests_amount}} </div><div style='color:green;'>Total Requests Tax  : {{$total_requests_tax}} </div><div style='color:orange;'>Total Expenses Amount : {{$total_expensess_amount}} </div>
<div style='color:blue;'> Total Expenses Tax: {{$total_expensess_tax}}</div>
            <div style='color:black;'> Net Tax: {{$net_tax}}</div></div>
</body>
</html>
