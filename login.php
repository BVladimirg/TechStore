<?php

require('lib/common.php');

/*���������: ���������� �� ����� */

function is_postback() {
    return isset($_POST['login']);
}

function main(){

    session_start();

    if(is_current_user()) {
        redirect('./');
    }

    if(is_postback()){

    }


}

main();