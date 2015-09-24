@extends('layouts.master')

@section('title', 'Invoices List')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col s12">

                <h1>Invoices</h1>

                <a href="?from={{date('Y-m').'-01'}}&to={{date('Y-m-d', strtotime(date('Y-m').'-01' . ' +1 month')-1)}}" class="waves-effect waves-teal @if (Request::input('from')==date('Y-m-'.'01')) btn @else btn-flat @endif">{{date('M')}}</a>
                <a href="?from={{date('Y-m-d', strtotime(date('Y-m').'-01' . ' -1 month'))}}&to={{date('Y-m-d', strtotime(date('Y-m-d', strtotime(date('Y-m').'-01' . ' -1 month')).' +1 month')-1)}}" class="waves-effect waves-teal @if (Request::input('from')==date('Y-m-'.'01', strtotime(date('Y-m').'-01' . ' -1 month'))) btn @else btn-flat @endif">{{date('M', strtotime(date('Y-m').'-01' . ' -1 month'))}}</a>
                <a href="?from={{date('Y-m-d', strtotime(date('Y-m').'-01' . ' -2 month'))}}&to={{date('Y-m-d', strtotime(date('Y-m-d', strtotime(date('Y-m').'-01' . ' -2 month')).' +1 month')-1)}}" class="waves-effect waves-teal @if (Request::input('from')==date('Y-m-'.'01', strtotime(date('Y-m').'-01' . ' -2 month'))) btn @else btn-flat @endif">{{date('M', strtotime(date('Y-m').'-01' . ' -2 month'))}}</a>

                <!-- Dropdown Trigger -->
                <a class='dropdown-button @if (!empty(Request::input('from')) && strtotime(Request::input('from')) < strtotime('-3 month')) btn @else btn-flat @endif' href='#' data-activates='dropdown1'>earlier</a>

                <!-- Dropdown Structure -->
                <ul id='dropdown1' class='dropdown-content'>
                    @for ($i = 3; $i <= 12; $i++)
                        <a href="?from={{date('Y-m-d', strtotime(date('Y-m').'-01' . ' -'. $i .' month'))}}&to={{date('Y-m-d', strtotime(date('Y-m-d', strtotime(date('Y-m').'-01' . ' -'. $i .' month')).' +1 month')-1)}}" class="waves-effect waves-teal btn-flat">{{date('M', strtotime(date('Y-m').'-01' . ' -'. $i .' month'))}}</a>
                    @endfor
                </ul>




                <table>
                    <thead>
                    <tr>
                        <th data-field="id">No.</th>
                        <th data-field="name">Date</th>
                        <th data-field="price" class="hide-on-small-only">Buyer</th>
                        <th data-field="price" class="hide-on-med-and-down">Currency</th>
                        <th data-field="price" class="right-align">Value ({{$settings['domestic_currency']}})</th>
                        <th data-field="price" class="hide-on-med-and-down right-align">Value ({{$settings['foreign_currency']}})</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td><a href="{{route('invoice-view', $invoice['invoice'])}}">{{$invoice['invoice']}}</a></td>
                            <td>{{date('j-M-Y', strtotime($invoice['issued_on']))}}</td>
                            <td class="hide-on-small-only">{{$invoice['buyer_name']}}</td>
                            <td class="hide-on-med-and-down">{{$invoice['base_currency']}}</td>
                            <td class="right-align">{{number_format($invoice['subtotal_domestic'], $settings['decimals'])}}</td>
                            <td class="hide-on-med-and-down right-align">{{!empty($invoice['subtotal_foreign']) ? number_format($invoice['subtotal_foreign'], $settings['decimals']) : '---'}}</td>
                        </tr>
                    @endforeach
                    <tr class="">
                        <td class=""></td>
                        <td class=""></td>
                        <td class="hide-on-small-only"><strong>TOTAL</strong></td>
                        <td class="hide-on-med-and-down"></td>
                        <td class="right-align"><strong>{{number_format($totals['domestic'], $settings['decimals'])}}</strong></td>
                        <td class="hide-on-med-and-down right-align"><strong>{{number_format($totals['foreign'],$settings['decimals'])}}</strong></td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    @include('invoices/components/add-invoice')
@endsection
