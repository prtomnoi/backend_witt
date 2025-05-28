<?php

namespace App\Models;

use App\Enums\Enum;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ข้อมูลผู้ใช้งาน
 *
 * @property int $user_id รหัสผู้ใช้งาน (primary key)
 * @property string $user_id_no รหัสบัตรประชาชน
 * @property string $user_password รหัสผ่าน
 * @property string|null $user_prefix คำนำหน้า นาย, นาง, นางสาว
 * @property string|null $user_fname ชื่อ
 * @property string|null $user_lname นามสกุล
 * @property string|null $user_gender เพศ M = ชาย, F = หญิง
 * @property string|null $user_birthday วันเกิด
 * @property string $user_email อีเมล
 * @property string|null $user_tel เบอร์โทรศัพท์
 * @property string $user_number เลขที่สมาชิก
 * @property string|null $witness1 พยาน1
 * @property string|null $witness2 พยาน2
 * @property string|null $user_avatar path รูปภาพประจำตัว
 * @property string|null $user_id_no_pic หลักฐาน1
 * @property string|null $user_id_no_pic_type สกุลไฟล์หลักฐาน1
 * @property string|null $user_home_pic หลักฐาน2
 * @property string|null $user_home_pic_type สกุลไฟล์หลักฐาน2
 * @property string|null $user_address ที่อยู่
 * @property string|null $district_code ข้อมูลตำบล/แขวง
 * @property int|null $amphur_id ข้อมูลอำเภอ/เขต
 * @property int|null $province_id ข้อมูลจังหวัด
 * @property int|null $zip_id ข้อมูลรหัสไปรษณี
 * @property int $rule_id ข้อมูลสิทธิ์ของผู้ใช้
 * @property int $position_id ข้อมูลตำแหน่ง
 * @property string|null $user_spouse_status สถานะสมรส S = โสด, W = หม้าย, M = สมรส, (ค่าว่าง) = ไม่ระบุ
 * @property string|null $user_spouse_name ชื่อคู่สมรส
 * @property string|null $user_spouse_number เลขที่สมาชิกคู่สมรส
 * @property string|null $user_start_date วันที่เป็นสมาชิก
 * @property string|null $auth_id_token token
 * @property string $user_status สถานะข้อมูล A = ใช้งาน, I = ไม่ใช้งาน, P = รอดำเนินการ
 * @property string|null $user_created_by สร้างโดย
 * @property string $user_created_date สร้างเมื่อ
 * @property string|null $user_updated_by อัพเดตโดย
 * @property string $user_updated_date อัพเดตเมื่อ
 *
 * @property-read Position|null $position ความสัมพันธ์กับตำแหน่ง
 * @property-read Rule|null $rule ความสัมพันธ์กับสิทธิ์
 * @property-read District|null $district ความสัมพันธ์กับตำบล
 * @property-read Amphur|null $amphur ความสัมพันธ์กับอำเภอ
 * @property-read Province|null $province ความสัมพันธ์กับจังหวัด
 * @property-read Zipcode|null $zipcode ความสัมพันธ์กับรหัสไปรษณีย์
 * @property-read Account[] $accounts ความสัมพันธ์กับบัญชี
 * @property-read Deposit[] $deposits ความสัมพันธ์กับการฝาก
 * @property-read OtherGroupMember[] $otherGroupMembers ความสัมพันธ์กับสมาชิกกลุ่มอื่น
 * @property-read MeetingDoc[] $createdMeetingDocs ความสัมพันธ์กับเอกสารการประชุมที่สร้าง
 * @property-read MeetingDoc[] $updatedMeetingDocs ความสัมพันธ์กับเอกสารการประชุมที่แก้ไข
 * @property-read Beneficiary[] $beneficiaries ความสัมพันธ์กับผู้รับผลประโยชน์
 * @property-read Occupation[] $occupations ความสัมพันธ์กับอาชีพ
 * @property-read UserMeetingDocMapping[] $userMeetingDocMappings ความสัมพันธ์กับการประชุม
 */
class User extends Model
{
    use Auditable;

    /**
     * ชื่อตาราง
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * คีย์หลัก
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * ไม่ใช้ timestamps อัตโนมัติ
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * คอลัมน์ที่สามารถกำหนดค่าได้
     *
     * @var array
     */
    protected $fillable = [
        'user_id_no',
        'user_password',
        'user_prefix',
        'user_fname',
        'user_lname',
        'user_gender',
        'user_birthday',
        'user_email',
        'user_tel',
        'user_number',
        'witness1',
        'witness2',
        'user_avatar',
        'user_id_no_pic',
        'user_id_no_pic_type',
        'user_home_pic',
        'user_home_pic_type',
        'user_address',
        'district_code',
        'amphur_id',
        'province_id',
        'zip_id',
        'rule_id',
        'position_id',
        'user_spouse_status',
        'user_spouse_name',
        'user_spouse_number',
        'user_start_date',
        'auth_id_token',
        'user_status',
        'user_created_by',
        'user_created_date',
        'user_updated_by',
        'user_updated_date',
    ];

