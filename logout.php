<?php

require('lib/common.php');

function main()
{
    // ������� ������
    session_start();

    // ��������� ����� �� ������� � �������������� ������������ �� ������� ��������
    logout_user();
    redirect('./');
}

main();