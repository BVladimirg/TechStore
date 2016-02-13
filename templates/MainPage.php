<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <link href="../css/main.css" rel="stylesheet" type="text/css">
    <title>Интернет-магазин цифровой и бытовой техники</title>
</head>
<body>
<!--Header-->
<div class="header">
    <div class="layout-positioner">
        <div class="lname">
            <div class="logo">
                <a href="MainPage.php"><img alt="TS Магазин цифрофой техники" src="../Images/logo.png"></a>
            </div>
        </div>
        <div class="search">
            <form action="POMENYAT" method="get">
                <input class="inputSearch" type="text" name="search" value="" placeholder="Поиск...">
                <input class="submitSearchBottom" type="submit" name="send" value="">
            </form>
        </div>
        <div class="rightHeader">
            <a href="CarPage.php"><div class="ToCar headCar">В корзину</div></a>
            <div class="login_positioner">
                <a href="login_form.php"><div class="entry_button login_cell">Вход</div></a>
                <a href="fgsg"><div class="registration_button login_cell">Регистрация</div></a>
            </div>
        </div>
    </div>
</div>

<div class=" central-part">
    <div class="layout-positioner">
        <div class="HelloDiv">Добро пожаловать на главную страницу магазина цифровой техники!</div>
        <!-- menu -->
        <div class="main-menu">
            <div class="catalog">Каталог товаров</div>
            <ul>
                <?php for($i=0; $i<6; $i++):?>
                    <li class="notebook-menu"><a href="CategoryPage.php">Ноутбуки и планшеты</a>
                        <ul class="sub">
                            <?php for($j=0; $j<6; $j++):?>
                                <li><a href="ProductPage.php">Ноутбук Lenovo IdeaPad G5045 80MQ001GRK</a></li>
                            <?php endfor;?>
                        </ul>
                    </li>
               <?php endfor;?>
            </ul>
        </div>

        <!-- Popular -->
        <div class="main-part">
            <div class="category_name popular">Популярное</div>
            <div class="main-window">
                <?php for($i=0; $i<6; $i++): ?>
                    <a href="ProductPage.php">
                        <div class="item">
                            <div class="main-image">
                                <img alt="Notebook" src="../Images/30023889m.jpg">
                            </div>
                            <div class="product-name">15.6" Ноутбук Asus X553MA 90NB04X1-M25360 черный</div>
                            <div class="Price">21990р.</div>
                            <a href="CarPage.php"><div class="ToCar">+Купить</div></a>
                        </div>
                    </a>
                <?php endfor;?>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <div class="layout-positioner">
        <div class="contackts">
            <p>Бухаркин Владимир</p>
            <p>Группа ВР-09-15-11</p>
        </div>
    </div>
</div>
</body>
</html>