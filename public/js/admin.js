// Admin dashboard specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Fix for dashboard card links - ensures cards link to their intended destination
    const dashboardCards = document.querySelectorAll('.dashboard-card');
    
    // Remove any existing event listeners by cloning and replacing
    dashboardCards.forEach(card => {
        const newCard = card.cloneNode(true);
        card.parentNode.replaceChild(newCard, card);
    });
    
    // Add new clean event listeners
    document.querySelectorAll('.dashboard-card').forEach(card => {
        card.style.cursor = 'pointer'; // Visual indication that it's clickable
        
        card.addEventListener('click', function(event) {
            // If the clicked element is a link, let the link handle it
            if (event.target.tagName === 'A' || event.target.closest('a')) {
                return;
            }
            
            // Get the link destination from data-href or the stretched-link
            const href = this.getAttribute('data-href');
            const link = this.querySelector('.stretched-link');
            
            if (href) {
                event.stopPropagation();
                window.location.href = href;
            } else if (link && link.href) {
                event.stopPropagation();
                window.location.href = link.href;
            }
        });
    });
});
