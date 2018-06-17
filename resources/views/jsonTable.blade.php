@if(isset($data['currency']))
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Name</th>
            <th>Unit</th>
            <th>Currency Code</th>
            <th>Country</th>
            <th>Rate</th>
            <th>Change</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data['currency'] as $currency)
            <tr>
                <td>{{$currency['name']}}</td>
                <td>{{$currency['unit']}}</td>
                <td>{{$currency['currencycode']}}</td>
                <td>{{$currency['country']}}</td>
                <td>{{$currency['rate']}}</td>
                <td>{{$currency['change']}}</td>
            </tr>
        @endforeach
        <tr>
            <td>Last Update:</td>
            <td align="right" colspan="5">{{ $data['last_update'] }}</td>
        </tr>
        </tbody>
    </table>
@else
    <h3 style="color:red">Invalid json file!</h3>
@endif