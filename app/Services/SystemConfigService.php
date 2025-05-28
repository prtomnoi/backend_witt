<?php

namespace App\Services;

use App\Models\SystemConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class SystemConfigService
{
    /**
     * Get all system configurations
     *
     * @return Collection
     */
    public function getAllConfigs(): Collection
    {
        return SystemConfig::all();
    }

    /**
     * Get a specific system configuration by ID
     *
     * @param int $id
     * @return SystemConfig
     */
    public function getConfigById(int $id): SystemConfig
    {
        return SystemConfig::findOrFail($id);
    }

    /**
     * ดึงค่า system_config_key ตาม system_config_type และ system_config_name
     *
     * @param string $type ประเภทของการตั้งค่า
     * @param string $name ชื่อของการตั้งค่า
     * @return string|null ค่า system_config_key ที่พบ หรือ null ถ้าไม่พบ
     */
    public function getConfigKeyByTypeAndName(string $type, string $name): ?string
    {
        $config = SystemConfig::where('system_config_type', $type)
            ->where('system_config_name', $name)
            ->first();

        return $config ? $config->system_config_key : null;
    }

    /**
     * Create a new system configuration
     *
     * @param array $data
     * @return SystemConfig
     */
    public function createConfig(array $data): SystemConfig
    {
        $this->validateUniqueTypeAndName($data);
        $data['system_config_created_by'] = Auth::id();
        return SystemConfig::create($data);
    }

    /**
     * Update an existing system configuration
     *
     * @param int $id
     * @param array $data
     * @return SystemConfig
     */
    public function updateConfig(int $id, array $data): SystemConfig
    {
        $config = SystemConfig::findOrFail($id);
        $this->validateUniqueTypeAndName($data, $id);
        $data['system_config_updated_by'] = Auth::id();
        $config->update($data);
        return $config;
    }

    /**
     * Delete a system configuration
     *
     * @param int $id
     * @return bool
     */
    public function deleteConfig(int $id): bool
    {
        $config = SystemConfig::findOrFail($id);
        return $config->delete();
    }

    /**
     * Get system configuration by key
     *
     * @param string $key
     * @return SystemConfig|null
     */
    public function getConfigByKey(string $key): ?SystemConfig
    {
        return SystemConfig::where('system_config_key', $key)->first();
    }

    /**
     * ตรวจสอบความไม่ซ้ำกันของ system_config_type และ system_config_name
     *
     * @param array $data ข้อมูลที่ต้องการตรวจสอบ
     * @param int|null $excludeId ID ที่ต้องการยกเว้นในการตรวจสอบ (ใช้สำหรับการอัปเดต)
     * @throws ValidationException เมื่อพบการซ้ำกันของ type และ name
     * @return void
     */
    private function validateUniqueTypeAndName(array $data, ?int $excludeId = null): void
    {
        $query = SystemConfig::where('system_config_type', $data['system_config_type'])
            ->where('system_config_name', $data['system_config_name']);

        if ($excludeId) {
            $query->where('system_config_id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'system_config_type' => 'The combination of system_config_type and system_config_name already exists.',
            ]);
        }
    }
}
