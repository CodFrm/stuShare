<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/5 13:29
 * blog:blog.icodef.com
 * function:
 *============================
 */


function isActive($ctrl) {
    if (is_array($ctrl)) {
        foreach ($ctrl as $item) {
            if ($item == input('ctrl')) {
                return ' active';
            }
        }
    } else if ($ctrl == input('ctrl')) {
        return ' active';
    }
    return '';
}

function getUserSetMeal($uid) {
    return DB('usergroup as a')->find(['uid' => $uid], '*',
        'join ' . input('config.DB_PREFIX') . 'group as b on a.group_id=b.group_id ' .
        'join ' . input('config.DB_PREFIX') . 'set_meal as c on c.group_id=b.group_id');
}
