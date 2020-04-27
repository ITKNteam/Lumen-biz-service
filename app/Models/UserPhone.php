<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserPhone extends Model
{
    protected $table = 'users_phones';
    protected $primaryKey = 'id';

    public function isActive()
    {
        return $this->is_active;
    }

    public function setHashByCode($code)
    {
        $this->hash = md5($code.'salt'.time());
    }
}
