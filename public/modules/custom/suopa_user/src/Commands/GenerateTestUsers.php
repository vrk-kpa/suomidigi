<?php

namespace Drupal\suopa_user\Commands;

use Drupal\user\Entity\User;
use Drupal\group\Entity\Group;
use Drush\Commands\DrushCommands;
use Drupal\key\Entity\Key;

/**
 * File for creating test users via drush.
 */
class GenerateTestUsers extends DrushCommands {

  /**
   * Constructs a new generateTestUsers object.
   */
  public function __construct() {

  }

  /**
   * Generate test users.
   *
   * @command suopa_user:generateTestUsers
   */
  public function generateTestUsers() {
    $data = \Drupal::service('key.repository')->getKey('test_users');

    if (!empty($data) && $data instanceof Key) {
      $users = unserialize($data->getKeyValue());

      if (is_array($users)) {
        foreach ($users as $user_name => $user_data) {
          if (user_load_by_name($user_name) === FALSE) {
            $user = User::create();
            $user->setPassword($user_data['password']);
            $user->enforceIsNew();
            $user->activate();
            $user->setUsername($user_name);

            if (!empty($user_data['roles'])) {
              foreach ($user_data['roles'] as $role => $roles) {
                if (isset($role) && $role == 'role') {
                  $user->addRole($roles);
                }
              }
            }

            $user->save();
            $this->logger()->success(dt('User @user has been created.', ['@user' => $user_name]));

            if (!empty($user_data['group'])) {
              $account = User::load($user->id());
              $group = Group::load($user_data['group']);

              if (isset($user_data['roles']['group'])) {
                $group->addMember($account, ['group_roles' => $user_data['roles']['group']]);
              }
              else {
                $group->addMember($account);
              }
              $group->save();
            }
          }
        }
      }
    }
  }

}
