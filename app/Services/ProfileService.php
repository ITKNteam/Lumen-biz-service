<?php


namespace App\Services;


use App\Models\ResultDTO;
use App\Models\UserEmail;
use App\Models\UserPhone;
use App\Models\User;
use App\Models\UserProfile;


class ProfileService
{
    /**
     * @param string $login
     * @param bool   $isEmail
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
     * @param $login
     * @param $phoneCountryCode
     * @param $bindUserId
     *
     * @return ResultDTO
     */
    private function bindPhoneToUser($phoneNumber, $countryCode, $bindUserId)
    {

        $phone = UserPhone::where('number', $phoneNumber)
            ->where('country_code', $countryCode)->first();
        $userId = $bindUserId ?? false;

        if ($phone) {
            if ($phone->isActive() === User::IS_ACTIVE) {
                return new ResultDTO(ResultDTO::FAIL,
                    'Пользователь существует');
            } else {
                return new ResultDTO(ResultDTO::FAIL,
                    'Пользователь существует но телефон не поддвержден');
            }

            if (!$userId) {
                $userId = $phone->user_id;
            }

        } else {
            if ($userId === 0) {
                $user = new User();
                $user->login = $countryCode . $phoneNumber;

                if (!$user->save()) {
                    return new ResultDTO(ResultDTO::FAIL,
                        'Ошибка сохранения пользователя');
                }

                $userId = $user->id;
            }

            $phone = new UserPhone();
            $phone->number = $phoneNumber;
            $phone->country_code = $countryCode;
            $phone->is_active = User::IS_NOT_ACTIVE;
            $phone->user_id = $userId;
        }

        $smsCode = rand(1000, 9999);
        $phone->hash = $this->getHashPhone($phoneNumber, $smsCode);

        if (!$phone->save()) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка сохранения телефона пользователя');
        }

        return new ResultDTO(ResultDTO::OK, 'ok phone', [
            'hash'   => $smsCode,
            'userId' => $userId
        ]);
    }

    /**
     * @param string $emailStr
     * @param int    $bindUserId
     *
     * @return ResultDTO
     */
    private function bindEmailToUser(
        string $emailStr,
        int $bindUserId
    ): ResultDTO {
        $email = UserEmail::where('email', $emailStr)->first();

        $userId = $bindUserId ?? false;

        if ($email) {
            if ($email->isActive() == User::IS_ACTIVE) {
                return new ResultDTO(ResultDTO::FAIL,
                    'Пользователь существует');
            } else {
                return new ResultDTO(ResultDTO::FAIL,
                    'Пользователь существует но email  не подтвержден');
            }

            if (!$userId) {
                $userId = $email->user_id;
            }
        } else {
            if (!$userId) {
                $user = new User();
                $user->login = $emailStr;
                if (!$user->save()) {
                    return new ResultDTO(ResultDTO::FAIL,
                        'Ошибка сохранения пользователя');
                }
                $userId = $user->id;
            }

            $email = new UserEmail();
            $email->email = $emailStr;
            $email->is_active = User::IS_NOT_ACTIVE;
            $email->user_id = $userId;
        }

        $email->setHashByEmail($emailStr);
        if (!$email->save()) {
            return new ResultDTO(ResultDTO::FAIL, 'Ошибка сохранения email');
        }

        return new ResultDTO(ResultDTO::OK,
            'Пользователь создан и email привязан', [
                'hash'   => $email->hash,
                'userId' => $userId
            ]);

    }

    /**
     * @param string $hash
     *
     * @return ResultDTO
     */
    public function activateEmail(string $hash): ResultDTO
    {
        $email = UserEmail::where('hash', $hash)->first();

        if (!$email) {
            return new ResultDTO(ResultDTO::FAIL, 'Ошибка, email не найден');
        }

        $userId = $email->user_id;

        if (!$this->disableUserEmail($userId)->isSuccess()) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка, обновления статусов email');
        }

        $email->is_active = User::IS_ACTIVE;
        $email->hash = '';
        if (!$email->save()) {
            return new ResultDTO(ResultDTO::FAIL, 'Ошибка, обновления email');
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
    public function disableUserEmail(int $userId): ResultDTO
    {
        $emails = UserEmail::where('user_id', $userId)->get();
        foreach ($emails as $email) {
            $email->is_active = User::IS_NOT_ACTIVE;
            if (!$email->save()) {
                return new ResultDTO(ResultDTO::FAIL,
                    'Ошибка, обновления email');
            }
        }
        return new ResultDTO(ResultDTO::OK, 'Обновления email удачно');
    }


    public function activatePhone(string $phoneNumber, string $code): int
    {
        $phone = UserPhone::where('hash', $this->getHashPhone($phoneNumber, $code))->first();

        if (!$phone) {
            return new ResultDTO(ResultDTO::FAIL,
                'Ошибка, телефон не найден', [
                    'phone' => $phoneNumber,
                    'code'  => $code,
                    'hash'  => $this->getHashPhone($phoneNumber, $code)
                ]);

        }

        $userId = $phone->user_id;
        $user = \User::findFirst($userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        $this->disableUserPhone($userId);

        $phone->setActive(\User::IS_ACTIVE);
        $phone->setHash('');
        if (!$phone->save()) {
            throw new PhoneNotSaveException();
        }

        $oldPhones = \UserPhone::find([
            'userId = :userId: AND isActive = :isActive:',
            'bind' => [
                'userId'   => $userId,
                'isActive' => 'false'
            ]
        ]);

        $oldPhones->delete();

        return $userId;
    }


    /**
     * @param $phoneNumber
     * @param $code
     *
     * @return string
     */
    private function getHashPhone($phoneNumber, $code)
    {
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

    /**
     * @param int $userId
     *
     * @return array
     * @throws UserNotSaveException
     */
    public function mobileConfirmTerm(int $userId)
    {
        $user = \User::findFirst($userId);
        $user->setIsConfirmTerm(\User::IS_ACTIVE);
        if (!($user->save())) {
            throw new UserNotSaveException();
        }
        return ['res' => 1, 'message' => 'Confirm', 'data' => ''];
    }

    /**
     * @param string $phoneNumber
     * @param string $password
     * @param string $rePassword
     * @param string $code
     *
     * @return array
     * @throws PasswordsMismatchException
     * @throws PhoneNotFoundException
     * @throws UserNotFoundException
     * @throws UserNotSaveException
     */
    public function setPasswordApplyByPhone(
        string $phoneNumber,
        string $password,
        string $rePassword,
        string $code
    ): array {
        if ($password !== $rePassword) {
            throw new PasswordsMismatchException();
        }

        $phone = \UserPhone::findFirst([
            'hash = :hash:',
            'bind' => [
                'hash' => $this->getHashPhone($phoneNumber, $code)
            ]
        ]);

        if (!$phone) {
            throw new  PhoneNotFoundException();
        }

        $user = \User::findFirst($phone->getUserId());
        if (!$user) {
            throw new UserNotFoundException();
        }
        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
        $phone->setActive(\User::IS_ACTIVE);
        $phone->setHash('');

        if (!($user->save() && $phone->save())) {
            throw new UserNotSaveException();
        }

        return [
            'userId' => $user->getId()
        ];
    }
}
