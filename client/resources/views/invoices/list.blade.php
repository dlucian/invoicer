@extends('layouts.master')

@section('title', 'Invoices List')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col s12">
                <h1>Invoices</h1>

                <table>
                    <thead>
                    <tr>
                        <th data-field="id">No.</th>
                        <th data-field="name">Date</th>
                        <th data-field="price" class="hide-on-small-only">Buyer</th>
                        <th data-field="price" class="hide-on-med-and-down">Value</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td><a href="{{route('invoice-view', $invoice['invoice'])}}">{{$invoice['invoice']}}</a></td>
                            <td>{{date('j-M-Y', strtotime($invoice['issued_on']))}}</td>
                            <td class="hide-on-small-only">{{$invoice['buyer_name']}}</td>
                            <td class="hide-on-med-and-down">0 USD</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    @include('invoices/components/add-invoice')
@endsection
