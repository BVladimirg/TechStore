<?php

require('lib/common.php');

/*���������: ���������� �� ����� */
function is_postback() {
    return isset($_POST ['register']);
}

function main()
{
    session_start();



    render('register_form', array('form' => array(), 'errors' => array()));
}

main();