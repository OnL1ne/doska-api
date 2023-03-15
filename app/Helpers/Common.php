<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class CommonHelper {
    /**
     * @param $users
     * @return array|bool
     */
    public function decryptUsersDetails($users)
    {
        if (!$users) return false;
        $result = array();
        foreach ($users as $key => $user) {
            $result[$key] = $user;
            if (isset($result[$key]->detail)) {
                $result[$key]->detail->first_name = decrypt($result[$key]->detail->first_name);
                $result[$key]->detail->last_name = decrypt($result[$key]->detail->last_name);
                $result[$key]->detail->title = decrypt($result[$key]->detail->title);
            }
        }

        return $result;
    }
}
