<?php

require('lib/common.php');

function main() {
    session_start();

    /*������� ������������� �������� */
    render('Main_Page_Template', array());
}

main();