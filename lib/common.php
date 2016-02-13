<?php


require('BDconfig.php');

/*��������� ������������� �� ��������� ��������*/

function redirect($url)
{
    session_write_close();
    header('Location: '.$url);
    exit;
}

/*��������� ����� ���������� ������� templates � ������� data*/

function render($template, $data=array())
{
    extract($data);
    require('templates/'.$template.'.php');
}

/*
*  ��� ������������, ��������� � ������� ������ � ����� ��� ID
*/

/*������� ��� ��������: ������� �� ������������ � ����*/
function is_current_user()
{
    return isset($_SESSION['user_id']);
}

/*������� ���������� ������������ ������������*/
function get_current_id()
{
    return $_SESSION['user_id'];
}

/*������� ���������� ������������ ������������ � ������*/
function store_current_user_id($id)
{
    $_SESSION['user_id'] = $id;
}

/*���������� ����������� ������������*/
function reset_current_user_id()
{
    unset($_SESSION['user_id']);
}

/* ****************************************************************************
* ������� ������ � �������� ������
*/

/*
 * �������������� ��������� ������� ��� �������� ���������� �� �������
 */
function empty_errors()
{
    return array(
        'fields' 	=> array(),
        'messages'	=> array(),
    );
}

/*
 * ���������, ��� ���� ��������� ���� � �������� ������
 */
function has_errors($errors)
{
    return isset($errors['fields']) && count($errors['fields']) > 0;
}

/*
 * ���������, ��� ��������� ���� ���� � ������ ��������� �����
 */
function is_error($errors, $field)
{
    return isset($errors['fields']) && in_array($field, $errors['fields']);
}

/*
 * ��������� �������� ������ � ������ ������
 */
function add_error(&$errors, $field, $description)
{
    $errors['fields'][] = $field;
    $errors['messages'][$field] = "@$field-$description";
    return false;
}


/* ****************************************************************************
 * ��������� ������
 */

/*
 * ��������� ������������ ������ � �����, ���� ������ ���������, �������� �� � $obj
 * � ���������� true; false � ����������� ������ ������, ���� ���
 */
function read_string($form, $field, &$obj, &$errors, $min, $max, $is_required, $default=null, $trim=true) {
    $obj[$field]=$default;
    if(!isset($form[$field])) {
        return $is_required ? add_error($errors, $field, 'required') : true;
    }

    $value = $trim ? trim($form[$field]) : $form[$field];
    if($value == '' && $is_required) {
        return add_error($errors, $field, 'required');
    }

    if(strlen($value) > $max) {
        return add_error($errors, $field, 'too-long');
    }

    if (strlen($value) < $min) {
        return add_error($errors, $field, 'too-long');
    }

    $obj[$field] = $value;
    return true;
}

function read_email($form, $field, &$obj, &$errors, $min, $max, $is_required, $default=null, $trim=true)
{
    $obj[$field] = $default;
    if(!isset($form[$field])) {
        return $is_required ? add_error($errors, $field, 'required') : true;
    }

    $value = trim($form[$field]);

    if (strlen($value) < $min)
        return add_error($errors, $field, 'too-short');

    if (strlen($value) > $max)
        return add_error($errors, $field, 'too-long');

    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        return add_error($errors, $field, 'not validate e-mail');
    }

    $obj[$field] = $value;
    return true;
}

/*
 * ��������� ������������ ������ ������ �� �������� � �����, ���� ������� ��������
 * �� ���������� ������, �������� ��� � $obj � ���������� true; false � �����������
 * ������ ������, ���� ���
 */
function read_list($form, $field, &$obj, &$errors, $list, $is_required, $default=null)
{
    $obj[$field] = $default;
    if (!isset($form[$field])) {
        return $is_required ? add_error($errors, $field, 'required') : true;
    }

    $value = trim($form[$field]);
    if (!in_array($value, $list))
        return add_error($errors, $field, 'invalid');

    $obj[$field] = $value;
    return true;
}

/*
 * ��������� ������������ ����������� ��������, ���� ���������, �������� ���
 * � $obj � ���������� true; false � ����������� ������ ������, ���� ���
 */
function read_bool($form, $field, &$obj, &$errors, $true, $is_required, $default=null)
{
    $obj[$field] = $default;
    if (!isset($form[$field])) {
        return $is_required ? add_error($errors, $field, 'required') : true;
    }

    $value = trim($form[$field]);
    $obj[$field] = $value === $true;
    return true;
}

/********************************************************
/*
 * ��������� ���� ������������ � �������, ���������� true, ���� ����
 * �������� �������, � false � ����������� ������ ������ � ���������
 * ������
 */

function login_user(&$user, &$errors)
{
    $user = array();
    $errors = empty_errors();

    read_string($_POST, 'username', $user, $errors, 3, 20, true);
    read_string($_POST, 'password', $user, $errors, 6, 20, true);

    if (has_errors($errors)) {
        return false;
    }

    store_current_user_id(456);
    return true;
}

/*��������� ����� �� �������*/
function logout_user()
{
    reset_current_user_id();
}

/*
 * ��������� ����������� ������������, ���������� true, ���� ������������
 * ����������� �������, � false � ����������� ������ ������ � ���������
 * ������
 */

function register_user(&$user, &$errors)
{
    $user = array();
    $errors = empty_errors();

    read_string($_POST, 'username', $user, $errors, 3, 64, true);
    read_string($_POST, 'password', $user, $errors, 6, 20, true);
    read_email($_POST, 'e-mail', $user, $errors, 2, 64, true);
    read_string($_POST, 'confirm-password', $user, $errors, 2, 64, true);
    read_bool($_POST, 'newsletter', $user, $errors, '1', false, false);
    read_list($_POST, 'gender', $user, $errors, array('M', 'F'), false);
}

