<?php


namespace App\Console\Commands;


use App\Models\User;
use App\Models\UserEmail;
use App\Services\ProfileService;
use Illuminate\Console\Command;

use Faker\Generator as Faker;
use Faker\Provider\Internet as FakerInternet;
use Faker\Provider\Person as FakerPerson;
use Faker\Provider\PhoneNumber as FakerPhone;

class ProfileTestCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "profile:test";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "profile";

    private $service;

    /**
     * ProfileTestCommand constructor.
     *
     * @param $service
     */
    public function __construct(ProfileService $service)
    {
        $this->service = $service;
        parent::__construct();
    }


    public function handle()
    {


        $faker = new Faker();
        $faker->addProvider(new FakerInternet($faker));
        $faker->addProvider(new FakerPerson($faker));
        $faker->addProvider(new FakerPhone($faker));
        $phoneNumber = $faker->e164PhoneNumber;

        $phone = substr($phoneNumber, -10);
        $code = substr($phoneNumber, 0, strlen($phoneNumber)-10);


        $ret = $this->service->createUser($faker->email, true, '');
        var_dump($ret->isSuccess());
        die();
        $ins = false;
        $emailStr = 'kxxb@yandex.ru';
        $email = UserEmail::where('email', $emailStr)->first();

        if ($email->isActive() == User::IS_ACTIVE) {
            echo 'active';
        }
            //->take(1)
            //->get();

        if ($ins) {
            $user1 = new User();
            $user1->login = 'user3';
            //$user1->profile->name = 'user2 name';
            $user1->save();
            var_dump($user1->id);

        }
        $user =  User::all();
     //   $user->login = 'test';
     //   $user->is_android = false;
      //  $user->is_confirm_term = 0;


        var_dump($user->pro);


    }
}
