<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserEmail extends Model
{
    protected $table = 'users_emails';
    protected $primaryKey = 'id';


    public function isActive()
    {
        return $this->is_active;
    }

    public function setHashByEmail($email)
    {
        $this->hash = md5($email.'salt'.time());
    }
}
