@extends('layouts.master')

@section('title', 'Invoice')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col s12">
                <h1>@if (!empty($invoice['invoice'])) Invoice {{$invoice['invoice']}} @else New Invoice @endif</h1>

                @if (count($errors) > 0)
                    <div class="card red lighten-2 white-text">
                        <div class="card-content white-text">
                            <span class="card-title white-text">Could not save information</span>
                            @foreach ($errors->all() as $error)
                                <p>{{$error}}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="post" action="{{route('invoice-update', !empty($invoice['invoice']) ? $invoice['invoice'] : 0)}}" id="invoice-update">
                    {{csrf_field()}}
                    <div class="section">
                        <h4>Identification</h4>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input placeholder="{{$settings['invoice_prepend']}}" disabled id="invoice" name="invoice" type="text" class="validate"
                                       value="{{$invoice['invoice'] or $settings['invoice_prepend'] . sprintf('%0'. $settings['invoice_digits'] .'d', $settings['next_invoice'])}}">
                                <label for="invoice">Invoice no.</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input id="issued_on" name="issued_on" type="date" class="validate datepicker" value="{{Input::old('issued_on', !empty($invoice)?$invoice['issued_on']:date('Y-m-d'))}}"
                                       data-value="{{Input::old('issued_on', !empty($invoice)?$invoice['issued_on']:date('Y-m-d'))}}">
                                <label for="issued_on">Issue Date</label>
                            </div>
                        </div>
                    </div>

                    <div class="section">
                        <h4>Recipients</h4>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input id="seller_name" name="seller_name" type="text" class="validate" value="{{Input::old('seller_name', !empty($invoice)?$invoice['seller_name']:$settings['seller_name'])}}">
                                <label for="seller_name">Seller Name</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input id="buyer_name" name="buyer_name" type="text" class="validate" value="{{Input::old('buyer_name', !empty($invoice)?$invoice['buyer_name']:'')}}">
                                <label for="buyer_name">Buyer Name</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12 m6">
                                <textarea id="seller_info" name="seller_info" class="materialize-textarea">{{Input::old('seller_info', str_replace('\n',"\n", !empty($invoice)?$invoice['seller_info']:$settings['seller_info']))}}</textarea>
                                <label for="seller_info">Seller Info</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <textarea id="buyer_info" name="buyer_info" class="materialize-textarea">{{Input::old('buyer_info', str_replace('\n',"\n", !empty($invoice)?$invoice['buyer_info']:''))}}</textarea>
                                <label for="buyer_info">Buyer Info</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12 m6">
                                <textarea id="issuer_info" name="issuer_info" class="materialize-textarea">{{Input::old('issuer_info', !empty($invoice)?$invoice['issuer_info']:$settings['issuer_info'])}}</textarea>
                                <label for="issuer_info">Issuer Info</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <textarea id="receiver_info" name="receiver_info" class="materialize-textarea">{{Input::old('receiver_info', !empty($invoice)?$invoice['receiver_info']:'')}}</textarea>
                                <label for="receiver_info">Receiver Info</label>
                            </div>
                        </div>
                    </div>


                    <div class="section">
                        <h4>Products</h4>

                        @for ($i=0; $i<6; $i++)
                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input id="seller_name" name="description[{{$i}}]" type="text" class="validate"
                                       value="{{Input::old('description.' . $i, !empty($invoice['products'][$i]['description']) ? $invoice['products'][$i]['description'] : '')}}">
                                <label for="seller_name">Description</label>
                            </div>
                            <div class="input-field col s4 m1">
                                <input id="buyer_name" name="quantity[{{$i}}]" type="text" class="validate"
                                       value="{{Input::old('quantity.' . $i, !empty($invoice['products'][$i]['quantity']) ? $invoice['products'][$i]['quantity'] : '')}}">
                                <label for="buyer_name">Quantity</label>
                            </div>
                            <div class="input-field col s4 m3">
                                <input id="buyer_name" name="price[{{$i}}]" type="text" class="validate"
                                       value="{{Input::old('price.' . $i, !empty($invoice['products'][$i]['price']) ? $invoice['products'][$i]['price'] : '')}}">
                                <label for="buyer_name">Price</label>
                            </div>
                            <div class="input-field col s4 m2">
                                <input id="buyer_name" name="currency[{{$i}}]" type="text" class="validate"
                                       value="{{Input::old('currency.' . $i, !empty($invoice['products'][$i]['currency']) ? $invoice['products'][$i]['currency'] : '')}}">
                                <label for="buyer_name">Currency</label>
                            </div>
                        </div>
                        @endfor
                    </div>

                    <div class="section">
                        <h4>Additions</h4>

                        <div class="row">
                            <div class="input-field col s12">
                                <textarea id="extra" name="extra" class="materialize-textarea">{{Input::old('extra', !empty($invoice)?$invoice['extra']:'')}}</textarea>
                                <label for="extra">Extra</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input id="vat_percent" name="vat_percent" type="text" class="validate" value="{{Input::old('vat_percent', !empty($invoice)?$invoice['vat_percent']:$settings['vat_percent'])}}">
                                <label for="vat_percent">VAT %</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input id="branding" name="branding" type="text" class="validate" value="{{Input::old('branding', !empty($invoice)?$invoice['branding']:$settings['branding_label'])}}">
                                <label for="branding">Branding Label</label>
                            </div>
                        </div>
                    </div>

                    <div class="section">
                        <button class="waves-effect waves-light btn-large">Save</button>
                    </div>
                </form>
            </div>
        </div> <!-- end "row" -->
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $( document ).ready(function() {
            $('.datepicker').pickadate({
                selectMonths: true, // Creates a dropdown to control month
                selectYears: 5, // Creates a dropdown of 15 years to control year
                formatSubmit: 'yyyy-mm-dd',
                format: 'yyyy-mm-dd'
            });
        });
    </script>
@endsection