    /**
     * ความสัมพันธ์กับตำแหน่ง
     *
     * @return BelongsTo
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }

    /**
     * ความสัมพันธ์กับสิทธิ์
     *
     * @return BelongsTo
     */
    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'rule_id', 'rule_id');
    }

    /**
     * ความสัมพันธ์กับตำบล
     *
     * @return BelongsTo
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_code', 'district_code');
    }

    /**
     * ความสัมพันธ์กับอำเภอ
     *
     * @return BelongsTo
     */
    public function amphur(): BelongsTo
    {
        return $this->belongsTo(Amphur::class, 'amphur_id', 'amphur_id');
    }

    /**
     * ความสัมพันธ์กับจังหวัด
     *
     * @return BelongsTo
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

    /**
     * ความสัมพันธ์กับรหัสไปรษณีย์
     *
     * @return BelongsTo
     */
    public function zipcode(): BelongsTo
    {
        return $this->belongsTo(Zipcode::class, 'zip_id', 'zipcode_id');
    }

    /**
     * ความสัมพันธ์กับบัญชี
     *
     * @return HasMany
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'user_id', 'user_id');
    }

    /**
     * ความสัมพันธ์กับการฝาก
     *
     * @return HasMany
     */
    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class, 'user_id', 'user_id');
    }

    /**
     * ความสัมพันธ์กับสมาชิกกลุ่มอื่น
     *
     * @return HasMany
     */
    public function otherGroupMembers(): HasMany
    {
        return $this->hasMany(OtherGroupMember::class, 'user_id', 'user_id');
    }

    /**
     * ความสัมพันธ์กับเอกสารการประชุมที่สร้าง
     *
     * @return HasMany
     */
    public function createdMeetingDocs(): HasMany
    {
        return $this->hasMany(MeetingDoc::class, 'meeting_doc_created_by', 'user_id');
    }

    /**
     * ความสัมพันธ์กับเอกสารการประชุมที่แก้ไข
     *
     * @return HasMany
     */
    public function updatedMeetingDocs(): HasMany
    {
        return $this->hasMany(MeetingDoc::class, 'meeting_doc_updated_by', 'user_id');
    }

    /**
     * ความสัมพันธ์กับผู้รับผลประโยชน์
     *
     * @return HasMany
     */
    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class, 'user_id', 'user_id');
    }

    /**
     * ความสัมพันธ์กับอาชีพ
     *
     * @return HasMany
     */
    public function occupations(): HasMany
    {
        return $this->hasMany(Occupation::class, 'user_id', 'user_id');
    }

    /**
     * ความสัมพันธ์กับการประชุม
     *
     * @return HasMany
     */
    public function userMeetingDocMappings(): HasMany
    {
        return $this->hasMany(UserMeetingDocMapping::class, 'user_id', 'user_id');
    }

    /**
     * Accessor สำหรับที่อยู่เต็ม
     *
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->user_address} " .
            "ต.{$this->district->district_name} " .
            "อ.{$this->amphur->amphur_name} " .
            "จ.{$this->province->province_name} " .
            "{$this->zipcode->zipcode_name}";
    }

    /**
     * คืนค่าชื่อเต็มของผู้ใช้พร้อมคำนำหน้า
     *
     * @return string
     */
    public function getUserFullName(): string
    {
        return "{$this->user_prefix} {$this->user_fname} {$this->user_lname}";
    }

    /**
     * Mutator สำหรับวันเกิด
     *
     * @param string $value
     * @return void
     */
    public function setUserBirthdayAttribute($value): void
    {
        $this->attributes['user_birthday'] = date('Y-m-d', strtotime($value));
    }

    /**
     * Mutator สำหรับรหัสผ่าน
     *
     * @param string $value
     * @return void
     */
    public function setUserPasswordAttribute($value): void
    {
        $this->attributes['user_password'] = bcrypt($value);
    }

    /**
     * ตรวจสอบสถานะการเปิดบัญชี
     *
     * @return int 1 = สามารถเปิดบัญชีได้, 0 = ไม่สามารถเปิดบัญชีได้
     */
    public function checkOpenAccountStatus(): int
    {
        $validStatuses = [
            Enum::ACCOUNT_STATUS_A,
            Enum::ACCOUNT_STATUS_L,
            Enum::ACCOUNT_STATUS_G,
            Enum::ACCOUNT_STATUS_WI,
            Enum::ACCOUNT_STATUS_GC
        ];

        $latestAccount = $this->accounts()
            ->orderBy('account_created_date', 'desc')
            ->first();

        if ($latestAccount && in_array($latestAccount->account_status, $validStatuses)) {
            return 0;
        }

        return 1;
    }
}
