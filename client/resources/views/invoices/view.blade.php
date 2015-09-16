@extends('layouts.master')

@section('title', 'Invoice ' . $invoice['invoice'])

@section('content')
    <div class="container">
        <div class="row">
            <div class="col s12">
                <h1>Invoice {{$invoice['invoice']}}</h1>

                <div class="row">
                    <div class="col s12 m6">
                        <div class="card blue-grey lighten-2">
                            <div class="card-content white-text">
                                <span class="card-title">Information</span>
                                <p><strong>ID:</strong> {{$invoice['id']}}</p>
                                <p><strong>No.:</strong> {{$invoice['invoice']}}</p>
                                <p><strong>Issued:</strong> {{date('j M Y', strtotime($invoice['issued_on']))}}</p>
                                <p><strong>VAT %:</strong> {{$invoice['vat_percent']}}</p>
                                <p><strong>Exchange Rate {{$settings['foreign_currency']}}:</strong> {{$invoice['exchange_rate']}} {{$settings['domestic_currency']}}</p>
                                <p><strong>Branding:</strong> {{$invoice['branding']}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="card white">
                            <div class="card-content grey-text text-darken-3">
                                <span class="card-title grey-text text-darken-4">PDF</span>
                                <p>PDF is generated on-the-fly. Select the desired PDF version:</p>
                                <br />
                                <div class="row">
                                    <div class="col s12 m6">
                                        <a class="waves-effect waves-light btn blue"><i class="material-icons left">cloud</i>Domestic</a>
                                    </div>
                                    <div class="col s12 m6">
                                        <a class="waves-effect waves-light btn cyan"><i class="material-icons left">label</i>Foreign</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col s12">
                        <div class="card white">
                            <div class="card-content grey-text text-darken-3">
                                <span class="card-title grey-text text-darken-4">Invoice</span>
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
                                                    <td class="hide-on-small-only right-align">{{$product['price']}} {{$product['currency']}}</td>
                                                    <td class="hide-on-med-and-down right-align">{{$product['price_domestic'] or '---'}} {{$settings['domestic_currency'] or ''}}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td class="hide-on-small-only"></td>
                                                <td class="hide-on-small-only"></td>
                                                <td><strong>SUBTOTAL:</strong></td>
                                                <td class="right-align"><strong>{{$invoice['subtotal']}} {{$product['currency']}}</strong></td>
                                                <td class="hide-on-med-and-down right-align"><strong>{{$invoice['subtotal_domestic'] or '---'}} {{$settings['domestic_currency'] or ''}}</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="hide-on-small-only"></td>
                                                <td class="hide-on-small-only"></td>
                                                <td><strong>VAT {{sprintf('%.2f', $invoice['vat_percent'])}}%:</strong></td>
                                                <td class="right-align"><strong>{{$invoice['vat_value']}} {{$product['currency']}}</strong></td>
                                                <td class="hide-on-med-and-down right-align"><strong>{{$invoice['vat_domestic'] or '---'}} {{$settings['domestic_currency'] or ''}}</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="hide-on-small-only"></td>
                                                <td class="hide-on-small-only"></td>
                                                <td><strong>TOTAL:</strong></td>
                                                <td class="right-align"><strong>{{$invoice['subtotal']+$invoice['vat_value']}} {{$product['currency']}}</strong></td>
                                                <td class="hide-on-med-and-down right-align">
                                                    <strong>
                                                        @if (!empty($invoice['subtotal_domestic']))
                                                            {{$invoice['subtotal_domestic']+$invoice['vat_domestic']}}
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
                            </div>
                        </div>
                    </div>
                </div> <!-- end "row" -->

                <div class="row">
                    <div class="col s12">
                        <a href="{{route('invoice-update', $invoice['invoice'])}}" class="waves-effect waves-light btn"><i class="material-icons left">toc</i>edit</a>
                        <a class="waves-effect waves-light btn"><i class="material-icons left">toll</i>duplicate</a>
                        <a class="waves-effect waves-light btn red"><i class="material-icons left">not_interested</i>delete</a>
                    </div>
                </div>
            </div>
        </div> <!-- end "row" -->
    </div>
@endsection
