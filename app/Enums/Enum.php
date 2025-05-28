<?php

namespace App\Enums;

enum Enum
{
    //=========================================================================
    // กลุ่มบัญชีหลัก
    //=========================================================================
    const ACCOUNT_SAVINGS = '99996701';       // บัญชีเงินฝาก
    const ACCOUNT_DIVIDEND = '99996702';      // บัญชีปันผล 60
    const ACCOUNT_CONTRIBUTION = '99996703';  // บัญชีเงินสมทบ 10
    const ACCOUNT_WELFARE = '99996704';       // บัญชีสวัสดิการ 30

    const MAIN_ACCOUNT = [self::ACCOUNT_SAVINGS, self::ACCOUNT_DIVIDEND, self::ACCOUNT_CONTRIBUTION, self::ACCOUNT_WELFARE];

    //=========================================================================
    // กลุ่มค่าคงที่ระบบ
    //=========================================================================
    const SYSTEM_CONFIG_TYPE_RATE = 'RATE';
    const SYSTEM_CONFIG_NAME_DEPOSIT_RATE = 'DEPOSIT_RATE';
    const SYSTEM_CONFIG_NAME_LOAN_RATE = 'LOAN_RATE';
    const USER_SYSTEM_NAME = 'SYSTEM';    // ชื่อ user ระบบ
    const UNIT_PRICE = 50;                // ราคาต่อหุ้น
    const UNIT_MAX = 20;                  // จำนวนหุ้นสูงสุด
    const UNIT_DEFAULT_NAME = 'หุ้น 1 หน่วย 50 บาท';  // ชื่อหุ้นเริ่มต้น
    const ALLOWED_FILE_TYPES = ['image/jpeg', 'image/png', 'application/pdf'];

    //=========================================================================
    // กลุ่มสถานะผู้ใช้งาน
    //=========================================================================
    const USER_STATUS_A = 'A';    // ใช้งาน
    const USER_STATUS_I = 'I';    // ไม่ใช้งาน
    const USER_STATUS_P = 'P';    // รอดำเนินการ

    const DEFAULT_STATUS_A = 'A';    // ใช้งาน
    const DEFAULT_STATUS_I = 'I';    // ไม่ใช้งาน

    //=========================================================================
    // กลุ่มสถานะบัญชี
    //=========================================================================
    const ACCOUNT_STATUS_W = 'W';     // รอพิจารณา
    const ACCOUNT_STATUS_A = 'A';     // ใช้งาน
    const ACCOUNT_STATUS_I = 'I';     // ปิดบัญชี
    const ACCOUNT_STATUS_C = 'C';     // ไม่ผ่านการเห็นชอบ
    const ACCOUNT_STATUS_L = 'L';     // กู้เงิน
    const ACCOUNT_STATUS_G = 'G';     // ค้ำประกัน
    const ACCOUNT_STATUS_WI = 'WI';   // รอพิจารณาปิดบัญชี
    const ACCOUNT_STATUS_IC = 'IC';   // ปิดบัญชีจากความผิด
    const ACCOUNT_STATUS_GC = 'GC';   // รับภาระหนี้

    //=========================================================================
    // กลุ่มเดือน
    //=========================================================================
    const MONTH_01 = '01';
    const MONTH_02 = '02';
    const MONTH_03 = '03';
    const MONTH_04 = '04';
    const MONTH_05 = '05';
    const MONTH_06 = '06';
    const MONTH_07 = '07';
    const MONTH_08 = '08';
    const MONTH_09 = '09';
    const MONTH_10 = '10';
    const MONTH_11 = '11';
    const MONTH_12 = '12';

    //=========================================================================
    // กลุ่มประเภทประกาศ
    //=========================================================================
    const ANNOUNCE_TYPE_A = 'A';    // ประกาศ
    const ANNOUNCE_TYPE_B = 'B';    // ระเบียบ

    //=========================================================================
    // กลุ่มการฝากเงิน
    //=========================================================================
    const DEFAULT_DEPOSIT_MONTHS = 12;  // จำนวนเดือนเริ่มต้นสำหรับแผนการฝาก
    const MIN_DEPOSIT_MONTHS = 1;       // จำนวนเดือนขั้นต่ำ
    const MAX_DEPOSIT_MONTHS = 60;      // จำนวนเดือนสูงสุด

    const DEPOSIT_FLAG_P = 'P';    // ยังไม่ได้จ่าย
    const DEPOSIT_FLAG_Y = 'Y';    // จ่ายแล้ว
    const DEPOSIT_FLAG_W = 'W';    // รอตรวจสอบข้อมูล

    const DEPOSIT_PAY_TYPE_CASH = 'CASH';  // เงินสด
    const DEPOSIT_PAY_TYPE_TRAN = 'TRAN';  // โอน
    const DEPOSIT_PAY_TYPE_WAIT = 'WAIT';  // รอจ่าย

    //=========================================================================
    // กลุ่มการอนุมัติ
    //=========================================================================
    const APPROVE_ACTION = 'approve';
    const REJECT_ACTION = 'reject';

