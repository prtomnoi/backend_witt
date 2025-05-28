<?php

namespace App\Services;

use App\Enums\Enum;
use App\Models\Beneficiary;
use App\Models\Occupation;
use App\Models\OtherGroupMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserService
{
    public function getAllUsers($perPage = 10)
    {
        return User::with(['position', 'rule'])->paginate($perPage);
    }

    public function getUsersWithRelations(): Collection
    {
        return User::whereIn('user_status', [
            Enum::USER_STATUS_A,  // Active
            Enum::USER_STATUS_P   // Pending
        ])->get();
    }


    public function getActiveUsers(): Collection
    {
        return User::whereIn('user_status', [
            Enum::USER_STATUS_A // Active
        ])->get();
    }

    public function findUserById($id): User
    {
        return User::findOrFail($id);
    }

    public function createUser(array $data)
    {
        DB::beginTransaction();
        try {
            // สร้าง user
            $user = $this->storeUserData($data);

            // บันทึกรูปภาพ
            $this->storeUserImages($user, $data);

            // บันทึกข้อมูลสมาชิกในกลุ่มอื่นๆ
            if (isset($data['ogm_names'])) {
                $this->storeOtherGroupMembers($user->user_id, $data['ogm_names']);
            }

            // บันทึกข้อมูล Beneficiaries
            if (isset($data['beneficiaries_name'])) {
                $this->storeBeneficiaries($user->user_id, $data);
            }

            // บันทึกข้อมูล Occupations
            if (isset($data['occupation_name'])) {
                $this->storeOccupations($user->user_id, $data);
            }

            DB::commit();
            return $user;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function storeUserData(array $data): User
    {
        $user = new User();
        $user->user_id_no = $data['user_id_no'];
        $user->user_password = Hash::make($data['user_password']);
        $user->user_prefix = $data['user_prefix'];
        $user->user_fname = $data['user_fname'];
        $user->user_lname = $data['user_lname'];
        $user->user_birthday = $data['user_birthday'];
        $user->user_email = $data['user_email'];
        $user->user_tel = $data['user_tel'];
        $user->user_address = $data['user_address'];
        $user->district_code = $data['district_code'];
        $user->amphur_id = $data['amphur_id'];
        $user->province_id = $data['province_id'];
        $user->zip_id = $data['zip_id'];
        $user->rule_id = $data['rule_id'];
        $user->position_id = $data['position_id'];
        $user->user_spouse_status = $data['user_spouse_status'];
        $user->user_spouse_name = $data['user_spouse_name'];
        $user->user_spouse_number = $data['user_spouse_number'];
        $user->user_start_date = $data['user_start_date'];
        $user->user_status = Enum::USER_STATUS_P;
        $user->user_created_by = Enum::USER_SYSTEM_NAME;
        $user->user_number = $data['user_number'];
        $user->witness1 = $data['witness1'];
        $user->witness2 = $data['witness2'];

        $user->save();
        return $user;
    }

    /**
     * จัดเก็บรูปภาพของผู้ใช้
     *
     * @param User $user อ็อบเจกต์ผู้ใช้ที่ต้องการจัดเก็บรูปภาพ
     * @param array $data ข้อมูลรูปภาพที่ต้องการจัดเก็บ
     * @return void
     *
     * ฟังก์ชันนี้จะจัดเก็บรูปภาพบัตรประชาชนและรูปภาพที่อยู่อาศัยของผู้ใช้
     * โดยจะเข้ารหัสรูปภาพและบันทึกลงในฐานข้อมูล
     */
    private function storeUserImages(User $user, array $data): void
    {
        if (isset($data['user_id_no_pic']) && $data['user_id_no_pic']) {
            $file = $data['user_id_no_pic'];
            // ตรวจสอบไฟล์
            $this->validateImage($file);

            $binaryData = file_get_contents($file->getRealPath());
            $base64 = base64_encode($binaryData);
            $encrypted = Crypt::encryptString($base64);

            $user->user_id_no_pic = $encrypted;
            $user->user_id_no_pic_type = $file->getClientMimeType();
        }

        if (isset($data['user_home_pic']) && $data['user_home_pic']) {
            $file = $data['user_home_pic'];
            // ตรวจสอบไฟล์
            $this->validateImage($file);

            $binaryData = file_get_contents($file->getRealPath());
            $base64 = base64_encode($binaryData);
            $encrypted = Crypt::encryptString($base64);

            $user->user_home_pic = $encrypted;
            $user->user_home_pic_type = $file->getClientMimeType();
        }

        $user->save();
    }

    private function storeOtherGroupMembers(int $userId, array $names): void
    {
        foreach ($names as $name) {
            if (!empty($name)) {
                OtherGroupMember::create([
                    'user_id' => $userId,
                    'ogm_name' => $name,
                    'ogm_status' => Enum::DEFAULT_STATUS_A,
                    'ogm_created_by' => auth()->id(),
                    'ogm_updated_by' => auth()->id()
                ]);
            }
        }
    }

//    private function storeBeneficiaries(int $userId, array $data): void
//    {
//        foreach ($data['beneficiaries_name'] as $key => $name) {
//            if (!empty($name)) {
//                Beneficiary::create([
//                    'user_id' => $userId,
//                    'beneficiaries_name' => $name,
//                    'beneficiaries_age' => $data['beneficiaries_age'][$key],
//                    'beneficiaries_relation' => $data['beneficiaries_relation'][$key],
//                    'beneficiaries_created_by' => auth()->id(),
//                    'beneficiaries_updated_by' => auth()->id()
//                ]);
//            }
//        }
//    }

    /**
     * บันทึกข้อมูลผู้รับผลประโยชน์
     *
     * @param int $userId รหัสผู้ใช้งาน
     * @param array $data ข้อมูลผู้รับผลประโยชน์ที่ส่งมาจากฟอร์ม ประกอบด้วย:
     *   - beneficiaries_name[] : ชื่อผู้รับผลประโยชน์
     *   - beneficiaries_age[] : อายุผู้รับผลประโยชน์
     *   - beneficiaries_relation[] : ความสัมพันธ์กับผู้รับผลประโยชน์
     *   - beneficiaries_ratio[] : สัดส่วนผลประโยชน์
     *   - beneficiaries_id_no[] : เลขบัตรประชาชน
     *   - beneficiaries_id_no_pic[] : ไฟล์รูปบัตรประชาชน (optional)
     * @return void
     * @throws \Exception เมื่อข้อมูลไม่ถูกต้องหรือมีข้อผิดพลาดในการบันทึก
     */
    private function storeBeneficiaries(int $userId, array $data): void
    {
        // ตรวจสอบผลรวมของสัดส่วนผลประโยชน์ต้องเท่ากับ 100
        $totalRatio = array_sum($data['beneficiaries_ratio'] ?? []);
        if ($totalRatio != 100) {
            throw new \Exception('สัดส่วนผลประโยชน์รวมต้องเท่ากับ 100%');
        }

        foreach ($data['beneficiaries_name'] as $key => $name) {
            if (!empty($name)) {
                try {
                    // จัดการรูปภาพบัตรประชาชน (ถ้ามี)
//                    $idNoPic = null;
//                    $idNoPicType = null;
//
//                    if (isset($data['beneficiaries_id_no_pic'][$key]) && $data['beneficiaries_id_no_pic'][$key]) {
//                        $file = $data['beneficiaries_id_no_pic'][$key];
//                        // ตรวจสอบไฟล์
//                        $this->validateImage($file);
//
//                        // แปลงและเข้ารหัสไฟล์
//                        $binaryData = file_get_contents($file->getRealPath());
//                        $base64 = base64_encode($binaryData);
//                        $idNoPic = Crypt::encryptString($base64);
//                        $idNoPicType = $file->getClientMimeType();
//                    }

                    // บันทึกข้อมูล
                    Beneficiary::create([
                        'user_id' => $userId,
                        'beneficiaries_name' => $name,
                        'beneficiaries_age' => $data['beneficiaries_age'][$key],
                        'beneficiaries_relation' => $data['beneficiaries_relation'][$key],
                        'beneficiaries_ratio' => $data['beneficiaries_ratio'][$key],
                        'beneficiaries_id_no' => $data['beneficiaries_id_no'][$key] ?? null,
//                        'beneficiaries_id_no_pic' => $idNoPic,
//                        'beneficiaries_id_no_pic_type' => $idNoPicType,
                        'beneficiaries_created_by' => auth()->id(),
                        'beneficiaries_updated_by' => auth()->id()
                    ]);

                } catch (\Exception $e) {
                    throw new \Exception("ไม่สามารถบันทึกข้อมูลผู้รับผลประโยชน์ {$name} ได้: " . $e->getMessage());
                }
            }
        }
    }


    /**
     * ตรวจสอบความถูกต้องของไฟล์รูปภาพและ PDF
     *
     * @param \Illuminate\Http\UploadedFile $file ไฟล์ที่อัพโหลด
     * @return void
     * @throws \Exception เมื่อไฟล์ไม่ถูกต้องตามเงื่อนไข
     */
    private function validateImage($file): void
    {
        // เพิ่ม application/pdf เข้าไปในประเภทไฟล์ที่อนุญาต
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf'
        ];

        $maxSize = 5 * 1024 * 1024; // 5MB
        $mimeType = $file->getMimeType();

        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new \Exception('ประเภทไฟล์ไม่ถูกต้อง อนุญาตเฉพาะ JPG, PNG, GIF และ PDF เท่านั้น');
        }

        if ($file->getSize() > $maxSize) {
            throw new \Exception('ขนาดไฟล์ใหญ่เกินไป ต้องไม่เกิน 5MB');
        }

        // ตรวจสอบเพิ่มเติมสำหรับไฟล์ PDF
        if ($mimeType === 'application/pdf') {
            // ตรวจสอบว่าเป็น PDF จริงๆ
            $fileContent = file_get_contents($file->getRealPath());
            if (strpos($fileContent, '%PDF-') !== 0) {
                throw new \Exception('ไฟล์ PDF ไม่ถูกต้อง');
            }

//            // ตรวจสอบจำนวนหน้า PDF (ถ้าต้องการจำกัด)
//            // ต้องติดตั้ง setasign/fpdi ก่อน
//            try {
//                $pdf = new \setasign\Fpdi\Fpdi();
//                $pageCount = $pdf->setSourceFile($file->getRealPath());
//
//                if ($pageCount > 5) { // ตัวอย่างจำกัดไม่เกิน 5 หน้า
//                    throw new \Exception('ไฟล์ PDF ต้องมีไม่เกิน 5 หน้า');
//                }
//            } catch (\Exception $e) {
//                throw new \Exception('ไม่สามารถตรวจสอบไฟล์ PDF ได้: ' . $e->getMessage());
//            }
        }
    }

    private function storeOccupations(int $userId, array $data): void
    {
        foreach ($data['occupation_name'] as $key => $name) {
            if (!empty($name)) {
                Occupation::create([
                    'user_id' => $userId,
                    'occupation_name' => $name,
                    'occupation_income' => $data['occupation_income'][$key],
                    'occupation_type' => $data['occupation_type'][$key],
                    'occupation_status' => Enum::DEFAULT_STATUS_A,
                    'occupation_created_by' => auth()->id(),
                    'occupation_updated_by' => auth()->id()
                ]);
            }
        }


    }

    public function updateUser(User $user, array $data)
    {
        DB::beginTransaction();
        try {
            // อัพเดทข้อมูลพื้นฐานของ user
            $this->updateUserData($user, $data);

            // อัพเดทรูปภาพ
            $this->updateUserImages($user, $data);

            // อัพเดทข้อมูลสมาชิกในกลุ่มอื่นๆ
            $this->updateOtherGroupMembers($user->user_id, $data);

            // อัพเดทข้อมูล Beneficiaries
            $this->updateBeneficiaries($user->user_id, $data);

            // อัพเดทข้อมูล Occupations
            $this->updateOccupations($user->user_id, $data);

            DB::commit();
            return $user;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function updateUserData(User $user, array $data): void
    {
        $user->fill(Arr::except($data, ['user_password', 'user_avatar', 'user_id_no_pic', 'user_home_pic']));

        if (!empty($data['user_password'])) {
            $user->user_password = Hash::make($data['user_password']);
        }

        $user->user_updated_by = Enum::USER_SYSTEM_NAME;
        $user->save();
    }

    private function updateUserImages(User $user, array $data): void
    {

        if (isset($data['user_id_no_pic']) && $data['user_id_no_pic']) {
            $file = $data['user_id_no_pic'];
            // ตรวจสอบไฟล์
            $this->validateImage($file);

            $binaryData = file_get_contents($file->getRealPath());
            $base64 = base64_encode($binaryData);
            $encrypted = Crypt::encryptString($base64);

            $user->user_id_no_pic = $encrypted;
            $user->user_id_no_pic_type = $file->getClientMimeType();
        }

        if (isset($data['user_home_pic']) && $data['user_home_pic']) {
            $file = $data['user_home_pic'];
            // ตรวจสอบไฟล์
            $this->validateImage($file);

            $binaryData = file_get_contents($file->getRealPath());
            $base64 = base64_encode($binaryData);
            $encrypted = Crypt::encryptString($base64);

            $user->user_home_pic = $encrypted;
            $user->user_home_pic_type = $file->getClientMimeType();
        }

        $user->save();
    }

    private function updateOtherGroupMembers(int $userId, array $data): void
    {
        // ลบข้อมูลเก่า
        OtherGroupMember::where('user_id', $userId)->delete();

        // บันทึกข้อมูลใหม่
        if (isset($data['ogm_names'])) {
            foreach ($data['ogm_names'] as $name) {
                if (!empty($name)) {
                    OtherGroupMember::create([
                        'user_id' => $userId,
                        'ogm_name' => $name,
                        'ogm_status' => Enum::DEFAULT_STATUS_A,
                        'ogm_created_by' => auth()->id(),
                        'ogm_updated_by' => auth()->id()
                    ]);
                }
            }
        }
    }

    /**
     * อัปเดตข้อมูลผู้รับผลประโยชน์ของผู้ใช้
     *
     * @param int $userId รหัสผู้ใช้
     * @param array $data ข้อมูลผู้รับผลประโยชน์ที่ต้องการอัปเดต
     * @return void
     * @throws \Exception เมื่อเกิดข้อผิดพลาดในการบันทึกข้อมูล
     */
    private function updateBeneficiaries(int $userId, array $data): void
    {
        // ตรวจสอบผลรวมของสัดส่วนผลประโยชน์ต้องเท่ากับ 100
        $totalRatio = array_sum($data['beneficiaries_ratio'] ?? []);
        if ($totalRatio != 100) {
            throw new \Exception('สัดส่วนผลประโยชน์รวมต้องเท่ากับ 100%');
        }

        // ลบข้อมูลเก่า
        Beneficiary::where('user_id', $userId)->delete();

        foreach ($data['beneficiaries_name'] as $key => $name) {
            if (!empty($name)) {
                try {
                    // จัดการรูปภาพบัตรประชาชน (ถ้ามี)
//                    $idNoPic = null;
//                    $idNoPicType = null;
//
//                    if (isset($data['beneficiaries_id_no_pic'][$key]) && $data['beneficiaries_id_no_pic'][$key]) {
//                        $file = $data['beneficiaries_id_no_pic'][$key];
//                        // ตรวจสอบไฟล์
//                        $this->validateImage($file);
//
//                        // แปลงและเข้ารหัสไฟล์
//                        $binaryData = file_get_contents($file->getRealPath());
//                        $base64 = base64_encode($binaryData);
//                        $idNoPic = Crypt::encryptString($base64);
//                        $idNoPicType = $file->getClientMimeType();
//                    }

                    // บันทึกข้อมูล
                    Beneficiary::create([
                        'user_id' => $userId,
                        'beneficiaries_name' => $name,
                        'beneficiaries_age' => $data['beneficiaries_age'][$key],
                        'beneficiaries_relation' => $data['beneficiaries_relation'][$key],
                        'beneficiaries_ratio' => $data['beneficiaries_ratio'][$key],
                        'beneficiaries_id_no' => $data['beneficiaries_id_no'][$key] ?? null,
//                        'beneficiaries_id_no_pic' => $idNoPic,
//                        'beneficiaries_id_no_pic_type' => $idNoPicType,
                        'beneficiaries_created_by' => auth()->id(),
                        'beneficiaries_updated_by' => auth()->id()
                    ]);

                } catch (\Exception $e) {
                    throw new \Exception("ไม่สามารถบันทึกข้อมูลผู้รับผลประโยชน์ {$name} ได้: " . $e->getMessage());
                }
            }
        }
    }


    private function updateOccupations(int $userId, array $data): void
    {
        // ลบข้อมูลเก่า
        Occupation::where('user_id', $userId)->delete();

        // บันทึกข้อมูลใหม่
        if (isset($data['occupation_name'])) {
            foreach ($data['occupation_name'] as $key => $name) {
                if (!empty($name)) {
                    Occupation::create([
                        'user_id' => $userId,
                        'occupation_name' => $name,
                        'occupation_income' => $data['occupation_income'][$key],
                        'occupation_type' => $data['occupation_type'][$key],
                        'occupation_status' => Enum::DEFAULT_STATUS_A,
                        'occupation_created_by' => auth()->id(),
                        'occupation_updated_by' => auth()->id()
                    ]);
                }
            }
        }
    }

    public function findBeneficiaryById(int $id): Collection
    {
        return Beneficiary::where('user_id', $id)->get();
    }

    public function findOccupationById($id): ?Occupation
    {
        return Occupation::find($id);
    }


    public function filterUsers(array $filters, $perPage = 10)
    {
        $query = User::with([
            'position',
            'rule',
            'accounts',
        ]);


        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('user_fname', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('user_lname', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('user_id_no', 'LIKE', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('user_status', $filters['status']);
        }

        if (!empty($filters['position'])) {
            $query->where('position_id', $filters['position']);
        }

        return $query->orderBy('user_created_date', 'desc')->paginate($perPage);
    }

    /**
     * แปลงข้อมูลรูปภาพที่เข้ารหัสกลับเป็นรูปภาพ
     *
     * @param User $user ข้อมูลผู้ใช้
     * @param string $imageType ประเภทของรูปภาพ ('id_no' หรือ 'home')
     * @return Response รูปภาพที่ถูกแปลงกลับหรือข้อความแจ้งข้อผิดพลาด
     *
     * @throws \Exception เมื่อไม่พบรูปภาพหรือประเภทรูปภาพไม่ถูกต้อง
     */
    public function getUserImage(User $user, string $imageType): Response
    {
        try {
            $imageData = null;
            $mimeType = null;

            switch ($imageType) {
                case 'id_no':
                    if (!$user->user_id_no_pic) {
                        throw new \Exception('Image not found');
                    }
                    $imageData = $user->user_id_no_pic;
                    $mimeType = $user->user_id_no_pic_type;
                    break;

                case 'home':
                    if (!$user->user_home_pic) {
                        throw new \Exception('Image not found');
                    }
                    $imageData = $user->user_home_pic;
                    $mimeType = $user->user_home_pic_type;
                    break;

                default:
                    throw new \Exception('Invalid image type');
            }

            // ถอดรหัสและแปลงกลับเป็นไฟล์รูปภาพ
            $decrypted = Crypt::decryptString($imageData);
            $binaryData = base64_decode($decrypted);

            return response($binaryData)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline');

        } catch (\Exception $e) {
            // จัดการข้อผิดพลาด
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }


}
