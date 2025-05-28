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
    /* text-align: right; */
}

.invoice-box table td {
    padding: 5px;
    vertical-align: top;
	/* text-align: center; */
}

.invoice-box table tr td:nth-child(2) {
    /* text-align: right; */
}

.invoice-box table tr.top table td {
    padding-bottom: 20px;
}

.invoice-box table tr.top table td.title {
    font-size: 45px;
    line-height: 45px;
    color: #333;
}

.invoice-box table tr.information table td {
    padding-bottom: 40px;
}

.invoice-box table tr.heading td {
    background: #eee;
    border-bottom: 1px solid #ddd;
    font-weight: bold;
}

.invoice-box table tr.details td {

    border-bottom: 1px solid black;
    border-top: 1px solid black;


}

.invoice-box table tr.item td {
    border-bottom: 1px solid #004cff;


}

.invoice-box table tr.item.last td {
    border-bottom: none;
}

.invoice-box table tr.total td:nth-child(2) {
    border-top: 2px solid #eee;
    font-weight: bold;
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
	text-align: right;
}



  
</style>
<body>
	<div style="position: absolute; top: 20px; left: 20px; border: 1px solid black; padding: 10px; width: 180px; font-size: 16px;">
        <p style="margin: 0;">สมาชิกเลขที่ ...........<strong>{{ $user->user_number }}</strong>..............</p>
        <p style="margin: 0;">บัญชีเลขที่ ......................................</p>
    </div>
	
	<div style="position: absolute; top: 10px; right: 20px; font-size: 16px;">
		อทผ-01 - 1 
	</div>
	<div class="paper-shadow">
		<div class="Title-Top">
			<img src="{{ public_path('src/assets/img/savinglogo.png') }}" width="100px" alt="logo">
			<h2 style="margin-top: -10px">ใบสมัคร</h2>
			<h3 style="margin-top: -10px">ใบสมัครเข้าเป็นสมาชิกกลุ่มออมทรัพย์เพื่อการผลิตบ้านคลองสามแพรก หมู่ที่ 4 ตำบลในคลองปลากด  </h3>
			<h3 style="margin-top : -30px">อำเภอ พระสมุทรเจดีย์ จังหวัด สมุทรปาการ</h3>
			<p style="margin-top : -25px">***********************************************</p>
		</div>
		<div class="invoice-box" >
			<div class="day-mount-year" >
				<p><b>เขียนที่ </b>...................<strong>ชุมชนคลองสามแพรก</strong>.........................</p>
				
			</div>
			<div class="day-mount-year">
				@php
					use Carbon\Carbon;
		
					$createdAt = $user->user_created_date ? Carbon::parse($user->user_created_date) : null;
					$age = Carbon::parse($user->user_birthday)->diffInYears(Carbon::now());

				@endphp
		
				@if ($createdAt)
					<p><b>วันที่</b> ....<strong>{{ $createdAt->format('d') }}</strong>....
					 <b>เดือน</b> .....<strong>{{ $createdAt->translatedFormat('F') }}</strong>.....
				   
					<b>พ.ศ.</b> .....<strong>{{ $createdAt->addYears(543)->format('Y') }}</strong>......</p>
				@else
					<b>วันที่</b> ...........
					<b>เดือน</b> ...........
					<b>พ.ศ.</b> ...........
				@endif
			</div>
		
       
			<div style="line-height: 18px;">
				<p style="text-indent: 20px;">1. ข้าพเจ้า (นาย/นาง/นางสาว)...................<strong>{{ $user->user_fname }} {{ $user->user_lname }}</strong>................... หมายเลขบัตรประชาชน .............<strong>{{ $user->user_id_no }}</strong>.............. อายุ ......<strong>{{ number_format($age) }}</strong>..... ปี</p>
				<p>สถานะ	
					<label style="display: inline-block; vertical-align: middle;">
					  <input type="checkbox" style="vertical-align: middle;" {{ $user->user_spouse_status === 'S' ? 'checked' : '' }}> โสด
					</label>
					<label style="display: inline-block; vertical-align: middle;">
					  <input type="checkbox" style="vertical-align: middle;" {{ $user->user_spouse_status === 'W' ? 'checked' : '' }}> หม้าย
					</label>
					<label style="display: inline-block; vertical-align: middle;">
					  <input type="checkbox" style="vertical-align: middle;" {{ $user->user_spouse_status === 'M' ? 'checked' : '' }}> สมรส ชื่อคู่สมรส
					</label>
					........................................... <strong>{{ $user->user_spouse_name ?? '........................................' }}</strong> ......................................
					 <label style="display: inline-block; vertical-align: middle;">
					  สมาชิกเลขที่
					</label>.........<strong>{{ $user->user_spouse_number ?? '..................................' }}</strong>.........</p>
				<p>ตั้งบ้านเรือนอยู่บ่านเลขที่ ..............<strong>{{ $user->user_address }}</strong>........................ ตำบล ............<strong>{{ $user->district->district_name ?? 'ไม่ระบุ' }}</strong>............... อำเภอ ............<strong>{{ $user->amphur->amphur_name ?? 'ไม่ระบุ' }}</strong>.......... จังหวัด ...........<strong>{{ $user->province->province_name ?? 'ไม่ระบุ' }}</strong>..............</p>
			    <p style="text-indent: 20px;">2. อาชีพและรายได้</p>
				@foreach ($user->occupations as $key => $occupation)
					<p style="text-indent: 30px;">2.{{ $key + 1 }} อาชีพ{{ $key === 0 ? 'หลัก' : 'รอง' }} ........................<strong>{{ $occupation->occupation_name ?? 'ไม่มีข้อมูล' }}</strong>.............................. รายได้เฉลี่ยเดือนละ ...........................<strong>{{ number_format($occupation->occupation_income ?? 0, 2) }}</strong>......................... บาท</p>
				@endforeach
				<p style="text-indent: 20px;">3. การเป็นสมาชิกกลุ่ม หรือ สหกรณ์ ( ระบุกลุ่มอาชีพและสหกรณ์ที่เป็นสมาชิก )</p>
				@php
					$count = count($user->otherGroupMembers);
				@endphp

				@for ($i = 0; $i < 3; $i++)
					@if ($i < $count)
						@php $group = $user->otherGroupMembers[$i]; @endphp
						<p style="text-indent: 30px;">
							3.{{ $i + 1 }} ............................................................<strong>{{ $group->ogm_name ?? 'ไม่มีข้อมูล' }}</strong>.............................................................................
						</p>
					@else
						<p style="text-indent: 30px;">
							3.{{ $i + 1 }} ...........................................................................................................................................................................................
						</p>
					@endif
				@endfor

			
				<p style="text-indent: 20px;">4.  ข้าพเจ้าขอสมัครเข้าเป็นสมาชิกกลุ่มออมทรัพย์เพื่อการผลิตบ้านคลองสามแพรก หมู่ที่ 4 ตำบลในคลองบางปลากด อำเภอพระสมุทรเจดีย์ จังหวัดสมุทรปราการ และจะส่งเงินสัจจะสะสมจำนวนทั้งหมด .................. หุ้น หุ้นละ 50 บาทรวมเป็นเงินสัจจะสะสมที่ต้องส่งเดือนละ 
					 ....................บาท (.......................................................................) ตั้งแต่วันที่ได้รับอนุมัติให้เข้าเป็นสมาชิกของกลุ่มเป็นต้นไป</p>
				<p style="text-indent: 20px;">
					<b>หมายเหตุ</b>: สมาชิกสามารถสมัครเพื่อส่งเงินสัจจะสะสมจำนวนหุ้นละ 50 บาท ได้ไม่ต่ำกว่า 1 หุ้น และรวมจำนวนหุ้นที่มีทั้งหมดต้องไม่เกิน 20 หุ้น 
				</p>
				<p style="text-indent: 20px;">5. ข้าพเจ้า เข้าใจหลักการ วัตถุประสงค์ ของกลุ่มออมทรัพย์เพื่อการผลิตเป็นอย่างดี จึงขอให้สัญญาว่าจะปฏิบัติตามระเบียบข้อบังคับของกลุ่มโดยเคร่งครัด และขอรับรองว่าข้อความที่กล่าวมาข้างต้นเป็นจริงทุกประการ พร้อมใบสมัครนี้ ได้ส่งเงินค่าสมัครและค่าธรรมเนียมแรกเข้ามาด้วยแล้วจำนวนเงิน .......20.......บาท ตัวอักษร (.....ยี่สิบบาทถ้วน.....)
				</p>
		
				<div style="text-align:center; ">
					<b class="original">(ลงชื่อ)</b>
					.........................................................<b>ผู้สมัคร</b><br />
				
					<b class="original">
						(</b>....................<strong>{{ $user->user_fname }} {{ $user->user_lname }}</strong>.........................<b
						class="original">)</b>
				
				</div>
				<br>
				<table border="1" width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse; font-size: 16px;">
					<tr>
						<!-- ฝั่งซ้าย -->
						<td width="35%" valign="top">
							<b><small>(เฉพาะเจ้าหน้าที่)</small></b><br>
							<p style="text-indent: 20px;">ได้รับเงินค่าสมัคร และค่าธรรมเนียมแรกเข้า จำนวน .....20...... บาท (สิบบาทถ้วน) แล้ว</p><br><br>
						
							<div style="text-align:center; margin-top: -30px; ">
								<b class="original">ลงชื่อ</b>
								.........................................................<b>ผู้รับเงิน</b><br />
							
								<b class="original">
									(</b>...........................................................<b
									class="original">)</b> <br>
									วันที่ ......../......./..........
							
							</div>
						</td>
				
						<!-- ฝั่งขวา -->
						<td width="65%" valign="top">
							<b><small>(ความเห็นคณะกรรมการกลุ่มออมทรัพย์เพื่อการผลิตบ้านคลองสามแพรก หมู่ที่ 4)</small></b><br>
							ได้รับใบสมัครของ (นาย/นาง/นางสาว) ................<strong>{{ $user->user_fname }} {{ $user->user_lname }}</strong>.......................... ที่ประชุม<br>
							คณะกรรมการ ครั้งที่ .......... ปี ........... มีมติร่วมกันแล้วเห็นว่า:<br><br>
							<div style="margin-top : -30px">
								<input   type="checkbox" name="" id=""> มีคุณสมบัติครบถ้วน ควรรับเป็นสมาชิกเลขที่ ......<strong>{{ $user->user_number }}</strong>......  บัญชีเลขที่ ................................................ 
								<br> <p style="text-indent: 20px;">ตั้งแต่วันที่ ..................... เดือน .................. พ.ศ ..............................</p> 
							   <br>
							   <p style="margin-top : -34px;"><input type="checkbox" name="" id=""> ไม่ควรรับ เนื่องจาก ......................................................................................................</p>
							   <br>	
							</div>							
					
							<div style="text-align:center; margin-top: -30px; ">
								<b class="original">ลงชื่อ</b>
								......................................................................................................<b>ประธานกลุ่มออมทรัพย์</b><br />
							
								<b class="original">
									(</b>...........................................................<b
									class="original">)</b> 
							
							</div>
						</td>
					</tr>
				</table>
			
			</div>
	

		</div>
	

	</div>
	

	<br>
	<div style="page-break-before: always;"></div>
	<div style="position: absolute; top: 10px; right: 20px; font-size: 16px;">
        อทผ-01 - 2
    </div>
	<div style="font-family: 'THSarabunNew', sans-serif; font-size: 18px; line-height: 1.8;">
		<h2 style="text-align: center; font-weight: bold; ">
			การรับโอนผลประโยชน์ (รายการแนบใบสมัคร)
		</h2>
		<h3 style="text-align: center; font-weight: bold; margin-top:-30px;">
			ของกลุ่มออมทรัพย์เพื่อการผลิตบ้านคลองสามแพรก หมู่ที่ 4 ตำบลในคลองบางปลากด
		</h3>
		<h3  style="text-align: center; font-weight: bold; margin-top:-50px;">
			อำเภอพระสมุทรเจดีย์ จังหวัดสมุทรปราการ
		</h3>
	
		<p style="text-indent: 20px;">ข้าพเจ้า (นาย/นาง/นางสาว) .........................<strong>{{ $user->user_fname }} {{ $user->user_lname }}</strong>...............................
		สมาชิกกลุ่มออมทรัพย์เพื่อการผลิตบ้านคลองสามแพรก<br>
		หมู่ที่ 4 ตำบลในคลองบางปลากด อำเภอพระสมุทรเจดีย์ จังหวัดสมุทรปราการ 
		สมาชิกเลขที่ .......<strong>{{ $user->user_number }}</strong>......... บัญชีเลขที่ .........................<br>
		ขอแต่งตั้งผู้รับผลประโยชน์ในกรณีที่ข้าพเจ้าเสียชีวิต โดยขอยอมรับว่าเงินดังกล่าวจะส่งต่อไปยังบัญชีของกลุ่มออมทรัพย์ฯ 
		ทั้งหมดให้กับผู้รับโอน<br>
		ผลประโยชน์ตามสัดส่วนที่กำหนด จำนวน ......<strong>{{ count($beneficiaries) }}</strong>....... ราย ได้แก่</p>
	
		<ol style="margin-left: 2rem;">
			@php
    		$count = count($beneficiaries);
			@endphp

			@for ($i = 0; $i < 3; $i++)
				@if ($i < $count)
					@php $beneficiary = $beneficiaries[$i]; @endphp
					<li>
						..................<strong>{{ $beneficiary->beneficiaries_name }}</strong>........................ 
						หมายเลขบัตรประชาชน ....................................... อายุ ....<strong>{{ $beneficiary->beneficiaries_age }}</strong>.... ปี<br>
						มีความเกี่ยวข้องเป็น .................<strong>{{ $beneficiary->beneficiaries_relation }}</strong>................ 
						ขอสมัคร โดยรับผลประโยชน์ในสัดส่วน (ร้อยละ) .............<strong>{{ $beneficiary->beneficiaries_ratio }}%</strong>................
					</li>
				@else
					<li>
						......................................................................... 
						หมายเลขบัตรประชาชน ....................................... อายุ ............ ปี<br>
						มีความเกี่ยวข้องเป็น ....................................................... 
						ขอสมัคร โดยรับผลประโยชน์ในสัดส่วน (ร้อยละ) .......................................
					</li>
				@endif
			@endfor

		</ol>
	
		<br><br>
		<table style="width: 100%; text-align: right;">
		    <tr>
				<td width="50%"></td>
				<td width="50%">
					<div style="text-align: center; margin-top: -60px;">
						<b class="original">(ลงชื่อ)</b>
						.........................................................<b>ผู้มอบ (ผู้สมัคร) </b>
						<br />
						<b class="original">
							(</b>.....................<strong>{{ $user->user_fname }} {{ $user->user_lname }}</strong>.......................<b
							class="original">)</b><br />
						<br />
					</div>
					<div style="text-align: center; margin-top: -50px;">
						<b class="original">(ลงชื่อ)</b>
						.........................................................<b>พยาน</b>
						<br />
						<b class="original">
							(</b>.......................<strong>{{ $user->witness1 }}</strong>.........................<b
							class="original">)</b><br />
						<br />
					</div>
					<div style="text-align: center; margin-top: -50px;">
						<b class="original">(ลงชื่อ)</b>
						.........................................................<b>พยาน</b>
						<br />
						<b class="original">
							(</b>.......................<strong>{{ $user->witness2 }}</strong>.........................<b
							class="original">)</b><br />
						<br />
					</div>
				</td>
			</tr>
		</table>
	</div>
	




</body>

</html>