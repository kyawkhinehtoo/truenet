<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8" />

    <title>{{ config('app.name', 'Laravel') }}</title>
    <style>
    html,
    body {
        margin: 0 auto;

    }


    body {
        font-family: sans-serif, Tharlon;
        font-size: 1.1rem;
    }

    .header-img {
        width: 100%;
        height: auto;
    }

    .footer {
        float: left;
        margin-top: 50px;
        width: 100%;
        color: #ffffff;
        text-align: center;
    }

    .container {
        width: 100%;
    }

    .center {
        margin: 0 auto;
        width: 85%;
    }

    .center table {
        margin: 0 auto;
        width: 100%;
    }

    table.head {
        border-spacing: 0;
    }

    table.header {
        width: 100%;
    }

    table.header td {
        width: 50%;
        padding: 5px;
    }

    .text-bold {
        font-weight: 800;
    }

    .text-semibold {
        font-weight: 600;
    }

    .border {
        border: 2px solid #000000;
    }

    .border-top {
        border-top: 2px solid #000000;
    }

    .border-bottom {
        border-bottom: 2px solid #000000;
    }

    .font-medium {
        font-size: 1.4rem;
    }

    .font-small {
        font-size: 1rem;
    }

    .collapse {
        border-collapse: collapse;
        padding: 0;
        border-spacing: 0;
        text-align: center;
        margin-top: 20px;
        float: left;
        display: table;
    }

    .collapse>tbody>tr {
        display: table-row;
    }

    .collapse>tbody tr:last-child {
        height: 250px;
    }

    tbody td {
        text-align: center;
        vertical-align: top;
    }

    .collapse th,
    .collapse td {
        border: 1px solid #000000;
        height: 1em;
        width: auto;
        padding: 10px;
        color: #484848;
        font-weight: 600;
    }

    tr td.fix {
        width: 1%;
        padding: 0 20px;
        white-space: nowrap;
    }

    .left {
        text-align: left;
    }

    .space {

        border-spacing: 10px;

    }

    table.head td.text {
        overflow: hidden;
        position: relative;
    }

    table.head td.text {
        width: 90%;
        text-align: left;
        padding: 10px 0;
    }

    table.head td.text:after {
        content: " ....................................................................................................................... ";
        position: absolute;
        padding-left: 5px;
    }

    .orange_bg {
        color: #000000;
        background-color: #1e3a8a;
    }


    .header h2.title {
        margin: 50px;
        color: #1e3a8a;
        text-align: center;
        font-size: xx-large;
        text-transform: uppercase;
        font-family: 'Times New Roman', Times, serif;
    }

    .sign_area {
        overflow: hidden;
    }

    .signature {
        float: left;
        margin-top: 40px;
        width: 100%;
        text-align: left;
        overflow: hidden;
        position: relative;
    }

    .signature .txt:after {
        content: " ....................................................................................................................... ";
        position: absolute;
        padding-left: 5px;
    }

    .label {
        float: left;
        margin-top: 10px;
        width: 100%;
        text-align: left;
    }

    .header_warapper div {
        float: left;
        width: calc(50% - 10px);
        padding: 10px 5px;
        font-size: medium;
        font-weight: bold;
    }

    .header_warapper {
        float: left;
        position: relative;
        width: calc(100% - 2px);
        border: 1px solid #000000;
        border-bottom: 0;
    }

    td.title {
        text-align: right;
    }

    .thankyou {
        padding: 25px 0;
        color: #1e3a8a;
        text-align: center;
        margin: 0;
        font-weight: 700;
        font-size: 1.2em !important;
        text-transform: capitalize;
        font-family: sans-serif;
    }

    @page {
        header: page-header;
        footer: page-footer;
    }
    </style>

</head>

<body class="font-sans antialiased" style="border-top:0 !important">

    @php
    if (strpos($period_covered, ' to ')) {
    $p_months = explode(" to ", ($period_covered));
    $from = (new DateTime($p_months[0]));
    $to = (new DateTime($p_months[1]));
    $first_date = $from->format("d-M-Y");
    $last_date = $to->format("d-M-Y");
    $period_covered = $first_date.' to '.$last_date;
    }
    $invoice_no = "INV".substr($bill_number,0, 4).str_pad($invoice_number,5,"0", STR_PAD_LEFT);
    @endphp
    <div class="container">
        <htmlpageheader name="page-header">

            <img src="{{ public_path('storage/images/invoice-header.png') }}" class="header-img" />
        </htmlpageheader>
        <div class="header">
            <h2 class="title">Invoice</h2>
        </div>
        <div class="center">

            <div class="header_warapper">
                <table class="header">
                    <tr>
                        <td colspan="2">Customer Name : {{$bill_to}}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Customer ID : {{$ftth_id}}</td>
                    </tr>
                    <tr>
                        <td>Invoice No. : {{$invoice_no}}</td>
                        <td>Address : {{$attn}}</td>
                    </tr>
                    <tr>
                        <td>Date : {{ date("j F Y",strtotime($date_issued)) }}</td>
                        <td>Package : {{$service_description}}</td>
                    </tr>
                    <tr>
                        <td>Contact No. : {{$phone}}</td>
                        <td>Internet Speed : {{$qty}}</td>
                    </tr>
                </table>

            </div>





            <!-- </tbody>
       </table>
                        <table class="collapse" style="margin-top:0px; width:100%; ">
                        <thead>

                        </thead>
                        <tbody> -->
            <table class="collapse" style="width:100%; ">
                <tbody>
                    <tr>
                        <td style="width:10%">No.</td>
                        <td style="width:50%">Description</td>
                        <td style="width:10%">Qty</td>
                        <td style="width:15%">Price (THB)</td>
                        <td style="width:15%">Total Amount (THB)</td>
                    </tr>

                    <tr>
                        <td>1</td>
                        <td>{{$service_description}} for <br /> {{$period_covered}} </td>
                        <td>1</td>
                        <td>{{number_format($normal_cost,2,'.')}}</td>
                        <td>{{number_format($sub_total,2,'.')}}</td>
                    </tr>
                    @php
                    if($discount){
                    @endphp
                    <tr>
                        <td>2</td>
                        <td>Discount </td>
                        <td>1</td>

                        <td>{{number_format($discount,2,'.')}}</td>
                        <td>{{number_format($discount,2,'.')}}</td>
                    </tr>
                    @php
                    }
                    @endphp


                </tbody>

                <tfoot>
                    <tr>

                        <td class="title" colspan="4">Subtotal</td>
                        <td class="text">{{number_format($sub_total,2,'.')}}</td>
                    </tr>
                    <tr>

                        <td class="title" colspan="4">Discount</td>
                        <td class="text">{{number_format($discount,2,'.')}}</td>
                    </tr>
                    <tr>

                        <td class="title" colspan="4">Commercial Tax</td>
                        <td class="text">{{number_format($tax,2,'.')}}</td>
                    </tr>
                    <tr>

                        <td class="title" colspan="4">Grand Total </td>
                        <td class="text">{{number_format($total_payable,2,'.')}}</td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <p class="thankyou">Thank you Choosing Our Services !</p>
                        </td>
                    </tr>

                </tfoot>


            </table>





        </div>
        <htmlpagefooter name="page-footer">
            <img src="{{ public_path('storage/images/invoice-footer.png') }}" class="header-img" />
        </htmlpagefooter>

</body>

</html>