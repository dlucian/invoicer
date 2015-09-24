@extends('layouts.master')

@section('title', 'Settings')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col s12">

                <h1>Settings</h1>

                <table class="striped">
                    <thead>
                    <tr>
                        <th data-field="setting">Setting</th>
                        <th data-field="value">Value</th>
                        <th data-field="options" class="center-align">Options</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($settings as $name => $value)
                        <tr>
                            <td>{{$name}}</td>
                            <td><pre>{{$value}}</pre></td>
                            <td class="center-align"><a href="{{route('setting-update',$name)}}"
                                class="waves-effect waves-teal btn-flat"><i class="material-icons left">mode_edit</i> change</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    @include('invoices/components/add-invoice')
@endsection
