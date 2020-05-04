<?php

namespace App\Services;


use App\Models\ResultDTO;
use App\Models\UserEmail;
use App\Models\UserPhone;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPushToken;
use Exception;


class ProfileService {
    /**
     * @param string $login
     * @param bool $isEmail
     * @param string $phoneCountryCode
     *
     * @return ResultDTO
     */
    public function createUser(
        string $login,
        bool $isEmail,
        string $phoneCountryCode
    ): ResultDTO {
        return $isEmail
            ? $this->bindEmailToUser($login, 0)
            : $this->bindPhoneToUser($login, $phoneCountryCode, 0);
    }


    /**
     * @param int $id
     *
     * @return ResultDTO
     */
    public function getUser(int $id) {
        $user = User::find($id);
        if ($user === null) {
            return new ResultDTO(ResultDTO::FAIL, 'Пользователь не найден', [],
                404);
        }

        $userData = $user->getData();

        $profileData = (new UserProfile)->getData();
        if ($user->profile()->first() != null) {
            $profileData = $user->profile()->first()->getData();
        }


        $profileData['phone'] = [];
        $userPhoneData = $user->phone()->first();
        if ($userPhoneData != null) {
            $profileData['phone'] = $userPhoneData->getData();
        }
        $profileData['email'] = [];
        $userEmailData = $user->email()->first();
        if ($userEmailData != null) {
            $profileData['email'] = $userEmailData->getData();
        }

        $pushToken = $user->pushToken()->first();
        if ($pushToken != null) {
            if ($pushToken->isAndroid()) {
                $profileData['android_token'] = $pushToken->pushToken;
            } else {
                $profileData['ios_token'] = $pushToken->pushToken;
            }
        }

        $profileData['isActive'] = $profileData['phone']['isActive'] ??
            $profileData['email']['isActive'];

        $data = $userData + $profileData;

        return new ResultDTO(ResultDTO::OK, 'Пользователь', $data);
    }

    /**
     * @param $userId
     * @param $data
     *
     * @return ResultDTO
     */
    public function updateUser($id, $params) {
        $user = User::find($id);
        if ($user === null) {
            return new ResultDTO(ResultDTO::FAIL, 'Пользователь не найден', [],
                404);
        }

        $userData = $user->getData();

        $profile = $user->profile()->first();
        if ($profile == null) {
            $profile = new UserProfile();
            $profile->user_id = $id;
        }

        $profile->setName($params['name'] ?? $profile->getName());
        $profile->setGender($params['gender'] ?? $profile->getGender());
        $profile->setBirthDate($params['birthDate'] ??
            $profile->getBirthDate());
        $profile->setCountryId($params['countryId'] ??
            $profile->getCountryId());
        $profile->setRegionId($params['regionId'] ?? $profile->getRegionId());
        $profile->setCityId($params['cityId'] ?? $profile->getCityId());
        $profile->setTimezone($params['timezone'] ?? $profile->getTimezone());
        $profile->setCurrencyId($params['currencyId'] ??
            $profile->getCurrencyId());
        $profile->setLanguageId($params['languageId'] ??
            $profile->getLanguageId());
        $profile->setRideCount($params['rideCount'] ??
            $profile->getRideCount());
        $profile->setRideLength($params['rideLength'] ??
            $profile->getRideLength());
        $profile->setTotalCalories($params['totalCalories'] ??
            $profile->getTotalCalories());

        if (!$profile->save()) {
            return new ResultDTO(ResultDTO::FAIL,
                'Проблемы обновления данных пользователя', [
                    'userId' => $id
                ]);
        }
        $data = $userData + $profile->getData();
        return new ResultDTO(ResultDTO::OK,
            'Пользователь  обновлен', $data);
    }


    /**
     * @param array $params
     *
     * @return ResultDTO
     */
    public function findUserLoginPassword(array $params) {

        $login = $params['login'];
        $password = $params['password'];
        $loginIs = $params['loginIs'];

        $userId = null;
        if ($loginIs === 'phone') {
            $phoneCountryCode = $params['phoneCountryCode'];

            $userPhone = UserPhone::where('number', $login)
                ->where('country_code', $phoneCountryCode)->first();

            if ($userPhone != null) {
                $userId = $userPhone->getUserId();
            }

        } else {
            $userEmail = UserEmail::where('email', $login)->first();
            if ($userEmail != null) {
                $userId = $userEmail->getUserId();
            }
        }

        if (!$userId) {
            return new ResultDTO(ResultDTO::FAIL,
                'Пользователь не найден', [], 404);
        }

        $user = User::find($userId);
        if (!$user) {
            return new ResultDTO(ResultDTO::FAIL, 'Пользователь не найден', [],
                404);
        }


        if (password_verify($password, $user->getPassword())) {
            return new ResultDTO(ResultDTO::OK,
                'Пользователь успешно вошел в систему', [
                    'userId' => $userId,
                    'confirmTerm' => $user->getIsConfirmTerm()
                ]);

        } else {
            return new ResultDTO(ResultDTO::FAIL, 'Неверный пароль', []);
        }

    }


