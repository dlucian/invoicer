@extends('layouts.master')

@section('title', 'Settings')

@section('content')
    <div class="container">
        <div class="row">
            <form class="col s12" action="{{route('setting-update', $settingName)}}" method="post">
                @if ($settingName == 'new')
                    <h1>New Setting</h1>
                @else
                    <h1>Setting: {{$settingName}}</h1>
                @endif


                <div class="row">
                    {{csrf_field()}}
                    @if ($settingName == 'new')
                        <div class="input-field col s6">
                            <textarea id="setting-name" name="name" class="materialize-textarea" placeholder="setting_name"></textarea>
                            <label for="setting-name">Name</label>
                        </div>
                    @endif
                    <div class="input-field col s6">
                        <textarea id="setting-value" name="value" class="materialize-textarea">{{$settings[$settingName] or ''}}</textarea>
                        <label for="setting-value">Value</label>
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
