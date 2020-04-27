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


    protected $table = 'users';
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

    /*
    protected $attributes = [
        'id' => 'id',
        'login' => 'login',
        'password' => 'password',
        'is_android' => 'isAndroid',
        'is_confirm_term' => 'isConfirmTerm',
        'balance' => 'balance',
        'tariff_id' => 'tariffId',
        'currency_id' => 'currencyId',
        'payment_system_status' => 'paymentSystemStatus',
        'created_at' => 'createdAt',
        'updated_at' => 'updatedAt',
    ];
*/

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }


}
