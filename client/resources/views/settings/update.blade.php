@extends('layouts.master')

@section('title', 'Settings')

@section('content')
    <div class="container">
        <div class="row">
            <form class="col s12" action="{{route('setting-update', $settingName)}}" method="post">
                <h1>Setting: {{$settingName}}</h1>

                <div class="row">
                    {{csrf_field()}}
                    <div class="input-field col s6">
                        <textarea id="setting-value" name="value" class="materialize-textarea">{{$settings[$settingName]}}</textarea>
                        <label for="setting-value">Setting Value</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6">
                        <button type="submit" class="waves-effect waves-light btn-large"><i class="material-icons left">cloud</i>save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
