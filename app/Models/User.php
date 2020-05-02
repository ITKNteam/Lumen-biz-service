<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class User extends Model
{
    /**
     *
     */
    const IS_NOT_ACTIVE = 0;

    /**
     *
     */
    const IS_ACTIVE = 1;


    protected $table = 'qlick.biz_users';
    protected $primaryKey = 'id';

    public function phone()
    {
        return $this->hasOne('App\Models\UserPhone');
    }

    public function profile()
    {
        return $this->hasOne('App\Models\UserProfile');
    }

    public function email()
    {
        return $this->hasOne('App\Models\UserEmail');
    }

    public function pushToken()
    {
        return $this->hasOne('App\Models\UserPushToken');
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login): void
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
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
    public function getIsConfirmTerm()
    {
        return $this->is_confirm_term;
    }

    /**
     * @param mixed $isConfirmTerm
     */
    public function setIsConfirmTerm($isConfirmTerm): void
    {
        $this->is_confirm_term = $isConfirmTerm;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param mixed $balance
     */
    public function setBalance($balance): void
    {
        $this->balance = $balance;
    }

    /**
     * @return mixed
     */
    public function getTariffId()
    {
        return $this->tariff_id;
    }

    /**
     * @param mixed $tariffId
     */
    public function setTariffId($tariffId): void
    {
        $this->tariff_id = $tariffId;
    }

    /**
     * @return mixed
     */
    public function getCurrencyId()
    {
        return $this->currency_id;
    }

    /**
     * @param mixed $currencyId
     */
    public function setCurrencyId($currencyId): void
    {
        $this->currency_id = $currencyId;
    }

    /**
     * @return mixed
     */
    public function getPaymentSystemStatus()
    {
        return $this->payment_system_status;
    }

    /**
     * @param mixed $paymentSystemStatus
     */
    public function setPaymentSystemStatus($paymentSystemStatus): void
    {
        $this->payment_system_status = $paymentSystemStatus;
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

    public function getData(): array
    {
        return [
            'id'           => $this->getId(),
            'registerAt'   => $this->getCreatedAt(),
            'lastActivity' => $this->getUpdatedAt(),
            'tariff_id'    => $this->getTariffId(),
            'balance'      => $this->getBalance(),
            'robokassaStatus'  => $this->getPaymentSystemStatus()
        ];
    }




    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }


}
