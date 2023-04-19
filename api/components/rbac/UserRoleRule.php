<?php

namespace app\components\rbac;

use app\models\User;
use yii\rbac\Rule;

class UserRoleRule extends Rule
{
    public $name = 'userRole';

    public function execute($user, $item, $params)
    {
        if (!\Yii::$app->user->isGuest) {
            /** @var User $currentUser */
            $currentUser = \Yii::$app->user->identity;
            $role = $currentUser->role;

            if ($item->name === User::ROLE_ADMIN) {
                return $role == User::ROLE_ADMIN;
            }
            elseif ($item->name === User::ROLE_MODERATOR) {
                return $role == User::ROLE_ADMIN ||
                    $role == User::ROLE_MODERATOR;
            }
            elseif ($item->name === User::ROLE_TECH) {
                return $role == User::ROLE_ADMIN ||
                    $role == User::ROLE_MODERATOR ||
                    $role == User::ROLE_TECH;
            }
            elseif ($item->name === User::ROLE_USER) {
                return $role == User::ROLE_ADMIN ||
                    $role == User::ROLE_MODERATOR ||
                    $role == User::ROLE_TECH ||
                    $role == User::ROLE_USER;
            }
        }

        return false;
    }
}