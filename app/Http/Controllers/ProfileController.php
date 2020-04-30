<?php


namespace App\Http\Controllers;

use App\Models\ResultDTO;
use App\Services\ProfileService;
use Illuminate\Http\Request;

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



    public function getUsers(Request $request)
    {
        return ['getUsersss' => 'ok'];
    }

    public function createUser(Request $request)
    {
        if (!$request->has(['login', 'loginIs'])) {
            return response((new ResultDTO(0, 'fields login and loginIs requried',
                [], 400))->getResult(), 400);
        }
        if (!in_array($request->input('loginIs'), ['email', 'phone'])) {
            return response((new ResultDTO(0,
                    'Value loginIs not in values phone or email', [],
                    400))->getResult(), 400);
        }
        $login = $request->input('login');
        $isEmail = $request->input('loginIs') === 'email';
        $phoneCountryCode = $request->input('phoneCountryCode', '7');
        $result = $this->profileService->createUser($login, $isEmail,
            $phoneCountryCode);
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }

    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function getUserById(Request $request, $id)
    {

        $result = $this->profileService->getUser($id);
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }


    public function updateUser(Request $request)
    {
        if (!$request->has(['id'])) {
            return response((new ResultDTO(0, 'id si requried',
                [], 400))->getResult(), 400);
        }
        $userId = $request->input('id');
        $result = $this->profileService->updateUser($userId,  $request->input());
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }

    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function login(Request $request) {

        if (!$request->has(['login', 'password', 'loginIs', 'phoneCountryCode'])) {
            return response((new ResultDTO(0, 'fields is requried',
                [], 400))->getResult(), 400);
        }

        $result = $this->profileService->findUserLoginPassword($request->input());
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }

    }

    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function setPasswordApplyByPhone(Request $request) {

        if (!$request->has(['phone', 'password', 'rePassword', 'code'])) {
            return response((new ResultDTO(0, 'fields is requried',
                [], 400))->getResult(), 400);
        }

        $result = $this->profileService->setPasswordApplyByPhone($request->input());
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }

    }

    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function setPasswordApplyByEmail(Request $request) {

        if (!$request->has(['password', 'rePassword', 'hash'])) {
            return response((new ResultDTO(0, 'fields is requried',
                [], 400))->getResult(), 400);
        }

        $result = $this->profileService->setPasswordApplyByEmail($request->input());
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }

    }

    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function deleteUserByPhone(Request $request) {

        if (!$request->has(['phone'])) {
            return response((new ResultDTO(0, 'field phone is requried',
                [], 400))->getResult(), 400);
        }

        $result = $this->profileService->deleteUserByPhone($request->input('phone'));
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }


    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function setPushToken(Request $request)
    {
        if (!$request->has(['user_id', 'push_token', 'is_android'])) {
            return response((new ResultDTO(0, 'fields is requried',
                [], 400))->getResult(), 400);
        }

        $result = $this->profileService->savePushToken($request->input());
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }




    /**
     * add not active email to user
     */
    public function addEmailToUser(Request $request)
    {

        if (!$request->has(['email', 'userId'])) {
            return response((new ResultDTO(0, 'fields is requried',
                [], 400))->getResult(), 400);
        }

        $result = $this->profileService->bindEmailToUser($request->input('email'),
            $request->input('userId'));
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }

    /**
     * Generate new email hash to reset password
     */
    public function generateEmailResetHash(Request $request)
    {
        if (!$request->has(['email'])) {
            return response((new ResultDTO(0, 'email is requried',
                [], 400))->getResult(), 400);
        }

        $result = $this->profileService->generateEmailHash($request->input('email'));
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }


    /**
     * activate email and delete old
     */
    public function activateEmail(Request $request) {
        if (!$request->has(['hash'])) {
            return response((new ResultDTO(0, 'activateEmail is requried',
                [], 400))->getResult(), 400);
        }

        $result = $this->profileService->activateEmail($request->input('hash'));
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }

    /**
     * create not active phone to user
     */
    public function addPhoneToUser(Request $request){
        if (!$request->has(['phone', 'userId', 'country_code'])) {
            return response((new ResultDTO(0, 'fields is requried',
                [], 400))->getResult(), 400);
        }

        $result = $this->profileService->bindPhoneToUser($request->input('phone'),
            $request->input('userId'), $request->input('country_code'));
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }

    /**
     * reset hash for phone
     */
    public function generatePhoneResetHash(Request $request) {
        if (!$request->has(['phone', 'country_code'])) {
            return response((new ResultDTO(0, 'fields is requried',
                [], 400))->getResult(), 400);
        }
        $result = $this->profileService->generatePhoneHash($request->input('phone'),
            $request->input('country_code'));
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }

    /**
     * activate phone number and delete old numbers
     */
    public function activatePhone(Request $request) {
        if (!$request->has(['phone', 'code'])) {
            return response((new ResultDTO(0, 'fields is requried',
                [], 400))->getResult(), 400);
        }

        $phone = str_replace('+', '', $phone);
        $phone = str_replace('-', '', $phone);
        $phone = str_replace('–', '', $phone);
        $phone = str_replace(' ', '', $phone);
        $phone = str_replace('(', '', $phone);
        $phone = str_replace(')', '', $phone);
        $resPhone = substr($phone, -10);
        $code = $this->getOption('code');
        $code = str_replace('-', '', $code);
        $code = str_replace('–', '', $code);
        $code = str_replace(' ', '', $code);

        $result = $this->profileService->activatePhone($resPhone,
            $code);
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }




    ////mobile
    //

    /**
     * Create empty user and generate confirmation code|hash
     *  Next step - mobileRegistartionUser, if start from mobile application
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function mobileCreateUser(Request $request)
    {
        if (!$request->has(['phone', 'phoneCountryCode'])) {
            return response((new ResultDTO(0, 'fields is requried',
                [], 400))->getResult(), 400);
        }

        $isEmail = false;
        $result = $this->profileService->createUser($request->input('phone'), $isEmail,
            $request->input('phoneCountryCode', '7'));
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }

    /**
     * Registration - from mobile application
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function mobileRegistartionUser(Request $request)
    {
        if (!$request->has(['phone', 'phoneCountryCode', 'code','name'])) {
            return response((new ResultDTO(0, 'fields is requried',
                [], 400))->getResult(), 400);
        }

        $countryCode = $request->input('phoneCountryCode', '7');
        $phoneNumber = $request->input('phone');
        $code = $request->input('code');
        $name = $request->input('name');


        $result = $this->profileService->mobileRegistration($countryCode,
            $phoneNumber, $code, $name);
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }

    }

    public function getSmsCode(Request $request)
    {

        if (!$request->has(['phone', 'phoneCountryCode'])) {
            return response((new ResultDTO(0, 'fields is requried',
                [], 400))->getResult(), 400);
        }

        $countryCode = $request->input('phoneCountryCode', '7');
        $phone = $request->input('phone');

        $result = $this->profileService->generatePhoneHash($phone, $countryCode);
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }


    /**
     * confirm term of use
     */
    public function mobileConfirmTerm(Request $request)
    {
        if (!$request->has(['userId'])) {
            return response((new ResultDTO(0, 'fields is requried',
                [], 400))->getResult(), 400);
        }

        $result = $this->profileService->mobileConfirmTerm($request->input('userId'));
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }


    public function mobileLogin(Request $request)
    {

        if (!$request->has(['phone', 'code', 'phoneCountryCode'])) {
            return response((new ResultDTO(0, 'fields is requried',
                [], 400))->getResult(), 400);
        }

        $params = [
            'login' => $request->input('phone'),
            'password' => $request->input('code'),
            'loginIs' => 'phone',
            'phoneCountryCode' => $request->input('phoneCountryCode', 7)
        ];


        $result = $this->profileService->findUserLoginPassword($params);
        if ($result->isSuccess()){
            return $result->getResult();
        } else {
            return response($result->getResult(), $result->getResult()['code']?? 500);
        }
    }

}
