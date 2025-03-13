<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8" />
  
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="{{ asset('storage/css/burglish.css')}}">
<style>
        @media print {
            body {
                width: 21cm;
                height: 29.7cm;
                margin: 30mm 45mm 30mm 45mm;
                page-break-inside: avoid;
                /* change the margins as you want them to be. */
            }
            .container {
            width: 100%;
            bottom: 0;
                    position: absolute;
            }

            /* .footer {
                position: absolute;
                bottom: 0;
                width: 100%;
                height: 30px;
            } */

        }

        html,
        body {
            margin: 0 auto;
            height: 297mm;
            width: 210mm;
        }


        body {
            font-family: sans-serif;
            font-size: 0.9rem;
        }
    
        .header-img {
            width: 100%;
            height: auto;
        }

        .footer {
            float: left;
            margin-top: 80px;
            width: 100%;
            position: relative;
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
        table.head{
            border-spacing: 0;
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
       
        .collapse > tbody > tr {
            display: table-row;
        }
        .collapse > tbody tr:last-child{
            height: 150px;
        }
        tbody td{
            text-align: center;
            vertical-align: top;
        }
        .collapse th,
        .collapse  td {
            border:1px solid #000000;
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

        table.head td.text{
            overflow: hidden;
            position: relative;
        }
        table.head td.text { 
         width: 90%;
         text-align:left; padding:10px 0; 
        }
        table.head td.text:after{
            content: " ....................................................................................................................... ";
            position: absolute;
            padding-left: 5px;
        }
        .orange_bg{
            color:#000000;
            background-color: #1e3a8a;
        }
      

        .header h2.title {
            padding: 25px 0 ;
            color: #1e3a8a;
            text-align: center;
            margin:0;
            font-size: xx-large;
            text-transform: uppercase;
            font-family: 'Times New Roman', Times, serif;
        }

        .sign_area{
            overflow: hidden;
        }
        .signature{
            float: left;
            margin-top: 40px;
            width: 100%;
            text-align: left;
            overflow: hidden;
            position: relative;
        }
        .signature .txt:after{
            content: " ....................................................................................................................... ";
            position: absolute;
            padding-left: 5px;
        }
        .label{
            float: left;
            margin-top: 10px;
            width: 100%;
            text-align: left;
        }
        td.title{
        text-align: right;
       }
        .header_warapper div{
            float:left;
            width: calc(50% - 10px);
            padding:10px 5px;
            font-size: small;
            font-weight: bold;
        }
       .header_warapper{
            float: left;
            position: relative;
            width: calc(100% - 2px);
            border:1px solid #000000;
            border-bottom: 0;
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
                        $receipt_no = "REC".substr($bill_number,0, 4).str_pad($receipt_number,5,"0", STR_PAD_LEFT);
                @endphp
    <div class="container">
        <div class="header">
            <img src="{{ asset('storage/images/invoice-header.png') }}" class="header-img" />
            <h2 class="title">Receipt</h2>
        </div>
    
       
        <div class="center" style="margin-top:5px;">
        <div class="header_warapper">
                    <div>Customer Name :  {{$bill_to}}</div>
                    <div>Receipt No. :  {{$receipt_no}}</div>
                    <div>Customer ID :  {{$ftth_id}}</div>
                    <div>Invoice No. :  {{$invoice_no}}</div>
                    <div>Address : {{$attn}}</div>
                    <div>Date :  {{ date("j F Y",strtotime($date_issued)) }}</div>
                   
                    <div>Package : {{$service_description}}</div>
                    <div>Contact No. : {{$phone}}</div>
                    <div>Internet Speed : {{$qty}}</div>
            </div>
       <table class="collapse" style=" width:100%; ">
               <tbody>
               

               <!-- </tbody>
       </table>
                        <table class="collapse" style="margin-top:0px; width:100%; ">
                        <thead>

                        </thead>
                        <tbody> -->
                    <tr>
                        <td>No.</td>
                        <td>Description</td>
                        <td>Qty</td>
                        <td>Price (THB)</td>
                        <td>Total Amount (THB)</td>
                    </tr>

                 <tr >
                            <td>1</td>
                            <td>{{$service_description}} </td>
                            <td >1</td>
                            <td>{{number_format($normal_cost,2,'.')}}</td>
                            <td>{{number_format($sub_total,2,'.')}}</td>
                        </tr>
                        @php
                        if($discount){
                        @endphp 
                        <tr >
                            <td>2</td>
                            <td>Discount </td>
                            <td ></td>
                         
                            <td></td>
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
                          <td class=" left" colspan="5">Period : {{$period_covered}}</td>
                       </tr>
                       <tr>
                          <td class=" left" colspan="5">Remark : {{$remark ?? '' }}</td>
                       </tr>
                        <tr>
                            <td colspan="3" class="sign_area">
                                <span class="label"> Sale Person</span>
                                <span class="signature"><span class="txt">Signature</span> </span>
                            </td>
                            <td colspan="3" class="sign_area">
                                <span class="label"> Paid By</span>
                                <span class="signature"><span class="txt">Signature</span> </span>
                            </td>
                        </tr>
                          
                    </tfoot>
                       
                   
                </table>
            
        
        
            

        </div>
        <div class="footer">
        <img src="{{ asset('storage/images/invoice-footer.png') }}" class="header-img" />
        </div>
   
</body>

</html>