<?php

require('lib/common.php');

/*���������: ���������� �� ����� */

function is_postback() {
    return isset($_POST['login']);
}

function main(){

    session_start();

    if(true){
        redirect('./');
    }


}

main();