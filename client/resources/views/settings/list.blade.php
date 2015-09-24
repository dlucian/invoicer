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
                            <td class="center-align">
                                <a href="{{route('setting-update',$name)}}" class="waves-effect waves-teal btn-flat"><i class="material-icons">mode_edit</i></a>
                                <a href="{{route('setting-delete',$name)}}" class="waves-effect waves-teal btn-flat delete-setting"><i class="material-icons">delete</i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
        <a href="{{route('setting-update','new')}}" class="btn-floating btn-large red tooltipped" data-position="left" data-delay="50" data-tooltip="New Setting">
            <i class="large material-icons">add</i>
        </a>
    </div>
@endsection



@section('scripts')
    <script type="text/javascript">
        $( document ).ready(function() {
            $('.delete-setting').click(function(e) {
                if (!confirm('Are you sure?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection
