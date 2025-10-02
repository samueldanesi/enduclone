<?php
/**
 * Componente Navbar - Barra davigazione unificata
 * Questo componente deve essere incluso in tutte le pagine per mantenere la consistenza
 */

function renderNavbar($active_section = '') {
    // Determina la pagina corrente per evidenziare il menu attivo
    $current_page = basename($_SERVER['REQUEST_URI'], '?' . ($_SERVER['QUERY_STRING'] ?? ''));
    $current_page = trim($current_page, '/');
    
    // Se è specificata una sezione attiva, usala
    if (!empty($active_section)) {
        $current_page = $active_section;
    }
?>

<nav class="navbar">
    <div class="glass-card">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 0;">
                <a href="/" style="text-decoration: none;">
                    <h2 class="gradient-text" style="font-size: 1.8rem; font-weight: 700;">
                        <i class="fas fa-running" style="margin-right: 10px;"></i>SportEvents
                    </h2>
                </a>
                
                <div style="display: flex; gap: 25px; align-items: center;">
                    <a href="/" style="color: <?= $current_page === '' || $current_page === 'home' ? '#667eea' : '#666' ?>; text-decoration: none; font-weight: <?= $current_page === '' || $current_page === 'home' ? '600' : '500' ?>; transition: color 0.3s;">
                        <i class="fas fa-home" style="margin-right: 8px;"></i>Home
                    </a>
                    <a href="/events" style="color: <?= $current_page === 'events' ? '#667eea' : '#666' ?>; text-decoration: none; font-weight: <?= $current_page === 'events' ? '600' : '500' ?>; transition: color 0.3s;">
                        <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i>Eventi
                    </a>
                    <a href="/community" style="color: <?= $current_page === 'community' ? '#667eea' : '#666' ?>; text-decoration: none; font-weight: <?= $current_page === 'community' ? '600' : '500' ?>; transition: color 0.3s;">
                        <i class="fas fa-users" style="margin-right: 8px;"></i>Community
                    </a>
                    <a href="/teams" style="color: <?= $current_page === 'teams' ? '#667eea' : '#666' ?>; text-decoration: none; font-weight: <?= $current_page === 'teams' ? '600' : '500' ?>; transition: color 0.3s;">
                        <i class="fas fa-user-friends" style="margin-right: 8px;"></i>Team
                    </a>
                    <a href="/shop" style="color: <?= $current_page === 'shop' ? '#667eea' : '#666' ?>; text-decoration: none; font-weight: <?= $current_page === 'shop' ? '600' : '500' ?>; transition: color 0.3s;">
                        <i class="fas fa-shopping-bag" style="margin-right: 8px;"></i>Shop
                    </a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['user_type'] === 'organizer'): ?>
                            <a href="/organizer" style="color: <?= $current_page === 'organizer' ? '#667eea' : '#666' ?>; text-decoration: none; font-weight: <?= $current_page === 'organizer' ? '600' : '500' ?>; transition: color 0.3s;">
                                <i class="fas fa-chart-line" style="margin-right: 8px;"></i>Dashboard
                            </a>
                        <?php endif; ?>
                        
                        <!-- Notifiche -->
                        <div style="position: relative;">
                            <a href="/notifications" style="color: <?= $current_page === 'notifications' ? '#667eea' : '#666' ?>; text-decoration: none; font-weight: 500; position: relative;">
                                <i class="fas fa-bell" style="margin-right: 8px;"></i>
                                <span id="notification-count" style="position: absolute; top: -8px; right: -8px; background: #f093fb; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; min-width: 18px; text-align: center; display: none;"></span>
                            </a>
                        </div>
                        
                        <!-- Menu Profilo -->
                        <div style="position: relative; display: inline-block;">
                            <button onclick="toggleProfileMenu()" class="btn btn-primary" style="flex: none; padding: 10px 20px; position: relative;">
                                <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['nome'] ?? 'Utente') ?>
                                <i class="fas fa-chevron-down" style="margin-left: 8px; font-size: 12px;"></i>
                            </button>
                            <div id="profileMenu" style="display: none; position: absolute; top: 100%; right: 0; background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); min-width: 200px; z-index: 1000; margin-top: 10px;">
                                <a href="/profile" style="display: block; padding: 15px 20px; color: #333; text-decoration: none; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <i class="fas fa-user-circle" style="margin-right: 10px;"></i>Il Mio Profilo
                                </a>
                                <a href="/messages" style="display: block; padding: 15px 20px; color: #333; text-decoration: none; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <i class="fas fa-envelope" style="margin-right: 10px;"></i>Messaggi
                                </a>
                                <a href="/events?my=true" style="display: block; padding: 15px 20px; color: #333; text-decoration: none; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <i class="fas fa-list" style="margin-right: 10px;"></i>Le Mie Iscrizioni
                                </a>
                                <a href="/shop/orders" style="display: block; padding: 15px 20px; color: #333; text-decoration: none; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <i class="fas fa-shopping-cart" style="margin-right: 10px;"></i>I Miei Ordini
                                </a>
                                <a href="/about" style="display: block; padding: 15px 20px; color: #333; text-decoration: none; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <i class="fas fa-info-circle" style="margin-right: 10px;"></i>Chi Siamo
                                </a>
                                <a href="/contact" style="display: block; padding: 15px 20px; color: #333; text-decoration: none; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <i class="fas fa-phone" style="margin-right: 10px;"></i>Contatti
                                </a>
                                <a href="/logout" style="display: block; padding: 15px 20px; color: #f5576c; text-decoration: none;">
                                    <i class="fas fa-sign-out-alt" style="margin-right: 10px;"></i>Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/about" style="color: <?= $current_page === 'about' ? '#667eea' : '#666' ?>; text-decoration: none; font-weight: <?= $current_page === 'about' ? '600' : '500' ?>; transition: color 0.3s;">
                            <i class="fas fa-info-circle" style="margin-right: 8px;"></i>Chi Siamo
                        </a>
                        <a href="/contact" style="color: <?= $current_page === 'contact' ? '#667eea' : '#666' ?>; text-decoration: none; font-weight: <?= $current_page === 'contact' ? '600' : '500' ?>; transition: color 0.3s;">
                            <i class="fas fa-phone" style="margin-right: 8px;"></i>Contatti
                        </a>
                        <a href="/register" class="btn btn-secondary" style="flex: none; padding: 10px 20px; margin-right: 10px;">
                            <i class="fas fa-user-plus"></i> Registrati
                        </a>
                        <a href="/login" class="btn btn-primary" style="flex: none; padding: 10px 20px;">
                            <i class="fas fa-sign-in-alt"></i> Accedi
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Stili CSS per la navbar -->
<style>
.navbar {
    position: sticky;
    top: 20px;
    z-index: 1000;
    margin: 20px 20px 0;
    width: calc(100% - 40px);
    box-sizing: border-box;
}

