@extends('layouts.master')

@section('title', 'Invoice ' . $invoice['invoice'])

@section('content')
    <div class="container">
        <div class="row">
            <div class="col s12">

                <div class="row">
                    <div class="col s12">
                        <div class="card white">
                            <div class="card-content grey-text text-darken-3">
                                <span class="card-title grey-text text-darken-4">Invoice {{$invoice['invoice']}} / {{date('j M Y', strtotime($invoice['issued_on']))}}</span>
                                <div class="row">
                                    <div class="col s12 m6 hide-on-small-only">
                                        <p><strong>{{$invoice['seller_name']}}</strong></p>
                                        <p>{!! nl2br(str_replace('\n',"\n",$invoice['seller_info'])) !!}</p>
                                        <p>&nbsp;</p>
                                        <p class="grey-text">{!! nl2br(str_replace('\n',"\n",$invoice['issuer_info'])) !!}</p>
                                    </div>
                                    <div class="col s12 m6">
                                        <p><strong>{{$invoice['buyer_name']}}</strong></p>
                                        <p>{!! nl2br(str_replace('\n',"\n",$invoice['buyer_info'])) !!}</p>
                                        <p>&nbsp;</p>
                                        <p class="grey-text">{!! nl2br(str_replace('\n',"\n",$invoice['receiver_info'])) !!}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12">
                                        <table>
                                            <thead>
                                            <tr>
                                                <th data-field="id" class="hide-on-small-only">No.</th>
                                                <th data-field="name">Description</th>
                                                <th data-field="price" class="hide-on-small-only right-align">Quantity</th>
                                                <th data-field="price" class="hide-on-med-and-down right-align">Price</th>
                                                <th data-field="price" class="hide-on-med-and-down right-align">Price (domestic)</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            <?php $i=1; ?>
                                            @foreach ($invoice['products'] as $product)
                                                <tr>
                                                    <td class="hide-on-small-only">{{$i++}}</td>
                                                    <td>{{$product['description']}}</td>
                                                    <td  class="hide-on-small-only right-align">{{$product['quantity']}}</td>
                                                    <td class="hide-on-small-only right-align">{{number_format($product['price'], $settings['decimals'])}} {{$product['currency']}}</td>
                                                    <td class="hide-on-med-and-down right-align">{{$product['price_domestic'] or '---'}} {{$settings['domestic_currency'] or ''}}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td class="hide-on-small-only"></td>
                                                <td class="hide-on-small-only"></td>
                                                <td><strong>SUBTOTAL:</strong></td>
                                                <td class="right-align"><strong>{{number_format($invoice['subtotal'], $settings['decimals'])}} {{$product['currency']}}</strong></td>
                                                <td class="hide-on-med-and-down right-align"><strong>{{$invoice['subtotal_domestic'] or '---'}} {{$settings['domestic_currency'] or ''}}</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="hide-on-small-only"></td>
                                                <td class="hide-on-small-only"></td>
                                                <td><strong>VAT {{number_format($invoice['vat_percent'], $settings['decimals'])}}%:</strong></td>
                                                <td class="right-align"><strong>{{number_format($invoice['vat_value'], $settings['decimals'])}} {{$product['currency']}}</strong></td>
                                                <td class="hide-on-med-and-down right-align"><strong>{{$invoice['vat_domestic'] or '---'}} {{$settings['domestic_currency'] or ''}}</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="hide-on-small-only"></td>
                                                <td class="hide-on-small-only"></td>
                                                <td><strong>TOTAL:</strong></td>
                                                <td class="right-align"><strong>{{number_format($invoice['subtotal']+$invoice['vat_value'], $settings['decimals'])}} {{$product['currency']}}</strong></td>
                                                <td class="hide-on-med-and-down right-align">
                                                    <strong>
                                                        @if (!empty($invoice['subtotal_domestic']))
                                                            @if (!empty($invoice['vat_domestic']))
                                                                {{$invoice['subtotal_domestic']+$invoice['vat_domestic']}}
                                                            @else
                                                                {{$invoice['subtotal_domestic']}}
                                                            @endif
                                                            {{$settings['domestic_currency'] or ''}}
                                                        @endif
                                                    </strong>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12">
                                        <p>{!! nl2br(str_replace('\n',"\n",$invoice['extra'])) !!}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12">
                                        <p class="grey-text text-lighten-1"><strong>Internal ID</strong> #{{$invoice['id']}} | VAT {{$invoice['vat_percent']}}% |
                                            <strong>Exchange Rate</strong> 1 {{$settings['foreign_currency']}} = {{$invoice['exchange_rate']}} {{$settings['domestic_currency']}} |
                                            <strong>Branding:</strong> {{$invoice['branding']}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end "row" -->

                <div class="row">
                    <div class="col s12">
                        <a class="waves-effect waves-light btn blue"><i class="material-icons left">cloud</i>Domestic</a>
                        <a class="waves-effect waves-light btn blue"><i class="material-icons left">label</i>Foreign</a>

                        <a href="{{route('invoice-update', $invoice['invoice'])}}" class="waves-effect waves-light btn"><i class="material-icons left">toc</i>edit</a>
                        <a class="waves-effect waves-light btn"><i class="material-icons left">toll</i>duplicate</a>
                        <a class="waves-effect waves-light btn red" id="delete-invoice"><i class="material-icons left">not_interested</i>delete</a>
                    </div>
                </div>
            </div>
        </div> <!-- end "row" -->
    </div>

    @include('invoices/components/add-invoice')

@endsection


@section('scripts')
    <script type="text/javascript">
        $( document ).ready(function() {
            $('#delete-invoice').click(function(e) {
                e.preventDefault();
                if (confirm('Are you sure?')) {
                    window.location = "{{route('invoice-delete', $invoice['invoice'])}}";
                }
            });
        });
    </script>
@endsection