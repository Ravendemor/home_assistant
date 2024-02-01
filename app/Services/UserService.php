<?php

namespace App\Services;

use App\DataTransferObjects\ServiceResponse;
use App\Models\User;
use Google\Client;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserService extends Service
{
    /**
     * Авторизует или регистрирует пользователя по email-адресу и паролю
     *
     * @param array $userData
     * @return ServiceResponse
     */
    public function authorizeByPassword(array $userData): ServiceResponse
    {
        if($user = User::whereEmail($userData['email'])->first()) {
            if(!Hash::check($userData['password'], $user->password)) {
                return $this->makeErrorResponse('Некорректный пароль');
            }
        } else {
            try {
                $user = User::query()->create($userData);
            } catch(UniqueConstraintViolationException) {
                return $this->makeErrorResponse('Адрес электронной почты уже используется');
            }
        }

        return $this->makeServiceResponse(['token' => $user->createToken('user_token')->plainTextToken]);
    }

    /**
     * Авторизует или регистрирует пользователя по данным из аккаунта google
     *
     * @param array $userData
     * @return ServiceResponse
     */
    public function authorizeByGoogle(array $userData): ServiceResponse
    {
        $token = $userData['token'];

        if (!$googleUser = $this->checkGoogleUser($token)) {
            return $this->makeErrorResponse();
        }
        if(!isset($googleUser['email'])) {
            return $this->makeErrorResponse('Необходимо дать доступ к электронной почте');
        }

        try {
            $user = User::query()->firstOrCreate(['email' => $googleUser['email']]);
        } catch(UniqueConstraintViolationException) {
            return $this->makeErrorResponse('Адрес электронной почты уже используется');
        }

        return $this->makeServiceResponse(['token' => $user->createToken('user_token')->plainTextToken]);
    }

    /**
     * Изменяет поля профиля пользователя - login, email, password
     *
     * @param array $userData
     * @return ServiceResponse
     */
    public function editProfile(array $userData): ServiceResponse
    {
        try {
            $this->getCurrentUser()->update($this->getCurrentUser()->getFillableArrayValues($userData));
        } catch(Throwable) {
            return $this->makeErrorResponse('Не удалось изменить профиль');
        }

        return $this->makeServiceResponse();
    }

    public function logout(): ServiceResponse
    {
        $this->getCurrentUser()->tokens()->delete();
        return $this->makeServiceResponse();
    }

    private function checkGoogleUser(string $token): array|bool
    {
        return (new Client(['client_id' => env('GOOGLE_CLIENT_ID')]))->verifyIdToken($token);
    }

    public function getCurrentUser(): User
    {
        return app('request')->user();
    }
}