    //=========================================================================
    // กลุ่มประเภทธุรกรรมบัญชี
    //=========================================================================
    // Account Transaction Types (สำหรับ account_transaction)
    const AT_TYPE_DP = 'DP';  // ฝากเงิน
    const AT_TYPE_WD = 'WD';  // ถอนเงิน
    const AT_TYPE_LN = 'LN';  // กู้เงิน
    const AT_TYPE_LP = 'LP';  // ชำระเงินกู้
    const AT_TYPE_LD = 'LD';  // จ่ายเงินกู้
    const AT_TYPE_DI = 'DI';  // รับเงินปันผล
    const AT_TYPE_DO = 'DO';  // จ่ายเงินปันผล
    const AT_TYPE_GU = 'GU';  // ค้ำประกัน
    const AT_TYPE_DS = 'DS';  // รับเงินจากการฝากเงิน
    const AT_TYPE_FE = 'FE';  // ค่าธรรมเนียม
    const AT_TYPE_PN = 'PN';  // ค่าปรับ
    const AT_TYPE_WF = 'WF';  // สวัสดิการ

    // Account Main Transaction Types (สำหรับ account_main_transaction)
    const AMT_TYPE_DP = 'DP';  // ฝากเงินเข้ากองทุน
    const AMT_TYPE_WD = 'WD';  // ถอนเงินจากกองทุน
    const AMT_TYPE_DI = 'DI';  // จัดสรรเงินปันผล
    const AMT_TYPE_DO = 'DO';  // จ่ายเงินปันผล
    const AMT_TYPE_AL = 'AL';  // จัดสรรผลกำไร
    const AMT_TYPE_TR = 'TR';  // โอนระหว่างกองทุน
    const AMT_TYPE_LN = 'LN';  // กู้เงิน (กองทุนจ่ายเงินกู้)
    const AMT_TYPE_LP = 'LP';  // รับชำระเงินกู้ (กองทุนรับเงินชำระ)
    const AMT_TYPE_WF = 'WF';  // จ่ายสวัสดิการ
    const AMT_TYPE_FE = 'FE';  // รับค่าธรรมเนียม
    const AMT_TYPE_PN = 'PN';  // รับค่าปรับ

    // Transaction Payment Types
    const TRANSACTION_PAYMENT_TYPE_S = 'S';  // บวก
    const TRANSACTION_PAYMENT_TYPE_D = 'D';  // ลบ

    const TRANSACTION_TABLE_AT = 'AT';
    const TRANSACTION_TABLE_AMT = 'AMT';

    //=========================================================================
    // กลุ่มเอกสารการประชุม
    //=========================================================================
    const UMDM_TYPE_OACC = 'OACC';    // เปิดบัญชี
    const UMDM_TYPE_CACC = 'CACC';    // ปิดบัญชี
    const UMDM_TYPE_LNACC = 'LNACC';  // กู้เงินสามัญ
    const UMDM_TYPE_GUACC = 'GUACC';  // ค้ำประกัน
    const UMDM_TYPE_LNEACC = 'LNEACC'; // กู้ฉุกเฉิน

    //=========================================================================
    // กลุ่มเงินกู้
    //=========================================================================
    const LOAN_TYPE_N = 'N';  // กู้สามัญ
    const LOAN_TYPE_E = 'E';  // กู้ฉุกเฉิน

    const LOAN_STATUS_W = 'W';    // รอพิจารณา
    const LOAN_STATUS_A = 'A';    // ปกติ
    const LOAN_STATUS_I = 'I';    // ปิดสัญญา
    const LOAN_STATUS_C = 'C';    // ยกเลิกสัญญา
    const LOAN_STATUS_E = 'E';    // ขาดส่ง
    const LOAN_STATUS_WC = 'WC';  // รอพิจารณายกเลิกสัญญา

    const IP_PAY_TYPE_A = 'A';    // ปกติ
    const IP_PAY_TYPE_B = 'B';    // ผ่อนผัน

    //=========================================================================
    // กลุ่มสถานะเอกสาร
    //=========================================================================
    const DOCUMENT_STATUS_ACTIVE = 'A';    // ใช้งาน
    const DOCUMENT_STATUS_INACTIVE = 'I';  // ไม่ใช้งาน

    //=========================================================================
    // กลุ่มประเภทห้องเอกสาร
    //=========================================================================
    const DOCUMENT_ROOM_TYPE_A = 'A';  // ขอกู้สามัญ
    const DOCUMENT_ROOM_TYPE_B = 'B';  // ขอกู้ฉุกเฉิน
    const DOCUMENT_ROOM_TYPE_C = 'C';  // ลาออก
    const DOCUMENT_ROOM_TYPE_D = 'D';  // ลาออก(กรณีเสียชีวิต)
    const DOCUMENT_ROOM_TYPE_E = 'E';  // ใบถอนเงิน
    const DOCUMENT_ROOM_TYPE_F = 'F';  // ใบถอนเงิน(ผู้รับผลประโยชน์)
    const DOCUMENT_ROOM_TYPE_G = 'G';  // ไม่ได้ระบุ

