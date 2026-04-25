<?php

namespace App\Http\Controllers\Web\Authentication;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use App\Models\Utility;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $companyUser = User::find($user->created_by);
        }

        if ($user && $user->is_disable == 0 && !in_array($user->type, ['company', 'super admin'])) {
            return redirect()->route('auth.login')->with('status', __('Your Account is disabled, please contact your Administrator.'));
        }

        if (
            ($user && $user->is_enable_login == 0) ||
            (isset($companyUser) && $companyUser && $companyUser->is_enable_login == 0)
            && $user->type != 'super admin'
        ) {
            return redirect()->route('auth.login')->with('status', __('Your Account is disabled, please contact your Administrator.'));
        }

        if (!$user) {
            // do {
            //     $code = rand(100000, 999999);
            // } while (User::where('referral_code', $code)->exists());

            // $user = User::create([
            //     'name' => $googleUser->getName(),
            //     'email' => $googleUser->getEmail(),
            //     'password' => bcrypt(Str::random(16)),
            //     'type' => 'company',
            //     'referral_code' => $code,
            //     'created_by' => 1,
            //     'lang' => Utility::getValByName('default_language'),
            //     'email_verified_at' => now(),
            //     'avatar' => $googleUser->getAvatar()
            // ]);

            // $role_r = \Spatie\Permission\Models\Role::findByName('company');
            // $user->assignRole($role_r);

            // $user->userDefaultData($user->id);
            // $user->userDefaultDataRegister($user->id);
            // $user->userDefaultBankAccount($user->id);
            // Utility::chartOfAccountTypeData($user->id);
            // Utility::chartOfAccountData1($user->id);
            // \App\Models\GenerateOfferLetter::defaultOfferLetterRegister($user->id);
            // \App\Models\ExperienceCertificate::defaultExpCertificatRegister($user->id);
            // \App\Models\JoiningLetter::defaultJoiningLetterRegister($user->id);
            // \App\Models\NOC::defaultNocCertificateRegister($user->id);

            $email = $googleUser->getEmail();
            $name = $googleUser->getName();

            return redirect()->route('auth.register')->with([
                'name' => $name,
                'email' => $email,
                'error' => __('You are not registered yet. Please complete the registration to continue.'),
            ]);
        }
        // if (empty($user->avatar)) {
        //     $googleAvatarUrl = $googleUser->getAvatar();

        //     try {
        //         $imageContents = file_get_contents($googleAvatarUrl);

        //         $extension = 'png';
        //         $fileNameToStore = 'avatar_' . uniqid() . '.' . $extension;
        //         $dir = 'uploads/avatars/';
        //         $fullPath = $dir . $fileNameToStore;

        //         if (!\File::exists(storage_path($dir))) {
        //             \File::makeDirectory(storage_path($dir), 0755, true);
        //         }

        //         $putSuccess = \File::put(storage_path($fullPath), $imageContents);
        //         if ($putSuccess !== false) {
        //             $user->avatar = $fileNameToStore;
        //             $user->save();
        //         }

        //     } catch (\Exception $e) {
        //         // \Log::error('Failed to download Google avatar: ' . $e->getMessage());
        //     }
        // }


        if (empty($user->avatar)) {
            $googleAvatarUrl = $googleUser->getAvatar();


            try {
                if ($user->type == 'company') {
                    $companySlug = Str::slug($user->company_name);
                    $dir = "uploads/companies/{$companySlug}/avatar";
                } else {
                    $companySlug = Str::slug($createdBy->company_name);
                    $createdBy = User::where('id', $user->created_by)->first();
                    $nameSlug = Str::slug($user->name);
                    $dir = "uploads/companies/{$companySlug}/employees/{$nameSlug}/avatar";
                }


                $imageContents = file_get_contents($googleAvatarUrl);

                $extension = pathinfo(parse_url($googleAvatarUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
                $fileNameToStore = 'avatar_' . uniqid() . '.' . $extension;

                $tmpFilePath = sys_get_temp_dir() . '/' . $fileNameToStore;
                file_put_contents($tmpFilePath, $imageContents);

                $uploadedFile = new UploadedFile(
                    $tmpFilePath,
                    $fileNameToStore,
                    mime_content_type($tmpFilePath),
                    null,
                    true
                );

                $request = new \Illuminate\Http\Request();
                $request->files->set('avatar', $uploadedFile);

                $uploadResult = Utility::upload_file($request, 'avatar', $fileNameToStore, $dir);

                if ($uploadResult['flag'] == 1) {
                    $user->avatar = $uploadResult['url'];
                    $user->save();
                } else {
                    // \Log::error("Google Avatar Upload Failed: " . $uploadResult['msg']);
                }

            } catch (\Exception $e) {
                // \Log::error('Failed to download Google avatar: ' . $e->getMessage());
            }
        }

        if (empty($user->email_verified_at)) {
            $user->email_verified_at = now();
            $user->save();
        }

        Auth::login($user);

        if (in_array($user->type, ['company', 'Employee_Admin'])) {
            return redirect()->intended(RouteServiceProvider::COMPANY_HOME);
        } else {
            return redirect()->intended(\App\Providers\RouteServiceProvider::EMPLOYEE_HOME);
        }


//        if ($user->type == 'company' || $user->hasRole('stealth user')) {
//            return redirect()->intended(\App\Providers\RouteServiceProvider::COMPANY_HOME);
//        } else {
//            return redirect()->intended(RouteServiceProvider::EMPLOYEE_HOME);
//        }
    }
}
