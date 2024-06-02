<div class="sidebar">
    <div class="logo-details">
        <img src="\img\descarga.png" alt="TeLoCambio Logo" class="icon">
        <div class="logo_name">TeLoCambio</div>
        <i class='bx bx-menu' id="btn"></i>
    </div>
    <ul class="nav-list">
        <li>
            <a href="menu.php">
                <i class='bx bx-home' type="solid"></i>
                <span class="links_name">Menu principal</span>
            </a>
            <span class="tooltip">Menu principal</span>
        </li>
        <li>
            <a href="misTurnos.php">
                <i class='bx bx-calendar'></i>
                <span class="links_name">Ver mis turnos</span>
            </a>
            <span class="tooltip">Ver mis turnos</span>
        </li>
        <li>
            <a href="notificaciones.php">
                <i class='bx bx-bell'></i>
                <span class="links_name">Notificaciones</span>
            </a>
            <span class="tooltip">Notificaciones</span>
        </li>
        <li>
            <a href="misCalificaciones.php">
                <i class='bx bx-star'></i>
                <span class="links_name">Mis calificaciones</span>
            </a>
            <span class="tooltip">Mis calificaciones</span>
        </li>
        <li class="profile">
            <div class="profile-details">
                <i class='bx bx-plus-medical'></i>
                <div class="name_job">
                <?php
                if (isset($_SESSION["usuario"])) {
                    $usuario = $_SESSION["usuario"];
                    echo "<p class='name'>$usuario</p>";
                }
                ?>
                <div class="job">Enfermero/a</div>
                </div>
                <a href="cerrarSesion.php" title="Cerrar sesión">
                    <i class='bx bx-log-out' id="log_out"></i>
                </a>
            </div>
        </li>
    </ul>
</div>
<script>
let sidebar = document.querySelector(".sidebar");
let closeBtn = document.querySelector("#btn");
let searchBtn = document.querySelector(".bx-search");

closeBtn.addEventListener("click", () => {
    sidebar.classList.toggle("open");
    menuBtnChange();
});

searchBtn.addEventListener("click", () => {
    sidebar.classList.toggle("open");
    menuBtnChange();
});

function menuBtnChange() {
    if (sidebar.classList.contains("open")) {
        closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");
    } else {
        closeBtn.classList.replace("bx-menu-alt-right", "bx-menu");
    }
}
</script>