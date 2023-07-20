
<div style='padding: 10px;text-align:left;font-weight: bold'><div style='color:red;'>Total Requests:  {{$report_details['total_requests']}}</div><div style='color:green;'>Service Charge (No Vat) : {{$report_details['service_charge']}} </div><div style='color:orange;'>Provider Charge : {{$report_details['embassy_charge']}} </div>
             <div style='color:blue;'> Tax Amount: {{$report_details['tax_amount']}}</div>
            <div style='color:black;'> Total: {{$report_details['amount']}}</div></div>
<table class='table' style='width:25%;position: relative;
left: 40%;
bottom: 123px;'>
  <thead>
    <tr>
      <th scope='col'>Service Title</th>
      <th scope='col'>Count</th>
    </tr>
  </thead>
  <tbody>
    @foreach($report_details['table'] as $detail)
    <tr>
      <td>{{$detail['title']}}</td>
      <td>{{$detail['count']}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
