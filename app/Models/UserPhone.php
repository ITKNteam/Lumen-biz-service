<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserPhone extends Model
{
    protected $table = 'qlick.biz_users_phones';
    protected $primaryKey = 'id';



    public function setHashByCode($code)
    {
        $this->hash = md5($code.'salt'.time());
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number): void
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive): void
    {
        $this->is_active = $isActive;
    }

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
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * @param mixed $countryCode
     */
    public function setCountryCode($countryCode): void
    {
        $this->country_code = $countryCode;
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

    public function getFullNumber()
    {
        return $this->getCountryCode() . $this->getNumber();
    }


    public function getData(): array {
        return [
            'id' => $this->getId(),
            'number' => $this->getNumber(),
            'countryCode' => $this->getCountryCode(),
            'fullNumber' => $this->getFullNumber(),
            'userId' => $this->getUserId(),
            'isActive' => $this->getIsActive(),
            'create' => $this->getCreatedAt()
        ];
    }



}