    /**
     * @param array $params
     *
     * @return ResultDTO
     */
    public function setPasswordApplyByPhone(array $params) {
        $phoneNumber = $params['phone'];
        $password = $params['password'];
        $rePassword = $params['rePassword'];
        $code = $params['code'];

        if ($password !== $rePassword) {
            return new ResultDTO(ResultDTO::FAIL, 'Пароли не совпадают', [],
                400);
        }

        $phone = UserPhone::where('hash',
            $this->getHashPhone($phoneNumber, $code))->first();

        if (!$phone) {
            return new ResultDTO(ResultDTO::FAIL, 'Телефон не найден', [], 404);
        }

        $user = User::find($phone->getUserId());
        if (!$user) {
            return new ResultDTO(ResultDTO::FAIL, 'Пользователь не найден', [],
                404);
        }
        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
        $phone->setIsActive(User::IS_ACTIVE);
        $phone->setHash('');

        if (!($user->save() && $phone->save())) {
            return new ResultDTO(ResultDTO::FAIL, 'Ошибка сохранения данных',
                [], 500);
        }

        return new ResultDTO(ResultDTO::OK,
            'Пароль установлен', [
                'userId' => $user->getId()
            ]);
    }


    /**
     * @param array $params
     *
     * @return ResultDTO
     */
    public function setPasswordApplyByEmail(array $params): ResultDTO {
        $password = $params['password'];
        $rePassword = $params['password'];
        $hash = $params['hash'];

        if ($password !== $rePassword) {
            return new ResultDTO(ResultDTO::FAIL, 'Пароли не совпадают', [],
                400);
        }

        $resActivateEmail = $this->activateEmail($hash);
        if ($resActivateEmail->isSuccess()) {
            $userId = $resActivateEmail->getResult()['data']['userId'];
        } else {
            return $resActivateEmail;
        }

        $user = User::find($userId);
        if (!$user) {
            return new ResultDTO(ResultDTO::FAIL, 'Пользователь не найден', [],
                404);
        }

        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));

        if (!$user->save()) {
            return new ResultDTO(ResultDTO::FAIL, 'Ошибка сохранения данных',
                [],
                500);
        }

        return new ResultDTO(ResultDTO::OK,
            'Пользователь успешно поддтвердил email', [
                'userId' => $userId
            ]);
    }


    /**
     * @param int $userId
     *
     * @return ResultDTO
     */
    public function mobileConfirmTerm(int $userId) {
        $user = User::find($userId);
        $user->setIsConfirmTerm(User::IS_ACTIVE);
        if (!($user->save())) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка сохранения', []);
        }
        return new ResultDTO(ResultDTO::OK,
            'Пользователь успешно поддтвердл email', []);
    }


    /**
     * @param string $phone
     *
     * @return ResultDTO
     */
    public function deleteUserByPhone(string $phone) {
        try {
            $userPhone = UserPhone::where('phone', $phone)->firstOrFail();

            User::find($userPhone->getUserId())->delete();

            return new ResultDTO(ResultDTO::OK, 'Пользователь успешно удален');
        } catch (\Exception $e) {
            return new ResultDTO(ResultDTO::FAIL,
                $e->getMessage(), [], 500);
        }

    }

    /**
     * @param array $params
     *
     * @return ResultDTO
     */
    public function savePushToken(array $params) {

        $userId = $params['user_id'];
        $pushToken = $params['push_token'];
        $deviceId = $params['device_id'] ?? 'no id';
        $deviceBrand = $params['device_brand'] ?? 'no brand';
        $isAndroid = $params['is_android'] == 'true' ? true : false;

        $userPushToken = UserPushToken::where('user_id', $userId)
            ->where('device_id', $deviceId)
            ->where('device_brand', $deviceBrand)->first();

        if ($userPushToken == null) {
            $userPushToken = new UserPushToken();
            $userPushToken->setUserId($userId);
        }

        $userPushToken->setPushToken($pushToken);
        $userPushToken->setDeviceId($deviceId);
        $userPushToken->setDeviceBrand($deviceBrand);
        $userPushToken->setIsAndroid($isAndroid);

        if (!$userPushToken->save()) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка сохранения пуштокена', []);
        }


        return new ResultDTO(ResultDTO::OK,
            'Пользователь успешно установил пуштокен');
    }


    /**
     * @param string $email
     *
     * @return ResultDTO
     */
    public function generateEmailHash(string $email) {
        $userEmail = UserEmail::where('email', $email)->first();
        if ($userEmail === null) {
            return new ResultDTO(ResultDTO::FAIL,
                'Email не найден', []);
        }

        $userEmail->setHashByEmail($email);
        if (!$userEmail->save()) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка сохранения нового hash', []);
        }

        return new ResultDTO(ResultDTO::OK,
            'успешно', ['hash' => $userEmail->getHash()]);
    }


    /**
     * @param string $phone
     * @param string $countryCode
     *
     * @return ResultDTO
     */
    public function generatePhoneHash(string $phone, string $countryCode) {
        $userPhone = UserPhone::where('email', $phone)
            ->where('country_code', $countryCode)->first();
        if ($userPhone === null) {
            return new ResultDTO(ResultDTO::FAIL,
                'Телефон не найден', []);
        }

        $smsCode = rand(1000, 9999);
        $userPhone->setHashByCode($smsCode);

        $user = User::find($userPhone->getUserId());
        $user->setPassword(password_hash($smsCode, PASSWORD_BCRYPT));

        if (!$userPhone->save() && !$user->save()) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка сохранения нового кода', []);
        }

        return new ResultDTO(ResultDTO::OK,
            'успешно', ['code' => $smsCode]);
    }


    /**
     * @param $phoneNumber
     * @param $countryCode
     * @return ResultDTO
     * @throws Exception
     */
    public function bindPhoneToUser($phoneNumber, $countryCode): ResultDTO {

        $phone = UserPhone::where('number', $phoneNumber)
            ->where('country_code', $countryCode)->first();

        $userId = null;

        if ($phone) {
            if ($phone->getIsActive() === User::IS_ACTIVE) {
                return new ResultDTO(ResultDTO::FAIL, 'Пользователь существует');
            }

            $userId = $phone->getUserId();
        } else {
            $user = new User();
            $user->setLogin($countryCode . $phoneNumber);

            if (!$user->save()) {
                return new ResultDTO(ResultDTO::FAIL,
                    'Ошибка сохранения пользователя');
            }
            $userId = $user->id;

            $phone = new UserPhone();
            $phone->setNumber($phoneNumber);
            $phone->setCountryCode($countryCode);
            $phone->setIsActive(User::IS_NOT_ACTIVE);
            $phone->setUserId($userId);
        }

        if (!$userId) {
            throw new Exception('Var userId is null');
        }

        $smsCode = rand(1000, 9999);
        $phone->hash = $this->getHashPhone($phoneNumber, $smsCode);

        if (!$phone->save()) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка сохранения телефона пользователя');
        }

        return new ResultDTO(ResultDTO::OK, 'ok phone', [
            'hash' => $smsCode,
            'userId' => $userId
        ]);
    }

    /**
     * @param string $emailStr
     * @param int $bindUserId
     *
     * @return ResultDTO
     */
    public function bindEmailToUser(
        string $emailStr,
        int $bindUserId
    ): ResultDTO {
        $email = UserEmail::where('email', $emailStr)->first();

        $userId = $bindUserId ?? false;

        if ($email && ($email->isActive() === User::IS_ACTIVE)) {
            return new ResultDTO(ResultDTO::FAIL,
                'Пользователь существует');
        }

        if (!$userId) {
            $user = new User();
            $user->setLogin($emailStr);
            if (!$user->save()) {
                return new ResultDTO(ResultDTO::FAIL,
                    'Ошибка сохранения пользователя');
            }
            $userId = $user->getId();
        }

        $email = new UserEmail();
        $email->setEmail($emailStr);
        $email->setIsActive(User::IS_NOT_ACTIVE);
        $email->setUserId($userId);

        $email->setHashByEmail($emailStr);
        if (!$email->save()) {
            return new ResultDTO(ResultDTO::FAIL, 'Ошибка сохранения email');
        }

        return new ResultDTO(ResultDTO::OK,
            'Пользователь создан и email привязан', [
                'hash' => $email->getHash(),
                'userId' => $userId
            ]);
    }

    /**
     * @param string $hash
     *
     * @return ResultDTO
     */
    public function activateEmail(string $hash): ResultDTO {
        $email = UserEmail::where('hash', $hash)->first();

        if (!$email) {
            return new ResultDTO(ResultDTO::FAIL, 'Ошибка, email не найден', [], 404);
        }

        $userId = $email->user_id;

        if (!$this->disableUserEmail($userId)->isSuccess()) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка, обновления статусов email', [], 500);
        }

        $email->is_active = User::IS_ACTIVE;
        $email->hash = '';
        if (!$email->save()) {
            return new ResultDTO(ResultDTO::FAIL, 'Ошибка, обновления email', [], 500);
        }

        UserEmail::where('user_id', $userId)
            ->where('is_active', User::IS_NOT_ACTIVE)->delete();

        return new ResultDTO(ResultDTO::OK, 'Пользователь активирован',
            ['userId' => $userId]);
    }

    /**
     * @param int $userId
     *
     * @return ResultDTO
     */
    public function disableUserEmail(int $userId): ResultDTO {
        $emails = UserEmail::where('user_id', $userId)->get();
        foreach ($emails as $email) {
            $email->is_active = User::IS_NOT_ACTIVE;
            if (!$email->save()) {
                return new ResultDTO(ResultDTO::FAIL,
                    'Ошибка, обновления email', [], 500);
            }
        }
        return new ResultDTO(ResultDTO::OK, 'Обновления email удачно');
    }

    /**
     * @param int $userId
     *
     * @return ResultDTO
     */
    public function disableUserPhone(int $userId) {
        $phones = UserPhone::where('user_id', $userId)->get();
        foreach ($phones as $phone) {
            $phone->setActive(User::IS_NOT_ACTIVE);
            if (!$phone->save()) {
                return new ResultDTO(ResultDTO::FAIL,
                    'Ошибка, телефон не обновлен', [], 500);
            }
        }
        return new ResultDTO(ResultDTO::OK,
            'Ошибка, телефон обновлен');
    }


    /**
     * @param string $phoneNumber
     * @param string $code
     *
     * @return ResultDTO
     */
    public function activatePhone(string $phoneNumber, string $code) {
        $phone = UserPhone::where('hash',
            $this->getHashPhone($phoneNumber, $code))->first();

        if (!$phone) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка, телефон не найден', [], 404);
        }

        $userId = $phone->getUserId();
        $user = User::find($userId);
        if (!$user) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка, не найден пользователь', [], 404);
        }

        if (!$this->disableUserPhone($userId)->isSuccess()) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка, обновления статусов телефон');
        }


        $phone->setActive(User::IS_ACTIVE);
        $phone->setHash('');
        if (!$phone->save()) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка сохранения данных');
        }
        UserPhone::where('user_id', $userId)
            ->where('is_active', User::IS_NOT_ACTIVE)->delete();

        return new ResultDTO(ResultDTO::OK,
            'Успешное сохранения данных', ['userId' => $userId]);
    }


    /**
     * @param $phoneNumber
     * @param $code
     *
     * @return string
     */
    private function getHashPhone($phoneNumber, $code) {
        return md5($phoneNumber . 'salt' . $code);
    }

    /**
     * @param string $countryCode
     * @param string $phoneNumber
     * @param string $code
     * @param string $name
     *
     * @return ResultDTO
     */
    public function mobileRegistration(
        string $countryCode,
        string $phoneNumber,
        string $code,
        string $name
    ) {
        $phone = UserPhone::where('hash',
            $this->getHashPhone($phoneNumber, $code))
            ->where('country_code', $countryCode)->first();

        if (!$phone) {
            return new ResultDTO(ResultDTO::FAIL, 'Ошибка, не найден телефон');
        }
        $userId = $phone->user_id;
        $user = User::where('id', $userId)->first();
        if (!$user) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка, не найден пользователь');
        }
        $user->password = password_hash($code, PASSWORD_BCRYPT);
        $phone->is_active = User::IS_ACTIVE;
        $phone->hash = null;

        $profile = UserProfile::where('user_id', $userId)->first();

        if (!$profile) {
            $profile = new UserProfile();
            $profile->user_id = $userId;
        }

        $profile->name = $name;

        if (!($user->save() && $phone->save() && $profile->save())) {
            return new ResultDTO(ResultDTO::FAIL, 'Ошибка, сохраненеия данных');
        }

        return new ResultDTO(ResultDTO::OK, 'Пользователь создан', [
            'userId' => $user->id
        ]);

    }
}
