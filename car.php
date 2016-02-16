<?php

require('lib/common.php');

/*
 * ����� ����� �������
 */
function main()
{
    session_start();

    // ������������ ������������ �����
    $dbh = db_connect();

    $items_result = get_popular_products($dbh);
    $category_items = db_product_find_category_all($dbh);

    db_close($dbh);

    render('Main_Page_Template', array(
        'items' => $items_result, 'category' => $category_items));
}

main();