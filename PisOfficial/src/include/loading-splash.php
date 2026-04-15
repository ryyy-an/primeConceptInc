<?php
if (!isset($_SESSION['login_success_splash']) || $_SESSION['login_success_splash'] !== true) {
    return;
}
// Clear flag so it only shows once
$_SESSION['login_success_splash'] = false;
?>
<div id="loading-splash" class="fixed inset-0 z-[10000] bg-white flex flex-col items-center justify-center transition-opacity duration-700">
    <div class="flex flex-col items-center translate-y-[15%]">
        <div class="relative w-32 h-32 flex items-center justify-center">
            <!-- Official Prime Concept Logo -->
            <div id="prime-logo-container" class="w-full h-full flex items-center justify-center">
                <img src="../../public/assets/img/primeLogo.ico"
                    alt="Prime Concept"
                    class="w-20 h-20 object-contain animate-logo-pulse brightness-110 drop-shadow-2xl">
            </div>

            <!-- Pulse Ring -->
            <div class="absolute inset-0 rounded-full border-4 border-red-500/10 animate-ping"></div>
        </div>

        <div class="mt-4 text-center min-w-[300px]">
            <h2 class="text-xl font-semibold text-gray-800 uppercase overflow-hidden h-8">
                <span id="loading-text" class="inline-block transition-transform duration-500 translate-y-full">Synchronizing...</span>
            </h2>
            <div class="w-40 h-1 bg-gray-100 rounded-full mt-2 mx-auto overflow-hidden">
                <div class="h-full bg-red-600 animate-loading-bar"></div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes logo-pulse {

        0%,
        100% {
            transform: scale(1);
            filter: brightness(1);
        }

        50% {
            transform: scale(1.1);
            filter: brightness(1.2);
        }
    }

    .animate-logo-pulse {
        animation: logo-pulse 2s infinite ease-in-out;
    }

    #loading-splash {
        background-color: #ffffff;
        z-index: 999999;
    }
</style>

<script>
    (function() {
        const text = document.getElementById('loading-text');
        const splash = document.getElementById('loading-splash');
        <?php
        $role = $_SESSION['role'] ?? 'staff';
        if ($role === 'admin') {
            $roleMsg = "Initializing Admin";
        } elseif ($role === 'showroom') {
            $roleMsg = "Preparing Showroom Catalog";
        } else {
            // Default message for Warehouse or others
            $roleMsg = "Readying Warehouse Inventory";
        }
        ?>
        const statusMessages = ["Optimizing Inventory", "<?= $roleMsg ?>", "Synchronizing Prime", "Loading Showroom"];

        if (!splash) return;

        let currentMsg = 0;

        const cycleText = () => {
            if (text) {
                text.style.transform = 'translateY(100%)';
                setTimeout(() => {
                    text.innerText = statusMessages[currentMsg % statusMessages.length] + "...";
                    text.style.transform = 'translateY(0)';
                }, 300);
            }
            currentMsg++;
        };

        cycleText();
        const interval = setInterval(cycleText, 1500);

        // Smooth exit
        window.addEventListener('load', () => {
            setTimeout(() => {
                clearInterval(interval);
                splash.classList.add('opacity-0');
                setTimeout(() => {
                    splash.remove();
                }, 700);
            }, 2500);
        });

        // Fallback
        setTimeout(() => {
            if (document.getElementById('loading-splash')) {
                clearInterval(interval);
                splash.classList.add('opacity-0');
                setTimeout(() => {
                    splash.remove();
                }, 700);
            }
        }, 5000);
    })();
</script>