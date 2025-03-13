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
       

        body {
            font-family: 'Calibri',sans-serif,Tharlon;
            font-size: 0.9rem;
        }
    
        .logo-img {
            width: 117px;
            height: 87px;
            margin-top:50px;
        }

        .container {
            width: 100%;
        }

        .center {
            margin: 0 auto;
            width: 90%;
        }

        .center table {
            margin: 0 auto;
            width: 100%;
        }

        .bottom {
            vertical-align: bottom;
        }

        .text-bold {
            font-weight: 800;
        }

        .text-semibold {
            font-weight: 600;
        }

        .border {
            border: 1px solid #000000;
        }

        .border-top {
            border-top: 1px solid #000000;
        }

        .border-bottom {
            border-bottom: 1px solid #000000;
        }

        .font-medium {
            font-size: 1rem;
        }

        .collapse {
            border-collapse: collapse;
            padding: 0;
            border-spacing: 0;
        }

        .-left {
            margin-left: -10px;
        }

        .space {

            border-spacing: 10px;

        }

        .underline {
            margin: 0 auto;
            width: 100%;
            height: 2px;
            background: #000000;
        }

        .bg-marga {
            background: #255978;
            color: #ffffff;
        }

        .address {

            font-size: 0.7rem;
            text-align: right;
        }
    </style>

</head>

<body class="font-sans antialiased" style="border-top:0 !important">
    <div class="container">
        <div class="center">
            <div style="margin-top:100px;vertical-align:bottom;width:100%;">
                <div style="width:50%;float:left;"><img src="{{ public_path('storage/images/marga-logo-m.png') }}" class="logo-img" /></div>
                <div class="address" style="width:50%;float:right;margin-top:25px;">
                    <p>Suite 1906-07, 19th Floor, Sule Square</p>
                    <p>No. 221, Sule Pagoda Road, Kyaukdata Township, Yangon</p>
                    <p>Email: billing@margaglobaltelecom.com</p>
                    <p>Hotline: +95 1 4700 333</p>
                </div>
            </div>

            <table class="collapse">
                <tbody>
                    <tr>
                        <td colspan="2" class="border-bottom bg-marga" style="padding:5px 5px;width:50%">
                            <h1 class="text-semibold font-medium">CASH RECEIPT</h1>
                        </td>
                        <td style="padding:10px;width:50%;font-size:0.8rem;text-align:right;" class="bottom border-bottom"><span class="text-semibold "> Payment Date:</span> {{ $receipt_date_2 }}</td>
                    </tr>
                </tbody>

            </table>

            <table class="collapse" style="margin-top:20px;">
                <tbody>
                    <tr>

                        <td style="padding:10px;width:70%;vertical-align:top;" rowspan="3" class="border ">
                            <table class="collapse ">
                                @php
                                if($bill_to == '0' or (strpos($bill_to,'uspend') !== false) or (strpos($bill_to,'09')!== false) or (strpos($bill_to,'959')!== false) ) {
                                @endphp

                                @php
                                }else{
                                @endphp
                                <tr>
                                    <td class="text-semibold" style="width:25%;vertical-align:top;">Payer Name </td>
                                    <td style="width:75%">: {{$bill_to}}</td>
                                </tr>
                                @php
                                }
                                @endphp
                                <tr>
                                    <td class="text-semibold" style="width:25%;vertical-align:top;"><br />Payer Address </td>
                                    <td style="width:75%"><br />: {{$attn}}</td>
                                </tr>
                                <tr>
                                    <td class="text-semibold" style="width:25%;vertical-align:top;"><br />Coverage Period </td>
                                    <td style="width:75%"><br />: {{$period_covered}}</td>
                                </tr>
                            </table>

                        </td>
                        <td style="width:30%;text-align:center;">
                            <div class="border" style="width:calc(100% - 40px);margin-left:20px;padding:10px;">
                            <span class="text-semibold"> Receipt ID:</span> <br />
                             @php
                              
                                $receipt_num = 'R'.str_pad($receipt_number,5,"0", STR_PAD_LEFT).'-'.substr($bill_number,0, 4).'-'.substr($ftth_id,0, 5);
                                echo $receipt_num;
                             @endphp
                           
                            </div>

                        </td>
                    </tr>

                    <tr>
                        <td style="width:30%;text-align:center;">
                            <div class="border" style="width:calc(100% - 40px);margin-left:20px;margin-top:20px;padding:10px;">
                            <span class="text-semibold">Bill Number:</span> <br />
                                {{ $bill_number }}
                                
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:30%;text-align:center;">
                            <div class="border" style="width:calc(100% - 40px);margin-left:20px;margin-top:20px;padding:10px;">
                            <span class="text-semibold"> Customer ID:</span> <br />
                                {{ $ftth_id }}
                                
     
                            </div>
                        </td>
                    </tr>

                </tbody>

            </table>

            <table class="collapse" style="margin-top:20px;">
            <tr>
                <td class="border" style="width:25%;padding:10px;text-align:center"><p class="text-semibold" >Amount (MMK)</p>{{number_format($collected_amount,2,'.')}}</td>
              
                <td style="width:75%;text-align:center;">
                <div class="border" style="width:calc(100% - 40px);margin-left:20px;padding:10px;">
                <p class="text-semibold" >Amount in Words</p>
                @php
              
                $inWords = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                $amount_word = ucwords($inWords->format($collected_amount)) . ' Kyats Only';
                echo $amount_word;
                @endphp
                </div>
            </td>
            </tr>
            </table>
            <table class="collapse" style="margin-top:20px;">
            <tr>
                <td style="width:50%;padding:20px;text-align:left"><p class="text-semibold" >Received By</p>
                    <br />
                    <br />
                    <p style="margin-top:10px;">Signature : ----------------------------</p>
                    <p style="margin-top:10px;">Name :</p>            
                    <p style="margin-top:10px;">Designation :</p>            
                </td>
                <td style="width:30%;padding:20px;text-align:left"><p class="text-semibold" >Paid By</p>
                    <br />
                    <br />
                    <p style="margin-top:10px;">Signature : ----------------------------</p>
                    <p style="margin-top:10px;">Name :</p>            
                    <p style="margin-top:10px;">Designation :</p>            
                </td>
               
              
              
            </tr>
            </table>

        </div>
    </div>

</body>

</html>