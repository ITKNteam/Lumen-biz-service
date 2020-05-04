<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserPushToken extends Model
{
    protected $table = 'biz_users_push_token';
    protected $primaryKey = 'id';

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId): void
    {
        $this->user_id = $userId;
    }

    /**
     * @return mixed
     */
    public function getPushToken()
    {
        return $this->push_token;
    }

    /**
     * @param mixed $pushToken
     */
    public function setPushToken($pushToken): void
    {
        $this->push_token = $pushToken;
    }

    /**
     * @return mixed
     */
    public function getDeviceId()
    {
        return $this->device_id;
    }

    /**
     * @param mixed $deviceId
     */
    public function setDeviceId($deviceId): void
    {
        $this->device_id = $deviceId;
    }

    /**
     * @return mixed
     */
    public function getDeviceBrand()
    {
        return $this->device_brand;
    }

    /**
     * @param mixed $deviceBrand
     */
    public function setDeviceBrand($deviceBrand): void
    {
        $this->device_brand = $deviceBrand;
    }

    /**
     * @return mixed
     */
    public function getIsAndroid()
    {
        return $this->is_android;
    }

    /**
     * @param mixed $isAndroid
     */
    public function setIsAndroid($isAndroid): void
    {
        $this->is_android = $isAndroid;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }




}
