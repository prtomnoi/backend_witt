<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบฝากเงินสัจจะ</title>
    <style>
        body {
            font-family: "THSarabunNew", sans-serif;
            font-size: 18px;
            margin: 40px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .border {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .p-2 {
            padding: 8px;
        }

        table {
            width: 100%;
        }

        td {
            vertical-align: top;
        }

        .checkbox {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 1px solid #000;
            margin-right: 8px;
            vertical-align: middle;
        }

        .section-title {
            font-weight: bold;
            margin-top: 1rem;
        }

        .signature-line {
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .signature-line > div {
            width: 45%;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="text-right" style="font-weight: bold;">อพค-04</div>
    

    <table  width="100%">
        <tr>
            <td width="70%">
                <table with="100%">
                    <tr>
                        <td width="23%">
                            <img src="{{ public_path('src/assets/img/savinglogo.png') }}" width="80" alt="logo">
                        </td>
                        <td width="77%">
                            <p style="margin: 0; font-size:20px;">กลุ่มออมทรัพย์เพื่อการผลิตบ้านคลองสามแพรก หมู่ที่ 4</p>
                            <p style="margin: 0; font-size:20px;">ต.ในคลองบางปลากด อ.พระสมุทรเจดีย์ จ.สมุทรปราการ</p>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="30%" style="text-align: right;">
                <h2 style="margin: 0;">ใบฝากเงินสัจจะ</h2>
                <small>(แทนใบเสร็จรับเงิน)</small>
            </td>
        </tr>
        
    </table>

    <hr style="margin-top: -3px; ">
    <div style="text-align: right">
        <p style="margin-top: -5px;">วันที่ทำรายการ .................................................</p>
    </div>
    <table class="border" cellpadding="6">
        <tr>
            <td class="border p-2" width="40%">
                บัญชีเลขที่ .......................................... <br>
               
            </td>
            <td class="border p-2" width="60%">
                สมาชิกเลขที่ .......................................... <br>
                ชื่อบัญชี .................................................
            </td>
        </tr>
        <tr>
            <td class="border p-2">
                รูปแบบการฝากเงิน<br>
                <div><span class="checkbox"></span> เงินสด</div>
                <div><span class="checkbox"></span> เงินโอน</div>
                <div><span class="checkbox"></span> อื่นๆ .........................................</div>
            </td>
            <td class="border p-2">
                ต้องการฝากเงินสัจจะสะสมของเดือน ....................... ปี ....................... <br>
                จำนวนเงินฝาก (ตัวเลข) .........................................................<br>
                (ตัวอักษร) ..............................................................................
            </td>
        </tr>
        <tr>
            <td style="text-align: center;" class="border p-2">
                <div>
                    ....................................................<br>
                    ลายมือชื่อผู้ฝากเงิน
                </div>
            </td>
            <td class="border p-2" style="text-align: center;"> 
                <div>
                    ....................................................<br>
                    (...................................................)<br>
                    ลายมือชื่อผู้รับเงิน
                </div>
            </td>
        </tr>
    </table>

    <div class="signature-line">
      
       
    </div>
</body>
</html>
