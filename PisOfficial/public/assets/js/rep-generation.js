/**
 * Report Generation Page Logic
 */

window.showReportSection = function(sectionId, linkId) {
    // Hide all sections
    document.querySelectorAll('.report-section').forEach(s => s.classList.add('hidden'));
    
    const activeClasses = ['bg-red-600', 'text-white', 'shadow-lg', 'shadow-red-100', 'active-report-link'];
    const inactiveClasses = ['text-gray-400', 'hover:bg-gray-50', 'hover:text-gray-900', 'border-transparent', 'hover:border-gray-100'];
    
    // Deactivate all links
    document.querySelectorAll('.report-link').forEach(l => {
        l.classList.remove(...activeClasses);
        l.classList.add(...inactiveClasses);
    });

    // Show target section
    const sec = document.getElementById(sectionId);
    if(sec) sec.classList.remove('hidden');

    // Activate target link
    const lnk = document.getElementById(linkId);
    if(lnk) {
        lnk.classList.remove(...inactiveClasses);
        lnk.classList.add(...activeClasses);
    }

    // Persistence
    localStorage.setItem('activeReportSection', sectionId);
    localStorage.setItem('activeReportLink', linkId);
    
    // Optional: update URL cleanly without reloading
    const url = new URL(window.location);
    url.searchParams.set('section', sectionId);
    window.history.replaceState({}, '', url);
}

document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const urlSection = urlParams.get('section');
    const savedSection = localStorage.getItem('activeReportSection');
    const savedLink = localStorage.getItem('activeReportLink');
    
    if (urlSection) {
        // If URL says which section to show, find its link
        let targetLinkId = '';
        document.querySelectorAll('.report-link').forEach(l => {
            const onclickStr = l.getAttribute('onclick') || '';
            if(onclickStr.includes(urlSection)) {
                targetLinkId = l.id;
            }
        });
        if(targetLinkId) {
            showReportSection(urlSection, targetLinkId);
        } else {
            showReportSection('filedReportsSection', 'filedReportsLink');
        }
    } else if (savedSection && savedLink) {
        // Otherwise check local storage
        showReportSection(savedSection, savedLink);
    } else {
        // Default
        showReportSection('filedReportsSection', 'filedReportsLink');
    }
});
