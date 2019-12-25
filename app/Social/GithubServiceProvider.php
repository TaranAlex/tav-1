<?php

namespace App\Social;

use App\User;

class GithubServiceProvider extends AbstractServiceProvider
{
    /**
     *  Handle Github response
     *
     *  @return Illuminate\Http\Response
     */
    public function handle()
    {
//        $user = $this->provider->fields([
//            'first_name',
//            'last_name',
//            'email',
//            'gender',
//            'verified',
//        ])->user();

        $user = $this->provider->user();

        $existingUser = User::where('settings->github_id', $user->id)->first();

        if ($existingUser) {
            $settings = $existingUser->settings;

            if (! isset($settings['github_id'])) {
                $settings['github_id'] = $user->id;
                $existingUser->settings = $settings;
                $existingUser->save();
            }

            return $this->login($existingUser);
        }

        $newUser = $this->register([
            'login' => $user->user['login'],
            'email' => $user->email,
            'settings' => [
                'github_id' => $user->id,
            ]
            //'settings' => json_encode(['github_id' => $user->id]),
        ]);
        /*$newUser = $this->register([
            'first_name' => $user->user['first_name'],
            'last_name' => $user->user['last_name'],
            'email' => $user->email,
            'gender' => ucfirst($user->user['gender']),
            'settings' => [
                'github_id' => $user->id,
            ]
        ]);*/

        return $this->login($newUser);
    }
}