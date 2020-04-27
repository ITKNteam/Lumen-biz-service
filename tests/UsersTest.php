<?php

use App\Models\User;
use Faker\Generator as Faker;
use Faker\Provider\Internet as FakerInternet;
use Faker\Provider\Person as FakerPerson;
use Faker\Provider\PhoneNumber as FakerPhone;
use Illuminate\Database\Eloquent\Factory;
use App\Services\ProfileService;
use App\Models\ResultDTO;


class UsersTest extends TestCase
{
    private $service;


    /**
     * UsersTest constructor.
     *
     * @param $service
     */
    public function __construct()
    {
        $this->service =  new ProfileService();
        parent::__construct();
    }



    public function testCreateUserByEmail()
    {

        $faker = new Faker();
        $faker->addProvider(new FakerInternet($faker));
        $faker->addProvider(new FakerPerson($faker));
        $email = $faker->email;
        $isEmail = true;


        $retOk = $this->service->createUser($email, $isEmail, '');
        $this->assertTrue($retOk->isSuccess(), 'Create by email - '. $retOk->getResult()['message']);

        $retFail = $this->service->createUser($email, $isEmail, '');
        $this->assertFalse($retFail->isSuccess(), 'Re create - '. $retOk->getResult()['message']);

        $retActivate = $this->service->activateEmail($retOk->getResult()['data']['hash']);
        $this->assertTrue($retActivate->isSuccess(), 'Activate email -'. $retOk->getResult()['message']);

        /**
         * return hash
         */
        echo json_encode($retOk->getResult()['data']).PHP_EOL;

    }

    public function testCreateUserByPhone()
    {

        $faker = new Faker();
        $faker->addProvider(new FakerPhone($faker));
        $phoneNumber = $faker->e164PhoneNumber;

        $phone = substr($phoneNumber, -10);
        $code = substr($phoneNumber, 0, strlen($phoneNumber)-10);

        $isEmail = false;
        $retOk = $this->service->createUser($phone, $isEmail, $code);
        $this->assertTrue($retOk->isSuccess());

        $ret = $this->service->createUser($phone, $isEmail, $code);
        $this->assertFalse($ret->isSuccess());

        /**
         * return code == password
         */
        echo json_encode($retOk->getResult()['data']).PHP_EOL;

    }

}
