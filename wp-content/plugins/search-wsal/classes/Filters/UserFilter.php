<?php

class WSAL_AS_Filters_UserFilter extends WSAL_AS_Filters_AbstractFilter {
    
    public function GetName(){
        return __('User');
    }
    
    public function IsApplicable($query){
        global $wpdb;
        $args = array(esc_sql($query) . '%', esc_sql($query) . '%');
        return !!$wpdb->count('SELECT COUNT(*) FROM wp_user WHERE name LIKE %s OR username LIKE %s', $args);
    }
    
    public function GetPrefixes(){
        return array(
//          'user.firstname',
//          'user.lastname',
//          'user.id',
//          'user.login',
//          'user',
            'username',
        );
    }
    
    public function GetWidgets(){
        $wgt = new WSAL_AS_Filters_AutoCompleteWidget($this, 'username', 'Username');
        $wgt->SetDataLoader(array($this, 'GetMatchingUsers'));
        return array($wgt);
    }
    
    public function GetMatchingUsers(WSAL_AS_Filters_AutoCompleteWidget $wgt){
        global $wpdb;
        $users = $wpdb->get_results("SELECT user_login, user_email, user_nicename FROM $wpdb->users");
        foreach ($users as $user) {
            $wgt->Add(
                $user->user_login,
                $user->user_login
            );
        }
    }
    
    public function ModifyQuery($query, $prefix, $value){
        $query->addMetaJoin();
        switch($prefix){
            case 'username':
                $userId = get_user_by('login', $value);
                $userId = $userId ? $userId->ID : -1;
                if ($userId == -1){
                    $userId = get_user_by('slug', $value);
                    $userId = $userId ? $userId->ID : -1;
                }

                $query->addORCondition(
                    array(
                        '( meta.name = "Username" AND TRIM(BOTH "\"" FROM meta.value) = TRIM(BOTH "\"" FROM %s) ) ' => json_encode($value), 
                        '( meta.name = "CurrentUserID" AND TRIM(BOTH "\"" FROM meta.value) = TRIM(BOTH "\"" FROM %s) )' => json_encode($userId)
                    )
                );
                
                break;
            default:
                throw new Exception('Unsupported filter "' . $prefix . '".');
        }
    }
}
