<nav class="nav-sb">
    <div class="logo">
        <i class="bx bx-menu menu-icon"></i>
        <span class="logo-name"><?= $_SESSION['storeName'] ?></span>
    </div>
    <div class="button-modes">
        <?php if ($_SESSION['roleId'] == 1) : ?>
        <a href="notifications.php" class="notification-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z" />
            </svg>
        </a>
        <?php endif; ?>
        <input type="checkbox" id="dark-mode">
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