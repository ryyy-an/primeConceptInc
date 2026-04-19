/**
 * Loading Splash Interface Logic
 */
(function() {
    document.addEventListener('DOMContentLoaded', () => {
        const splash = document.getElementById('loading-splash');
        if (!splash) return;

        const text = document.getElementById('loading-text');
        const roleMsg = splash.getAttribute('data-role-msg') || "Readying Account Modules";
        
        // Sequence for professional feel
        const statusMessages = ["Synchronizing Prime", roleMsg, "Optimizing Modules", "Finalizing Interface"];
        let index = 0;

        function cycleText() {
            if (!text) return;
            
            // Subtle Fade effect for text transitions
            text.style.opacity = '0';
            setTimeout(() => {
                index = (index + 1) % statusMessages.length;
                text.innerText = statusMessages[index];
                text.style.opacity = '1';
            }, 600);
        }

        // Cycle slower for premium feel
        const interval = setInterval(cycleText, 2500);
        
        // Text initial style
        if(text) {
            text.style.transition = 'opacity 0.6s ease-in-out';
            text.innerText = statusMessages[0];
            text.style.opacity = '1';
        }

        window.addEventListener('load', () => {
            // Allow user to see the splash for a short moment
            setTimeout(() => {
                clearInterval(interval);
                if (splash) {
                    splash.style.opacity = '0';
                    splash.style.pointerEvents = 'none';
                    setTimeout(() => { 
                        splash.style.display = 'none'; 
                    }, 800);
                }
            }, 3500);
        });
    });
})();
