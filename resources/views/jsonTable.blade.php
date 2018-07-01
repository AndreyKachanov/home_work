@if(isset($uploadedData))
    <p>Your uploaded file:</p>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Name</th>
            <th>Unit</th>
            <th>Currency Code</th>
            <th>Country</th>
            <th>Rate</th>
            <th>Change</th>
            @if(!isset($uploadedData['last_update']))
                <th>Last update</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($uploadedData['currency'] as $currency)
            <tr>
                <td>{{$currency['name']}}</td>
                <td>{{$currency['unit']}}</td>
                <td>{{$currency['currencycode']}}</td>
                <td>{{$currency['country']}}</td>
                <td>{{$currency['rate']}}</td>
                <td>{{$currency['change']}}</td>
                @isset($currency['last_update'])
                    <td>{{$currency['last_update']}}</td>
                @endisset
            </tr>
        @endforeach
        @isset($uploadedData['last_update'])
            <tr>
                <td>Last Update:</td>
                <td align="right" colspan="5">{{ $uploadedData['last_update'] }}</td>
            </tr>
        @endisset

        </tbody>
    </table>
    <p>
        <p>Updated file:</p>
        <a href="{{ config('app.files_path_new') .  $updatedFile }}" download="{{ config('app.files_path_new') .  $updatedFile }}">
            {{ $updatedFile }}
        </a>
    </p>
@else
    <h3 style="color:red">Invalid file format!</h3>
@endif

