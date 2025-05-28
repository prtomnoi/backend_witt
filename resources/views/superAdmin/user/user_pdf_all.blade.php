<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<style>
     @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path("fonts/THSarabunNew.ttf") }}') format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: bold;
            font-weight: bold;
            src: url('{{ storage_path("fonts/THSarabunNewBold.ttf") }}') format('truetype');
        }
        body {
            font-family: "THSarabunNew", Arial, sans-serif;
        }

p{
    font-size: 17px;
	margin: 0;
}
h2 {
	margin: 0;
}
.table-sortable tbody tr {
    cursor: move;
}

.invoice-box table {
    width: 100%;
    line-height: inherit;
    text-align: left;
}

.invoice-box table td {
    padding: 3px;
    vertical-align: top;
	text-align: center;
}




@media only screen and (max-width: 600px) {
    .invoice-box table tr.top table td {
        width: 100%;
        display: block;
        text-align: center;
    }

    .invoice-box table tr.information table td {
        width: 100%;
        display: block;
        text-align: center;
    }
}


.invoice-box.rtl {
    direction: rtl;
}

.invoice-box.rtl table {
    text-align: right;
}

.invoice-box.rtl table tr td:nth-child(2) {
    text-align: left;
}

.Title-Top {
    text-align: center;
}

.invoicet{
    margin-top: auto;
    text-align: justify; 
}
.box{
  border-radius: 10px;
}
.div2 {
    float: left;
    text-align: justify;
    box-sizing: content-box;
    border: 1px solid black;
    
  }
.div3{ 
    margin-left: 10px;  
    text-align: justify; 
    box-sizing: border-box;
    border: 1px solid black;
   
}
.xx{
    padding-left: 50px;
}
.xr{
    padding-left: 60px;
}

.day-mount-year {
	text-align: center;
}

</style>
<body>
	<div class="paper-shadow">
		<div class="Title-Top">
            <img src="{{ public_path('src/assets/img/savinglogo.png') }}" width="80px" alt="logo">
			<h3 style="margin-top: -5px; line-height: 18px;">รายชื่อผู้ยื่นเอกสารสมัครเข้าเป็นสมาชิกกลุ่มออมทรัพย์เพื่อการผลิตบ้านคลองสามแพรก หมู่ที่ 4 <br>
                ตำบลในตลองบางปลากด อำเภอพระสมุทรเจดีย์ จังหวัดสมุทรปราการ</h3>
                <h3 style="margin-top: -15px; line-height: 18px;">ประจำเดือน................................ ปี..................................</h3>
                <h3 style="margin-top: -15px; line-height: 18px;">สำหรับเข้าพิจารณาในที่ประชุมคณะกรรมการฯ ในวันที่............................... ครั้งที่........... ปี.................</h3>
		</div>
		<div class="invoice-box">
            <table border="1" cellpadding="0" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th rowspan="2">ลำดับ</th>
                        <th rowspan="2">ชื่อ</th>
                        <th rowspan="2">นามสกุล</th>
                        <th colspan="2">หมายเลขเอกสารที่ออกให้</th>
                    </tr>
                    <tr>
                        <th>ผู้สมัครเลขที่</th>
                        <th>บัญชีเลขที่</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->user_fname }}</td>
                            <td>{{ $user->user_lname }}</td>
                            <td>{{ $user->user_number }}</td>
                            <td>{{ $user->accounts->first()->account_no ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">ไม่มีข้อมูล</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
        </div>
        
	

	</div>
	

	<br>


</body>

</html>