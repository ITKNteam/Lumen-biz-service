<?php


namespace App\Http\Controllers;

use App\Services\ProfileService;
use Laravel\Lumen\Http\Request;

class ProfileController extends Controller
{

    private $profileService;

    /**
     * ProfileController constructor.
     *
     * @param $profileService
     */
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index(Request $request)
    {
        return 'biz ok';
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return array
     */
    public function getUserById(Request $request, int $id)
    {
        return ['getUser'=>'ok', 'id'=>$id, 'request'=> $request->input('id')];
    }

    public function getUsers(Request $request)
    {
        return ['getUsersss'=>'ok'];
    }

    public function createUser(Request $request) {

        return $this->profileService->createUser([])->getResult();
    }
}
