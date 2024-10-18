<link rel="stylesheet" href="../css/header.css">

<?php
    if (session_status() === PHP_SESSION_NONE){session_start();}
    if (!isset($_SESSION["client_id"])){
?>
    <header class="header">
        <div class="header__container">
            <div class="header__nav">
                <div class="header__logo">
                    <a href="./index.php"><img src="img/trisquel.webp" alt="Logo trisquel"></a>
                    <a href="./index.php" class="header__name">ALHaiZ Breizh</a>
                </div>
                <nav class="header__menu" id="LeMenu">
                    <ul class="menu__list" id="menu__list">
                        <li class="menu__item">
                            <a href="index.php" class="menu__link">Logements</a>
                        </li>
                        <li class="menu__item">
                            <a href="buisness/index.php" class="menu__link" style="color: #5669FF;">Ajouter mon établissement</a>
                        </li>

                    </ul>
                </nav>
            </div>
            <div class="header__form">
                <div class="header__connexion"><a href="login.php">Connexion</a></div>                    
            </div>


        </div>
    </header>
    <script src="js/header.js"></script>
<?php 
    } else {
        require_once '../utils.php';
        $id = $_SESSION["client_id"];
        $photo_user = $_SESSION["photo_user"];
        $user_info = request("SELECT pseudo FROM sae._compte_client
        NATURAL JOIN sae._utilisateur
        WHERE id = '$id'", true);
?>

<header class="header">
<div class="header__container">
    <div class="header__nav">
        <div class="header__logo">
            <a href="index.php"><img src="img/trisquel.webp" alt="Logo trisquel"></a>
            <a href="index.php" class="header__name">ALHaiZ Breizh</a>
        </div>
        <nav class="header__menu menu" id="LeMenu">
            <ul class="menu__list" id="menu__list">
                <li class="menu__item">
                    <a href="index.php" class="menu__link">Logements</a>
                </li>

                <li class="menu__item">
                    <a href="buisness/index.php" class="menu__link" style="color: #5669FF;">Ajouter mon établissement</a>
                 </li>
                
            </ul>
        </nav>
    </div>
    <div class="header__form">
            <div class="user__info" id="header__info">
                <img src="img<?=$photo_user?>" alt="Photo User" class="user__photo">
                <p><?= $user_info["pseudo"] ?></p>
                <img src="img/fleche.webp" alt="Ouvrir le menu" class="user__down">
            </div>                   
    </div>
    <ul class="header__menu-user" id="menu-user">
        <img src="img/fermer.webp" alt="Fermer le menu" id="fermerMenu">
        <li class="menu__item ">
            <a href="consulter_mon_compte.php" class="menu__link">Mon compte</a>
        </li>
        <li class="menu__item ">
            <a href="mes_reserv.php" class="menu__link">Mes réservations</a>
        </li>

        <li class="menu__item ">
            <a href="buisness/index.php" class="menu__link" style="color: #5669FF;">Ajouter mon établissement</a>
        </li>
        <li class="menu__item ">
            <a href="logout.php" class="menu__link" style="color: #FF5656;">Se déconnecter</a>
        </li>

        
    </ul> 

</div>
</header>
<script src="../js/header_user.js"></script>
<script src="../js/header.js"></script>
<?php 
    }
?>
