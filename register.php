<?php

require('lib/common.php');

/*���������: ���������� �� ����� */
function is_postback() {
    return isset($_POST ['register']);
}