    //=========================================================================
    // กลุ่มสถานะผลกำไรประจำปี
    //=========================================================================
    const ANNUAL_PROFIT_STATUS_OPEN = 'OPEN';            // เปิด/ยังไม่ปิดงบ
    const ANNUAL_PROFIT_STATUS_CLOSED = 'CLOSED';        // ปิดงบแล้ว
    const ANNUAL_PROFIT_STATUS_DISTRIBUTED = 'DISTRIBUTED'; // จัดสรรผลกำไรแล้ว

    //=========================================================================
    // กลุ่มการจัดสรรเงินปันผล
    //=========================================================================
    const DIVIDEND_ALLOCATION_STATUS_PENDING = 'PENDING';        // รอดำเนินการ
    const DIVIDEND_ALLOCATION_STATUS_APPROVED = 'APPROVED';      // อนุมัติแล้ว
    const DIVIDEND_ALLOCATION_STATUS_DISTRIBUTED = 'DISTRIBUTED'; // แจกจ่ายเงินปันผลแล้ว

    // ค่าคงที่สำหรับสัดส่วนการจัดสรรเงินปันผล (เปอร์เซ็นต์)
    const DIVIDEND_ALLOCATION_PERCENT = 60;      // สัดส่วนเงินปันผล (60%)
    const CONTRIBUTION_ALLOCATION_PERCENT = 10;  // สัดส่วนเงินสมทบ (10%)
    const WELFARE_ALLOCATION_PERCENT = 30;       // สัดส่วนเงินสวัสดิการ (30%)

    //=========================================================================
    // กลุ่มสถานะการจ่ายเงินปันผลรายสมาชิก
    //=========================================================================
    const MEMBER_DIVIDEND_STATUS_PENDING = 'PENDING';        // รอจ่าย
    const MEMBER_DIVIDEND_STATUS_PAID = 'PAID';              // จ่ายแล้ว
    const MEMBER_DIVIDEND_STATUS_TRANSFERRED = 'TRANSFERRED'; // โอนเข้าบัญชี
    const MEMBER_DIVIDEND_STATUS_CANCELLED = 'CANCELLED';    // ยกเลิก

    // ค่าคงที่สำหรับวิธีการจ่ายเงินปันผล
    const MEMBER_DIVIDEND_PAYMENT_METHOD_CASH = 'CASH';        // เงินสด
    const MEMBER_DIVIDEND_PAYMENT_METHOD_TRANSFER = 'TRANSFER'; // โอนเงิน
    const MEMBER_DIVIDEND_PAYMENT_METHOD_DEPOSIT = 'DEPOSIT';   // ฝากเข้าบัญชี

    //=========================================================================
    // กลุ่มสถานะสมรส
    //=========================================================================
    const USER_SPOUSE_STATUS_SINGLE = 'S';    // โสด
    const USER_SPOUSE_STATUS_WIDOWED = 'W';   // หม้าย
    const USER_SPOUSE_STATUS_MARRIED = 'M';   // สมรส
    const USER_SPOUSE_STATUS_UNKNOWN = '';    // ไม่ระบุ

    //=========================================================================
    // กลุ่มวิธีการชำระเงิน
    //=========================================================================
    const PAYMENT_METHOD_CASH = 'CASH';        // เงินสด
    const PAYMENT_METHOD_TRANSFER = 'TRANSFER'; // โอนเงิน
    const PAYMENT_METHOD_DEDUCT = 'DEDUCT';    // หักบัญชี
    const PAYMENT_METHOD_OTHER = 'OTHER';      // อื่นๆ

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลประเภทธุรกรรมบัญชี
    //=========================================================================
    public static function getAccountTransactionTypeDropdown(): array
    {
        return [
            self::AT_TYPE_DP => 'ฝากเงิน',
            self::AT_TYPE_WD => 'ถอนเงิน',
            self::AT_TYPE_LN => 'กู้เงิน',
            self::AT_TYPE_LP => 'ชำระเงินกู้',
            self::AT_TYPE_LD => 'จ่ายเงินกู้',
            self::AT_TYPE_DI => 'รับเงินปันผล',
            self::AT_TYPE_DO => 'จ่ายเงินปันผล',
            self::AT_TYPE_GU => 'ค้ำประกัน',
            self::AT_TYPE_DS => 'รับเงินจากการฝากเงิน',
            self::AT_TYPE_FE => 'ค่าธรรมเนียม',
            self::AT_TYPE_PN => 'ค่าปรับ',
            self::AT_TYPE_WF => 'สวัสดิการ'
        ];
    }

    public static function getAccountMainTransactionTypeDropdown(): array
    {
        return [
            self::AMT_TYPE_DP => 'ฝากเงินเข้ากองทุน',
            self::AMT_TYPE_WD => 'ถอนเงินจากกองทุน',
            self::AMT_TYPE_DI => 'จัดสรรเงินปันผล',
            self::AMT_TYPE_DO => 'จ่ายเงินปันผล',
            self::AMT_TYPE_AL => 'จัดสรรผลกำไร',
            self::AMT_TYPE_TR => 'โอนระหว่างกองทุน',
            self::AMT_TYPE_LN => 'กู้เงิน',
            self::AMT_TYPE_LP => 'รับชำระเงินกู้',
            self::AMT_TYPE_WF => 'จ่ายสวัสดิการ',
            self::AMT_TYPE_FE => 'รับค่าธรรมเนียม',
            self::AMT_TYPE_PN => 'รับค่าปรับ'
        ];
    }

