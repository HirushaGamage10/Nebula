<?php
/**
 * Authentication Facade Stubs
 * These provide IDE support for the auth() helper function
 */

namespace Illuminate\Support\Facades;

/**
 * @method static \Illuminate\Contracts\Auth\Authenticatable|null user()
 * @method static mixed id()
 * @method static bool check()
 * @method static bool guest()
 * @method static \Illuminate\Contracts\Auth\Authenticatable authenticate()
 * @method static bool hasUser()
 * @method static bool validate(array $credentials = [])
 * @method static void setUser(\Illuminate\Contracts\Auth\Authenticatable $user)
 * @method static void forgetUser()
 * @method static string getName()
 * @method static string getPassword()
 * @method static \Illuminate\Contracts\Auth\Guard|null attempt(array $credentials = [], bool $remember = false)
 * @method static void login(\Illuminate\Contracts\Auth\Authenticatable $user, bool $remember = false)
 * @method static \Illuminate\Contracts\Auth\Authenticatable|false loginUsingId(mixed $id, bool $remember = false)
 * @method static bool once(array $credentials = [])
 * @method static bool onceUsingId(mixed $id)
 * @method static void logout()
 * @method static \Illuminate\Contracts\Auth\Authenticatable|null user()
 */
class Auth {}

/**
 * Application Bootstrap Code
 */
if (!function_exists('auth')) {
    /**
     * Get the available auth instance.
     *
     * @param  string|null  $guard
     * @return \Illuminate\Contracts\Auth\Factory|\Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    function auth($guard = null) {}
}