.navbar a:hover {
    color: #667eea !important;
    transform: translateY(-1px);
}

#profileMenu a:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea !important;
}

.glass-card {
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    backdrop-filter: blur(20px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    transition: all 0.3s ease;
}

.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

.btn {
    padding: 12px 20px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.btn-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
}

@media (max-width: 768px) {
    .navbar {
        margin: 15px 15px 0;
    }
    
    .container > div {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .container > div > div {
        flex-wrap: wrap;
        justify-content: center;
    }
}
</style>

<!-- JavaScript per la navbar -->
<script>
// Gestione menu profilo a tendina
function toggleProfileMenu() {
    const menu = document.getElementById('profileMenu');
    menu.style.display = menu.style.display === 'none' || menu.style.display === '' ? 'block' : 'none';
}

// Chiudi menu se si clicca fuori
document.addEventListener('click', function(event) {
    const menu = document.getElementById('profileMenu');
    const button = event.target.closest('[onclick*="toggleProfileMenu"]');
    
    if (!button && menu) {
        menu.style.display = 'none';
    }
});

// Carica conteggio notifiche
function loadNotificationCount() {
    <?php if (isset($_SESSION['user_id'])): ?>
    fetch('/api/notifications/count')
        .then(response => response.json())
        .then(data => {
            const countElement = document.getElementById('notification-count');
            if (data.count > 0) {
                countElement.textContent = data.count > 99 ? '99+' : data.count;
                countElement.style.display = 'block';
            } else {
                countElement.style.display = 'none';
            }
        })
        .catch(error => console.log('Info: Sistema notifiche non ancora implementato'));
    <?php endif; ?>
}

// Carica notifiche all'avvio
document.addEventListener('DOMContentLoaded', function() {
    loadNotificationCount();
    // Aggiorna ogni 30 secondi
    setInterval(loadNotificationCount, 30000);
});
</script>
<?php
} // Fine della funzione renderNavbar()
?>