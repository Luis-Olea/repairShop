<nav class="nav-sb">
    <div class="logo">
        <i class="bx bx-menu menu-icon"></i>
        <span class="logo-name"><?= $_SESSION['storeName'] ?></span>
    </div>
    <div class="button-modes">
        <input type="checkbox" id="dark-mode">
        <label class="label-btn" for="dark-mode"></label>
    </div>

    <div class="sidebar">
        <div class="logo">
            <i class="bx bx-menu menu-icon"></i>
            <span class="logo-name"><?= $_SESSION['storeName'] ?></span>
        </div>

        <div class="sidebar-content">
            <ul class="lists">
                <li class="list">
                    <a href="dashboard.php" class="nav-sb-link">
                        <i class="bx bx-home-alt icon"></i>
                        <span class="link">Dashboard</span>
                    </a>
                </li>
                <?php if ($_SESSION['roleId'] != 3) : ?>
                    <li class="list">
                        <a href="pos.php" class="nav-sb-link">
                            <i class="bx bx-cart icon"></i>
                            <span class="link">POS</span>
                        </a>
                    </li>
                <?php endif;
                if ($_SESSION['roleId'] != 2) : ?>
                    <li class="list">
                        <a href="productMenu.php" class="nav-sb-link">
                            <i class="bx bx-store-alt icon"></i>
                            <span class="link">Productos</span>
                        </a>
                    </li>
                <?php endif;
                if ($_SESSION['roleId'] != 3) : ?>
                    <li class="list">
                        <a href="clientMenu.php" class="nav-sb-link">
                            <i class="bx bx-user-circle icon"></i>
                            <span class="link">Clientes</span>
                        </a>
                    </li>
                <?php endif;
                if ($_SESSION['roleId'] != 3) : ?>
                    <li class="list">
                        <a href="clientReceipt.php" class="nav-sb-link">
                            <i class="bx bx-dollar-circle icon"></i>
                            <span class="link">Ventas</span>
                        </a>
                    </li>
                <?php endif;
                if ($_SESSION['roleId'] != 2) : ?>
                    <li class="list">
                        <a href="supplierMenu.php" class="nav-sb-link">
                            <i class="bx bx-box icon"></i>
                            <span class="link">Proovedores</span>
                        </a>
                    </li>
                <?php endif;
                if ($_SESSION['roleId'] != 2) : ?>
                    <li class="list">
                        <a href="suppPayments.php" class="nav-sb-link">
                            <i class="bx bx-edit icon"></i>
                            <span class="link">Pedidos</span>
                        </a>
                    </li>
                <?php endif;
                if ($_SESSION['roleId'] == 1) : ?>
                    <li class="list">
                        <a href="userMenu.php" class="nav-sb-link">
                            <i class="bx bx-user icon"></i>
                            <span class="link">Usuarios</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="listsE">
                <div class="bottom-cotent">
                    <li class="list">
                        <a href="configuration.php" class="nav-sb-link">
                            <i class="bx bx-cog icon"></i>
                            <span class="link">Ajustes</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href='db/exit.php' class="nav-sb-link">
                            <i class="bx bx-log-out icon"></i>
                            <span class="link">Salir</span>
                        </a>
                    </li>
                </div>
            </ul>
        </div>
    </div>
</nav>

<section class="overlay"></section>