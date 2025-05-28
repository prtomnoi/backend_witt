<?php

namespace App\Models;

use App\Enums\Enum;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model สำหรับตาราง account
 *
 * @property int $account_id รหัสบัญชี (primary key)
 * @property int $user_id รหัสผู้ใช้งาน
 * @property string $account_name ชื่อบัญชี
 * @property string $account_no เลขที่บัญชี
 * @property int $account_book_no เลขที่สมุดบัญชี
 * @property string $account_status สถานะบัญชี (W=รอพิจารณา, A=ใช้งาน, I=ปิดบัญชี, C=ไม่ผ่านการเห็นชอบ, L=กู้เงิน, G=ค้ำประกัน, WI=รอพิจารณาปิดบัญชี, IC=ปิดบัญชีจากความผิด, GC=รับภาระหนี้)
 * @property int|null $account_consider_by รหัสผู้พิจารณา
 * @property string|null $account_consider_date วันที่พิจารณา
 * @property string|null $account_consider_remark หมายเหตุการพิจารณา
 * @property string|null $account_start_date วันที่เริ่มใช้บัญชี
 * @property int|null $account_final_unit จำนวนหุ้นสุดท้าย
 * @property float|null $account_balance ยอดเงินในบัญชี
 * @property float|null $account_balance_loan_normal ยอดเงินกู้สามัญ
 * @property float|null $account_balance_loan_emer ยอดเงินกู้ฉุกเฉิน
 * @property string|null $account_open_date วันที่เปิดบัญชี
 * @property string|null $account_close_date วันที่ปิดบัญชี
 * @property string|null $account_close_remark หมายเหตุการปิดบัญชี
 * @property int|null $account_created_by รหัสผู้สร้าง
 * @property string|null $account_created_date วันที่สร้าง
 * @property int|null $account_updated_by รหัสผู้อัพเดต
 * @property string|null $account_updated_date วันที่อัพเดต
 */
class Account extends Model
{

    use Auditable;

    /**
     * ชื่อตาราง
     *
     * @var string
     */
    protected $table = 'account';

    /**
     * Primary key
     *
     * @var string
     */
    protected $primaryKey = 'account_id';

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
        'user_id',
        'account_name',
        'account_no',
        'account_book_no',
        'account_status',
        'account_consider_by',
        'account_consider_date',
        'account_consider_remark',
        'account_start_date',
        'account_final_unit',
        'account_balance',
        'account_balance_loan_normal',
        'account_balance_loan_emer',
        'account_open_date',
        'account_close_date',
        'account_close_remark',
        'account_created_by',
        'account_created_date',
        'account_updated_by',
        'account_updated_date',
    ];

    /**
     * ความสัมพันธ์กับตาราง user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * ความสัมพันธ์กับตาราง user สำหรับผู้สร้าง
     *
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_created_by', 'user_id');
    }

    /**
     * ความสัมพันธ์กับตาราง user สำหรับผู้อัพเดต
     *
     * @return BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_updated_by', 'user_id');
    }

    /**
     * ความสัมพันธ์กับตาราง unit_tran
     *
     * @return HasMany
     */
    public function unitTrans(): HasMany
    {
        return $this->hasMany(UnitTran::class, 'account_id', 'account_id');
    }

    /**
     * ความสัมพันธ์กับตาราง account_transaction สำหรับการฝากเงิน
     *
     * @return HasMany
     */
    public function deposits(): HasMany
    {
        return $this->hasMany(AccountTransaction::class, 'account_no', 'account_no')
            ->where('at_type', Enum::AT_TYPE_DP);
    }

    /**
     * ความสัมพันธ์กับตาราง account_transaction สำหรับการถอนเงิน
     *
     * @return HasMany
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(AccountTransaction::class, 'account_no', 'account_no')
            ->where('at_type', Enum::AT_TYPE_WD);
    }

    /**
     * ความสัมพันธ์กับตาราง loan
     *
     * @return HasMany
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'account_id', 'account_id');
    }

    /**
     * ความสัมพันธ์กับตาราง guarantor_loan
     *
     * @return HasMany
     */
    public function guarantorLoans(): HasMany
    {
        return $this->hasMany(GuarantorLoan::class, 'account_id', 'account_id');
    }

    /**
     * ความสัมพันธ์กับตาราง account_transaction
     *
     * @return HasMany
     */
    public function accountTransactions(): HasMany
    {
        return $this->hasMany(AccountTransaction::class, 'account_no', 'account_no');
    }

    /**
     * ความสัมพันธ์กับตาราง account_main_transaction
     *
     * @return HasMany
     */
    public function accountMainTransactions(): HasMany
    {
        return $this->hasMany(AccountMainTransaction::class, 'account_no', 'account_no');
    }
}
