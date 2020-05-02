<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'qlick.biz_users_profile';
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->last_name = $lastName;
    }

    /**
     * @return mixed
     */
    public function getPatronymic()
    {
        return $this->patronymic;
    }

    /**
     * @param mixed $patronymic
     */
    public function setPatronymic($patronymic): void
    {
        $this->patronymic = $patronymic;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getBirthDate()
    {
        return $this->birth_date;
    }

    /**
     * @param mixed $birthDate
     */
    public function setBirthDate($birthDate): void
    {
        $this->birth_date = $birthDate;
    }

    /**
     * @return mixed
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * @param mixed $countryId
     */
    public function setCountryId($countryId): void
    {
        $this->country_id = $countryId;
    }

    /**
     * @return mixed
     */
    public function getRegionId()
    {
        return $this->region_id;
    }

    /**
     * @param mixed $regionId
     */
    public function setRegionId($regionId): void
    {
        $this->region_id = $regionId;
    }

    /**
     * @return mixed
     */
    public function getCityId()
    {
        return $this->city_id;
    }

    /**
     * @param mixed $cityId
     */
    public function setCityId($cityId): void
    {
        $this->city_id = $cityId;
    }

    /**
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param mixed $timezone
     */
    public function setTimezone($timezone): void
    {
        $this->timezone = $timezone;
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
    public function getLanguageId()
    {
        return $this->language_id;
    }

    /**
     * @param mixed $languageId
     */
    public function setLanguageId($languageId): void
    {
        $this->language_id = $languageId;
    }

    /**
     * @return mixed
     */
    public function getRideCount()
    {
        return $this->ride_count;
    }

    /**
     * @param mixed $rideCount
     */
    public function setRideCount($rideCount): void
    {
        $this->ride_count = $rideCount;
    }

    /**
     * @return mixed
     */
    public function getRideLength()
    {
        return $this->ride_length;
    }

    /**
     * @param mixed $rideLength
     */
    public function setRideLength($rideLength): void
    {
        $this->ride_length = $rideLength;
    }

    /**
     * @return mixed
     */
    public function getTotalCalories()
    {
        return $this->total_calories;
    }

    /**
     * @param mixed $totalCalories
     */
    public function setTotalCalories($totalCalories): void
    {
        $this->total_calories = $totalCalories;
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




    public function getData()
    {
        return [
            'name' => $this->getName(),
            'lastName' => $this->getLastName(),
            'gender' => $this->getGender(),
            'birthDate' => $this->getBirthDate(),
            'countryId' => $this->getCountryId(),
            'regionId' => $this->getRegionId(),
            'cityId' => $this->getCityId(),
            'timezone' => $this->getTimezone(),
            'currencyId' => $this->getCurrencyId(),
            'createTs' => $this->getCreatedAt(),
            'languageId' => $this->getLanguageId(),
            'rideCount' => $this->getRideCount(),
            'rideLength' => $this->getRideLength(),
            'totalCalories' => $this->getTotalCalories()
        ];
    }

}
