<?php

require('lib/common.php');

/*
 * ���������, ��� ���� ��������� �������� ����� �����
 */
function is_postback()
{
    return isset($_POST['login']);
}

/*
 * ����� ����� �������
 */
function main()
{
    // ������� ������
    session_start();

    if (is_current_user()) {
        // ���� ������������ ��� ���������, �� ���������� ��� �� �������
        redirect('./');
    }

    if (is_postback()) {
        // ������������ ������������ �����
        $dbh = db_connect();
        $post_result = login_user($dbh, $user, $errors);
        db_close($dbh);

        if ($post_result) {
            // �������������� �� �������
            redirect('./');
        } else {
            // ���������� � ������������ ��������� �����������, ������� �������� � ��������
            render('login_form', array(
                'form' => $_POST, 'errors' => $errors
            ));
        }
    } else {
        // ���������� ������������ ������ ����� ��� �����
        render('login_form', array(
            'form' => array(), 'errors' => array()
        ));
    }
}

main();
