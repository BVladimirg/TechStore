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
function read_string($form, $field, &$obj, &$errors, $min, $max, $is_required, $default=null, $trim=true)
{
    $obj[$field] = $default;
    if (!isset($form[$field])) {
        return $is_required ? add_error($errors, $field, 'required') : true;
    }

    $value = $trim ? trim($form[$field]) : $form[$field];


    if (strlen($value) < $min)
        return add_error($errors, $field, 'too-short');

    if (strlen($value) > $max)
        return add_error($errors, $field, 'too-long');

    $obj[$field] = $value;
    return true;
}

function read_email($form, $field, &$obj, &$errors, $min, $max, $is_required, $default=null)
{
    $obj[$field] = $default;
    if (!isset($form[$field])) {
        return $is_required ? add_error($errors, $field, 'required') : true;
    }

    $value = trim($form[$field]);
    if (strlen($value) < $min)
        return add_error($errors, $field, 'too-short');

    if (strlen($value) > $max)
        return add_error($errors, $field, 'too-long');

    // ���������, ��� � ������ ����� ����� ����������� �����
    if (!filter_var($value, FILTER_VALIDATE_EMAIL))
        return add_error($errors, $field, 'invalid');

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

function login_user($dbh, &$user, &$errors)
{
    $user = array();
    $errors = empty_errors();

    // ��������� ������ �� �������
    read_string($_POST, 'username', $user, $errors, 2, 64, true);
    read_string($_POST, 'password', $user, $errors, 6, 20, true);

    if (has_errors($errors))
        return false;


    // ����� �������� ���������, ���� ������������ � ��������� ������
    $db_user = db_user_find_by_login($dbh, $user['username']);
    // �������, ���� �� ����� ������������ � ��������� �� ������� ������
    if ($db_user == null || $db_user['password'] !== crypt($user['password'], $db_user['password']))
        return add_error($errors, 'password', 'invalid');

    // ������������ ���� ���������� ��� � ������, ���������� ��� � ������
    store_current_user_id($db_user['id']);
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

function register_user($dbh, &$user, &$errors)
{
    $user = array();
    $errors = empty_errors();

    // ��������� ������ �� �������
    read_string($_POST, 'username', $user, $errors, 2, 64, true);
    read_email($_POST, 'e-mail', $user, $errors, 2, 64, true);
    read_string($_POST, 'password', $user, $errors, 6, 24, true);
    read_string($_POST, 'confirm-password', $user, $errors, 6, 24, true);
    read_list($_POST, 'gender', $user, $errors, array('M', 'F'), false);
    read_bool($_POST, 'newsletter', $user, $errors, '1', false, false);

    // ������ � ������������� ������ ������ ���������
    if (!is_error($errors, 'password') &&
        !is_error($errors, 'confirm-password') &&
        $user['password'] != $user['confirm-password']) {
        $errors['fields'][] = 'password';
        add_error($errors, 'confirm-password', 'dont-match');
    }

    if (has_errors($errors))
        return false;

    // �������� ������ ������������
    $user['password'] = crypt($user['password']);
    unset($user['password_confirmation']);

    // ����� �������� ���������, ��������� ������������ � ���� ������
    $db_user = db_user_insert($dbh, $user);

    // ������������� ������� ������������ ����� �����������, ��������� ��� � ������
    store_current_user_id($db_user['id']);
    return true;
}

/* ****************************************************************************
 * ������ ������������� � ���� ������
 */

/*
 * ��������� ����������� � ���� ������
 */


function db_connect()
{
    $dbh = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (mysqli_connect_errno()) {
        db_handle_error($dbh);
    }

    mysqli_set_charset($dbh, "utf8");
    return $dbh;
}

/*
 * ��������� ����������� � ���� ������
 */
function db_close($dbh)
{
    mysqli_close($dbh);
}

/*
 * ��������� ������ ����������� � ���� ������
 */
function db_handle_error($dbh)
{
    $code = '@unknown-error';
    $message = '';
    if (mysqli_connect_error()) {
        $code = '@connect-error';
        $message = mysqli_connect_error();
    }

    if (mysqli_error($dbh)) {
        $code = '@query-error';
        $message =mysqli_error($dbh);
    }

    render('error', array(
        'code' => $code, 'message' => $message,
    ));
    exit;
}


/*
 * ��������� �� ���� ������ ������ �������������
 */
function db_user_find_all($dbh)
{
    $query = 'SELECT * FROM users';
    $result = array();

    // ��������� ������ � ���� ������
    $qr = mysqli_query($dbh, $query, MYSQLI_STORE_RESULT);
    if ($qr === false)
        db_handle_error($dbh);

    // ��������������� ��������� ������
    while ($row = mysqli_fetch_assoc($qr))
        $result[] = $row;

    // ����������� �������, ��������� � ��������� ����������
    mysqli_free_result($qr);

    return $result;
}

/*
 * ��������� ����� � ���� ������ � �������� ������������ � ��������� id
 */
function db_user_find_by_id($dbh, $id)
{
    $query = 'SELECT * FROM users WHERE id=?';

    // �������������� ������ ��� ����������
    $stmt = mysqli_prepare($dbh, $query);
    if ($stmt === false)
        db_handle_error($dbh);

    mysqli_stmt_bind_param($stmt, 's', $id);

    // ��������� ������ � �������� ���������
    if (mysqli_stmt_execute($stmt) === false)
        db_handle_error($dbh);

    // �������� �������������� ����� �����
    $qr = mysqli_stmt_get_result($stmt);
    if ($qr === false)
        db_handle_error($dbh);

    // ��������� �������������� ������
    $result = mysqli_fetch_assoc($qr);

    // ����������� �������, ��������� � ��������� ���������� � �������
    mysqli_free_result($qr);
    mysqli_stmt_close($stmt);

    return $result;
}

/*
 * ��������� ����� � ���� ������ � �������� ������������ � ��������� �������
 * (������� ������� ����� ����������� ����� � ��� ������������)
 */
function db_user_find_by_login($dbh, $login)
{
    $query = 'SELECT * FROM users WHERE email=? OR nickname=?';

    // �������������� ������ ��� ����������
    $stmt = mysqli_prepare($dbh, $query);
    if ($stmt === false)
        db_handle_error($dbh);

    mysqli_stmt_bind_param($stmt, 'ss', $login, $login);

    // ��������� ������ � �������� ���������
    if (mysqli_stmt_execute($stmt) === false)
        db_handle_error($dbh);

    // �������� �������������� ����� �����
    $qr = mysqli_stmt_get_result($stmt);
    if ($qr === false)
        db_handle_error($dbh);

    // ��������� �������������� ������
    $result = mysqli_fetch_assoc($qr);

    // ����������� �������, ��������� � ��������� ���������� � �������
    mysqli_free_result($qr);
    mysqli_stmt_close($stmt);

    return $result;
}

/*
 * ��������� � ���� ������ ������ � ����������� � ������������, ���������� ������
 * � ������� ������������ � ��� id � ���� ������
 */
function db_user_insert($dbh, $user)
{
    $query = 'INSERT INTO users(nickname,email,password,gender,newsletter) VALUES(?,?,?,?,?)';

    // �������������� ������ ��� ����������
    $stmt = mysqli_prepare($dbh, $query);
    if ($stmt === false)
        db_handle_error($dbh);

    mysqli_stmt_bind_param($stmt, 'ssssi',
        $user['username'], $user['e-mail'], $user['password'], $user['gender'], $user['newsletter']);

    // ��������� ������ � �������� ���������
    if (mysqli_stmt_execute($stmt) === false)
        db_handle_error($dbh);

    // �������� ������������� ����������� ������
    $user['id'] = mysqli_insert_id($dbh);

    // ����������� �������, ��������� � ��������� ���������� � �������
    mysqli_stmt_close($stmt);

    return $user;
}
