<?php


namespace app\commands;


use app\components\rbac\UserRoleRule;
use app\models\User;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = \Yii::$app->getAuthManager();
        $auth->removeAll();

        //Включаем наш обработчик
        $rule = new UserRoleRule();
        $auth->add($rule);

        // User
        $user = $auth->createRole(User::ROLE_USER);
        $user->description = 'Пользователь';
        $user->ruleName = $rule->name;
        $auth->add($user);

        // Tech
        $tech = $auth->createRole(User::ROLE_TECH);
        $tech->description = 'Специалист техподдержки';
        $tech->ruleName = $rule->name;
        $auth->add($tech);

        // Moderator
        $moderator = $auth->createRole(User::ROLE_MODERATOR);
        $moderator->description = 'Модератор';
        $moderator->ruleName = $rule->name;
        $auth->add($moderator);

        // Admin
        $admin = $auth->createRole(User::ROLE_ADMIN);
        $admin->description = 'Администратор';
        $admin->ruleName = $rule->name;
        $auth->add($admin);

        $auth->addChild($tech, $user);
        $auth->addChild($moderator, $tech);
        $auth->addChild($admin, $moderator);
    }
}
