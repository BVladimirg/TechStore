<?php


require('BDconfig.php');

/*��������� ������������� �� ��������� ��������*/

function redirect($url) {
    session_write_close();
    header('Location: '.$url);
    exit;
}

/*��������� ����� ���������� ������� templates � ������� data*/

function render($template, $data=array()) {
    extract($data);
    require('templates/'.$template.'.php');
}