    public static function getAccountTransactionTypeDescription($type)
    {
        $types = self::getAccountTransactionTypeDropdown();
        return $types[$type] ?? 'ไม่ระบุ';
    }

    public static function getAccountMainTransactionTypeDescription($type)
    {
        $types = self::getAccountMainTransactionTypeDropdown();
        return $types[$type] ?? 'ไม่ระบุ';
    }

    /**
     * กำหนดประเภทการบวก/ลบตามประเภทรายการและบัญชี
     *
     * @param string $transactionType ประเภทรายการ (AT_TYPE_* หรือ AMT_TYPE_*)
     * @param string $accountNo เลขที่บัญชี
     * @return string ประเภทการบวก/ลบ (S=บวก, D=ลบ)
     */
    public static function getTransactionPaymentTypeByTransactionType($transactionType, $accountNo = null): string
    {
        // ตรวจสอบว่าเป็นบัญชีหลักหรือไม่
        $isMainAccount = $accountNo && in_array($accountNo, self::MAIN_ACCOUNT);

        if ($isMainAccount) {
            // สำหรับบัญชีหลัก (กองทุน)

            // รายการที่ทำให้บัญชีกองทุนเพิ่ม (บวก)
            $addTypesForMainAccount = [
                self::AMT_TYPE_DP, // ฝากเงินเข้ากองทุน
                self::AMT_TYPE_DI, // จัดสรรเงินปันผล
                self::AMT_TYPE_LP, // รับชำระเงินกู้
                self::AMT_TYPE_FE, // รับค่าธรรมเนียม
                self::AMT_TYPE_PN, // รับค่าปรับ
                self::AMT_TYPE_AL  // จัดสรรผลกำไร
            ];

            // รายการที่ทำให้บัญชีกองทุนลด (ลบ)
            $subtractTypesForMainAccount = [
                self::AMT_TYPE_WD, // ถอนเงินจากกองทุน
                self::AMT_TYPE_DO, // จ่ายเงินปันผล
                self::AMT_TYPE_LN, // กู้เงิน (กองทุนจ่ายเงินกู้)
                self::AMT_TYPE_WF  // จ่ายสวัสดิการ
            ];

            // กรณีโอนระหว่างกองทุน ต้องดูว่าเป็นบัญชีต้นทางหรือปลายทาง
            if ($transactionType === self::AMT_TYPE_TR) {
                // ต้องมีการระบุในฟิลด์ reference_id หรือ reference_fund_code ว่าโอนไปที่ไหน
                // ในที่นี้สมมติว่าถ้าเป็นบัญชีต้นทาง จะเป็นลบ (เงินออก)
                // ถ้าเป็นบัญชีปลายทาง จะเป็นบวก (เงินเข้า)
                return self::TRANSACTION_PAYMENT_TYPE_D; // สมมติว่าเป็นบัญชีต้นทาง (ลบ)
            }

            if (in_array($transactionType, $addTypesForMainAccount)) {
                return self::TRANSACTION_PAYMENT_TYPE_S; // บวก
            } else if (in_array($transactionType, $subtractTypesForMainAccount)) {
                return self::TRANSACTION_PAYMENT_TYPE_D; // ลบ
            }
        } else {
            // สำหรับบัญชีสมาชิก

            // รายการที่ทำให้บัญชีสมาชิกเพิ่ม (บวก)
            $addTypesForMember = [
                self::AT_TYPE_DP, // ฝากเงิน
                self::AT_TYPE_LN, // กู้เงิน
                self::AT_TYPE_DI, // รับเงินปันผล
                self::AT_TYPE_DS  // รับเงินจากการฝากเงิน
            ];

            // รายการที่ทำให้บัญชีสมาชิกลด (ลบ)
            $subtractTypesForMember = [
                self::AT_TYPE_WD, // ถอนเงิน
                self::AT_TYPE_LP, // ชำระเงินกู้
                self::AT_TYPE_LD, // จ่ายเงินกู้
                self::AT_TYPE_DO, // จ่ายเงินปันผล
                self::AT_TYPE_GU, // ค้ำประกัน
                self::AT_TYPE_FE, // ค่าธรรมเนียม
                self::AT_TYPE_PN, // ค่าปรับ
                self::AT_TYPE_WF  // สวัสดิการ
            ];

            if (in_array($transactionType, $addTypesForMember)) {
                return self::TRANSACTION_PAYMENT_TYPE_S; // บวก
            } else if (in_array($transactionType, $subtractTypesForMember)) {
                return self::TRANSACTION_PAYMENT_TYPE_D; // ลบ
            }
        }

        // กรณีไม่พบประเภทรายการที่ระบุ
        return self::TRANSACTION_PAYMENT_TYPE_S; // ค่าเริ่มต้นเป็นบวก
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลประเภทเงินกู้
    //=========================================================================
    public static function getLoanTypeDropdown(): array
    {
        return [
            self::LOAN_TYPE_N => 'กู้สามัญ',
            self::LOAN_TYPE_E => 'กู้ฉุกเฉิน'
        ];
    }

    public static function getLoanTypeDescription($type)
    {
        $types = self::getLoanTypeDropdown();
        return $types[$type] ?? 'ไม่ระบุ';
    }

    public static function getLoanStatusDropdown(): array
    {
        return [
            self::LOAN_STATUS_W => 'รอพิจารณา',
            self::LOAN_STATUS_A => 'ปกติ',
            self::LOAN_STATUS_I => 'ปิดสัญญา',
            self::LOAN_STATUS_C => 'ยกเลิกสัญญา',
            self::LOAN_STATUS_E => 'ขาดส่ง',
            self::LOAN_STATUS_WC => 'รอพิจารณายกเลิกสัญญา'
        ];
    }

    public static function getLoanStatusDescription($type)
    {
        $statuses = self::getLoanStatusDropdown();
        return $statuses[$type] ?? 'ไม่ระบุ';
    }

    public static function getIpPayTypeDropdown(): array
    {
        return [
            self::IP_PAY_TYPE_A => 'ปกติ',
            self::IP_PAY_TYPE_B => 'ผ่อนผัน'
        ];
    }

    public static function getIpPayTypeDescription($type)
    {
        $types = self::getIpPayTypeDropdown();
        return $types[$type] ?? 'ไม่ระบุ';
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลการอนุมัติ
    //=========================================================================
    public static function getApprovalActions(): array
    {
        return [
            self::APPROVE_ACTION => 'อนุมัติ',
            self::REJECT_ACTION => 'ไม่อนุมัติ'
        ];
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลการฝากเงิน
    //=========================================================================
    public static function getValidDepositMonths(): array
    {
        return range(self::MIN_DEPOSIT_MONTHS, self::MAX_DEPOSIT_MONTHS);
    }

    public static function getDepositFlagDropdown(): array
    {
        return [
            self::DEPOSIT_FLAG_P => 'ยังไม่ได้จ่าย',
            self::DEPOSIT_FLAG_Y => 'จ่ายแล้ว',
            self::DEPOSIT_FLAG_W => 'รอตรวจสอบข้อมูล'
        ];
    }

    public static function getDepositFlagDescription($type)
    {
        $flags = self::getDepositFlagDropdown();
        return $flags[$type] ?? 'ไม่ระบุ';
    }

    public static function getDepositFlagForValidation(): array
    {
        return [
            self::DEPOSIT_FLAG_P,
            self::DEPOSIT_FLAG_Y,
            self::DEPOSIT_FLAG_W
        ];
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลสถานะบัญชี
    //=========================================================================
    public static function getAccountStatusesForValidation()
    {
        return [
            self::ACCOUNT_STATUS_W,
            self::ACCOUNT_STATUS_A,
            self::ACCOUNT_STATUS_I,
            self::ACCOUNT_STATUS_C,
            self::ACCOUNT_STATUS_L,
            self::ACCOUNT_STATUS_G,
            self::ACCOUNT_STATUS_WI,
            self::ACCOUNT_STATUS_IC,
            self::ACCOUNT_STATUS_GC,
        ];
    }

    public static function getDefaultStatusesForValidation()
    {
        return [
            self::DEFAULT_STATUS_A,
            self::DEFAULT_STATUS_I
        ];
    }

    public static function getUserStatusesDropdown(): array
    {
        return [
            self::USER_STATUS_A => 'ใช้งาน',
            self::USER_STATUS_I => 'ไม่ใช้งาน',
            self::USER_STATUS_P => 'รอดำเนินการ'
        ];
    }

    public static function getDefaultStatusDropdown(): array
    {
        return [
            self::DEFAULT_STATUS_A => 'ใช้งาน',
            self::DEFAULT_STATUS_I => 'ไม่ใช้งาน'
        ];
    }

    public static function getAccountStatusDropdown(): array
    {
        return [
            self::ACCOUNT_STATUS_W => 'รอพิจารณา',
            self::ACCOUNT_STATUS_A => 'ใช้งาน',
            self::ACCOUNT_STATUS_I => 'ปิดบัญชี',
            self::ACCOUNT_STATUS_C => 'ไม่ผ่านการเห็นชอบ',
            self::ACCOUNT_STATUS_L => 'กู้เงิน',
            self::ACCOUNT_STATUS_G => 'ค้ำประกัน',
            self::ACCOUNT_STATUS_WI => 'รอพิจารณาปิดบัญชี',
            self::ACCOUNT_STATUS_IC => 'ปิดบัญชีจากความผิด',
            self::ACCOUNT_STATUS_GC => 'รับภาระหนี้'
        ];
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลเดือน
    //=========================================================================
    public static function getMonthsDropdown(): array
    {
        return [
            self::MONTH_01 => 'มกราคม',
            self::MONTH_02 => 'กุมภาพันธ์',
            self::MONTH_03 => 'มีนาคม',
            self::MONTH_04 => 'เมษายน',
            self::MONTH_05 => 'พฤษภาคม',
            self::MONTH_06 => 'มิถุนายน',
            self::MONTH_07 => 'กรกฎาคม',
            self::MONTH_08 => 'สิงหาคม',
            self::MONTH_09 => 'กันยายน',
            self::MONTH_10 => 'ตุลาคม',
            self::MONTH_11 => 'พฤศจิกายน',
            self::MONTH_12 => 'ธันวาคม'
        ];
    }

    public static function getMonthsForValidation(): array
    {
        return [
            self::MONTH_01,
            self::MONTH_02,
            self::MONTH_03,
            self::MONTH_04,
            self::MONTH_05,
            self::MONTH_06,
            self::MONTH_07,
            self::MONTH_08,
            self::MONTH_09,
            self::MONTH_10,
            self::MONTH_11,
            self::MONTH_12
        ];
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลประเภทประกาศ
    //=========================================================================
    public static function getAnnounceTypeDropdown(): array
    {
        return [
            self::ANNOUNCE_TYPE_A => 'ประกาศ',
            self::ANNOUNCE_TYPE_B => 'ระเบียบ'
        ];
    }

    public static function getAnnounceTypeForValidation(): array
    {
        return [
            self::ANNOUNCE_TYPE_A,
            self::ANNOUNCE_TYPE_B
        ];
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลประเภทเอกสารการประชุม
    //=========================================================================
    public static function getUAMTypeForValidation(): array
    {
        return [
            self::UMDM_TYPE_OACC,
            self::UMDM_TYPE_CACC,
            self::UMDM_TYPE_LNACC,
            self::UMDM_TYPE_GUACC,
            self::UMDM_TYPE_LNEACC
        ];
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลสถานะสมรส
    //=========================================================================
    public static function getUserSpouseStatusDropdown(): array
    {
        return [
            self::USER_SPOUSE_STATUS_SINGLE => 'โสด',
            self::USER_SPOUSE_STATUS_WIDOWED => 'หม้าย',
            self::USER_SPOUSE_STATUS_MARRIED => 'สมรส',
            self::USER_SPOUSE_STATUS_UNKNOWN => 'ไม่ระบุ',
        ];
    }

    public static function getUserSpouseStatusDescription($status)
    {
        $statuses = self::getUserSpouseStatusDropdown();
        return $statuses[$status] ?? 'ไม่ระบุค่าที่กำหนด';
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลวิธีการชำระเงิน
    //=========================================================================
    public static function getPaymentMethodDropdown(): array
    {
        return [
            self::PAYMENT_METHOD_CASH => 'เงินสด',
            self::PAYMENT_METHOD_TRANSFER => 'โอนเงิน',
            self::PAYMENT_METHOD_DEDUCT => 'หักบัญชี',
            self::PAYMENT_METHOD_OTHER => 'อื่นๆ',
        ];
    }

    public static function getPaymentMethodDescription($method): string
    {
        $methods = self::getPaymentMethodDropdown();
        return $methods[$method] ?? 'ไม่ระบุ';
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลสถานะเอกสาร
    //=========================================================================
    public static function getDocumentStatusDropdown(): array
    {
        return [
            self::DOCUMENT_STATUS_ACTIVE => 'ใช้งาน',
            self::DOCUMENT_STATUS_INACTIVE => 'ไม่ใช้งาน'
        ];
    }

    public static function getDocumentStatusDescription(string $status): string
    {
        $statuses = self::getDocumentStatusDropdown();
        return $statuses[$status] ?? 'ไม่ระบุ';
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลประเภทห้องเอกสาร
    //=========================================================================
    public static function getDocumentRoomTypeDropdown(): array
    {
        return [
            self::DOCUMENT_ROOM_TYPE_A => 'ขอกู้สามัญ',
            self::DOCUMENT_ROOM_TYPE_B => 'ขอกู้ฉุกเฉิน',
            self::DOCUMENT_ROOM_TYPE_C => 'ลาออก',
            self::DOCUMENT_ROOM_TYPE_D => 'ลาออก(กรณีเสียชีวิต)',
            self::DOCUMENT_ROOM_TYPE_E => 'ใบถอนเงิน',
            self::DOCUMENT_ROOM_TYPE_F => 'ใบถอนเงิน(ผู้รับผลประโยชน์)',
            self::DOCUMENT_ROOM_TYPE_G => 'ไม่ได้ระบุ'
        ];
    }

    public static function getDocumentRoomTypeDescription($type): string
    {
        $types = self::getDocumentRoomTypeDropdown();
        return $types[$type] ?? 'ไม่ระบุ';
    }

    public static function getDocumentRoomTypeForValidation(): array
    {
        return [
            self::DOCUMENT_ROOM_TYPE_A,
            self::DOCUMENT_ROOM_TYPE_B,
            self::DOCUMENT_ROOM_TYPE_C,
            self::DOCUMENT_ROOM_TYPE_D,
            self::DOCUMENT_ROOM_TYPE_E,
            self::DOCUMENT_ROOM_TYPE_F,
            self::DOCUMENT_ROOM_TYPE_G
        ];
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลสถานะผลกำไรประจำปี
    //=========================================================================
    public static function getAnnualProfitStatusDropdown(): array
    {
        return [
            self::ANNUAL_PROFIT_STATUS_OPEN => 'เปิด/ยังไม่ปิดงบ',
            self::ANNUAL_PROFIT_STATUS_CLOSED => 'ปิดงบแล้ว',
            self::ANNUAL_PROFIT_STATUS_DISTRIBUTED => 'จัดสรรผลกำไรแล้ว'
        ];
    }

    public static function getAnnualProfitStatusDescription($status): string
    {
        $statuses = self::getAnnualProfitStatusDropdown();
        return $statuses[$status] ?? 'ไม่ระบุ';
    }

    public static function getAnnualProfitStatusForValidation(): array
    {
        return [
            self::ANNUAL_PROFIT_STATUS_OPEN,
            self::ANNUAL_PROFIT_STATUS_CLOSED,
            self::ANNUAL_PROFIT_STATUS_DISTRIBUTED
        ];
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลสถานะการจัดสรรเงินปันผล
    //=========================================================================
    public static function getDividendAllocationStatusDropdown(): array
    {
        return [
            self::DIVIDEND_ALLOCATION_STATUS_PENDING => 'รอดำเนินการ',
            self::DIVIDEND_ALLOCATION_STATUS_APPROVED => 'อนุมัติแล้ว',
            self::DIVIDEND_ALLOCATION_STATUS_DISTRIBUTED => 'แจกจ่ายเงินปันผลแล้ว'
        ];
    }

    public static function getDividendAllocationStatusDescription($status): string
    {
        $statuses = self::getDividendAllocationStatusDropdown();
        return $statuses[$status] ?? 'ไม่ระบุ';
    }

    public static function getDividendAllocationStatusForValidation(): array
    {
        return [
            self::DIVIDEND_ALLOCATION_STATUS_PENDING,
            self::DIVIDEND_ALLOCATION_STATUS_APPROVED,
            self::DIVIDEND_ALLOCATION_STATUS_DISTRIBUTED
        ];
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลสถานะการจ่ายเงินปันผลรายสมาชิก
    //=========================================================================
    public static function getMemberDividendStatusDropdown(): array
    {
        return [
            self::MEMBER_DIVIDEND_STATUS_PENDING => 'รอจ่าย',
            self::MEMBER_DIVIDEND_STATUS_PAID => 'จ่ายแล้ว',
            self::MEMBER_DIVIDEND_STATUS_TRANSFERRED => 'โอนเข้าบัญชี',
            self::MEMBER_DIVIDEND_STATUS_CANCELLED => 'ยกเลิก'
        ];
    }

    public static function getMemberDividendStatusDescription($status): string
    {
        $statuses = self::getMemberDividendStatusDropdown();
        return $statuses[$status] ?? 'ไม่ระบุ';
    }

    public static function getMemberDividendStatusForValidation(): array
    {
        return [
            self::MEMBER_DIVIDEND_STATUS_PENDING,
            self::MEMBER_DIVIDEND_STATUS_PAID,
            self::MEMBER_DIVIDEND_STATUS_TRANSFERRED,
            self::MEMBER_DIVIDEND_STATUS_CANCELLED
        ];
    }

    //=========================================================================
    // ฟังก์ชันสำหรับดึงข้อมูลวิธีการจ่ายเงินปันผล
    //=========================================================================
    public static function getMemberDividendPaymentMethodDropdown(): array
    {
        return [
            self::MEMBER_DIVIDEND_PAYMENT_METHOD_CASH => 'เงินสด',
            self::MEMBER_DIVIDEND_PAYMENT_METHOD_TRANSFER => 'โอนเงิน',
            self::MEMBER_DIVIDEND_PAYMENT_METHOD_DEPOSIT => 'ฝากเข้าบัญชี'
        ];
    }

    public static function getMemberDividendPaymentMethodDescription($method): string
    {
        $methods = self::getMemberDividendPaymentMethodDropdown();
        return $methods[$method] ?? 'ไม่ระบุ';
    }

    public static function getMemberDividendPaymentMethodForValidation(): array
    {
        return [
            self::MEMBER_DIVIDEND_PAYMENT_METHOD_CASH,
            self::MEMBER_DIVIDEND_PAYMENT_METHOD_TRANSFER,
            self::MEMBER_DIVIDEND_PAYMENT_METHOD_DEPOSIT
        ];
    }

    //====
// กลุ่มการติดตามการเปลี่ยนแปลง (Audit Log)
//====
    const AUDIT_ACTION_CREATE = 'CREATE';        // สร้างข้อมูลใหม่
    const AUDIT_ACTION_UPDATE = 'UPDATE';        // อัพเดทข้อมูล
    const AUDIT_ACTION_DELETE = 'DELETE';        // ลบข้อมูล
    const AUDIT_ACTION_STATUS_CHANGE = 'STATUS_CHANGE';  // เปลี่ยนสถานะ

// ฟังก์ชันสำหรับดึงข้อมูลประเภทการกระทำใน Audit Log
    public static function getAuditActionDropdown(): array
    {
        return [
            self::AUDIT_ACTION_CREATE => 'สร้างข้อมูลใหม่',
            self::AUDIT_ACTION_UPDATE => 'อัพเดทข้อมูล',
            self::AUDIT_ACTION_DELETE => 'ลบข้อมูล',
            self::AUDIT_ACTION_STATUS_CHANGE => 'เปลี่ยนสถานะ'
        ];
    }

    public static function getAuditActionDescription($action): string
    {
        $actions = self::getAuditActionDropdown();
        return $actions[$action] ?? 'ไม่ระบุ';
    }

    public static function getAuditActionForValidation(): array
    {
        return [
            self::AUDIT_ACTION_CREATE,
            self::AUDIT_ACTION_UPDATE,
            self::AUDIT_ACTION_DELETE,
            self::AUDIT_ACTION_STATUS_CHANGE
        ];
    }

    //====
// กลุ่มประเภทอาชีพ
//====
    const OCCUPATION_TYPE_M = 'M';    // อาชีพหลัก
    const OCCUPATION_TYPE_S = 'S';    // อาชีพรอง

//====
// ฟังก์ชันสำหรับดึงข้อมูลประเภทอาชีพ
//====
    public static function getOccupationTypeDropdown(): array
    {
        return [
            self::OCCUPATION_TYPE_M => 'อาชีพหลัก',
            self::OCCUPATION_TYPE_S => 'อาชีพรอง'
        ];
    }

    public static function getOccupationTypeDescription($type): string
    {
        $types = self::getOccupationTypeDropdown();
        return $types[$type] ?? 'ไม่ระบุ';
    }

    public static function getOccupationTypeForValidation(): array
    {
        return [
            self::OCCUPATION_TYPE_M,
            self::OCCUPATION_TYPE_S
        ];
    }

    public static function getDefaultStatusDescription($status): string
    {
        $statuses = self::getDefaultStatusDropdown();
        return $statuses[$status] ?? 'ไม่ระบุ';
    }

    //====
// กลุ่มประเภทสิทธิ์ผู้ใช้
//====
    const RULE_TYPE_A = 'A';    // Admin
    const RULE_TYPE_O = 'O';    // ทั่วไป

    /**
     * ฟังก์ชันสำหรับดึงข้อมูลประเภทสิทธิ์ผู้ใช้
     *
     * @return array
     */
    public static function getRuleTypeDropdown(): array
    {
        return [
            self::RULE_TYPE_A => 'Admin',
            self::RULE_TYPE_O => 'ทั่วไป'
        ];
    }

    /**
     * ฟังก์ชันสำหรับดึงคำอธิบายประเภทสิทธิ์ผู้ใช้
     *
     * @param string $type
     * @return string
     */
    public static function getRuleTypeDescription($type): string
    {
        $types = self::getRuleTypeDropdown();
        return $types[$type] ?? 'ไม่ระบุ';
    }

    /**
     * ฟังก์ชันสำหรับดึงค่าประเภทสิทธิ์ผู้ใช้สำหรับการตรวจสอบ
     *
     * @return array
     */
    public static function getRuleTypeForValidation(): array
    {
        return [
            self::RULE_TYPE_A,
            self::RULE_TYPE_O
        ];
    }

    //====
// กลุ่มประเภทสถานะ
//====
    const STATUS_TYPE_D = 'D';    // เงินฝาก
    const STATUS_TYPE_W = 'W';    // ถอนเงิน
    const STATUS_TYPE_P = 'P';    // ชำระเงิน

    /**
     * ฟังก์ชันสำหรับดึงข้อมูลประเภทสถานะ
     *
     * @return array
     */
    public static function getStatusTypesDropdown(): array
    {
        return [
            self::STATUS_TYPE_D => 'เงินฝาก',
            self::STATUS_TYPE_W => 'ถอนเงิน',
            self::STATUS_TYPE_P => 'ชำระเงิน'
        ];
    }

    /**
     * ฟังก์ชันสำหรับดึงคำอธิบายประเภทสถานะ
     *
     * @param string $type
     * @return string
     */
    public static function getStatusTypeDescription($type): string
    {
        $types = self::getStatusTypesDropdown();
        return $types[$type] ?? 'ไม่ระบุ';
    }

    /**
     * ฟังก์ชันสำหรับดึงค่าประเภทสถานะสำหรับการตรวจสอบ
     *
     * @return array
     */
    public static function getStatusTypeForValidation(): array
    {
        return [
            self::STATUS_TYPE_D,
            self::STATUS_TYPE_W,
            self::STATUS_TYPE_P
        ];
    }

    //====
// กลุ่มสถานะรายการหุ้น
//====
    const UNIT_TRAN_STATUS_A = 'A';    // ใช้งาน
    const UNIT_TRAN_STATUS_I = 'I';    // ไม่ใช้งาน

// ฟังก์ชันสำหรับดึงข้อมูลสถานะรายการหุ้น
    public static function getUnitTranStatusDropdown(): array
    {
        return [
            self::UNIT_TRAN_STATUS_A => 'ใช้งาน',
            self::UNIT_TRAN_STATUS_I => 'ไม่ใช้งาน'
        ];
    }

    public static function getUnitTranStatusDescription($status): string
    {
        $statuses = self::getUnitTranStatusDropdown();
        return $statuses[$status] ?? 'ไม่ระบุ';
    }

}
