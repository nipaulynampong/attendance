/**
 * iframe-resize.js
 * Handles resizing the iframe to fit content without creating double scrollbars
 */

// Handle iframe resizing communication with parent
function sendHeightToParent() {
    // Get the main content container
    const mainContent = document.querySelector('.content-wrapper');
    
    if (mainContent) {
        // Calculate the exact height with no buffer
        const height = mainContent.offsetHeight;
        
        // Send message to parent with the height
        if (window.parent) {
            window.parent.postMessage({
                type: 'resize',
                height: height
            }, '*');
        }
    }
}

// Listen for height requests from parent
window.addEventListener('message', function(event) {
    if (event.data.type === 'requestHeight') {
        sendHeightToParent();
    }
});

// Send height when the page loads and whenever content changes
window.addEventListener('load', function() {
    // Initial calculation
    sendHeightToParent();
    
    // Second calculation after a delay to ensure all content is rendered
    setTimeout(sendHeightToParent, 300);
});
window.addEventListener('resize', sendHeightToParent);
window.addEventListener('DOMContentLoaded', sendHeightToParent);

// Update height occasionally to handle dynamic content changes
let heightInterval = setInterval(sendHeightToParent, 2000);

// Stop interval after 10 seconds to prevent continual updates on static pages
setTimeout(function() {
    clearInterval(heightInterval);
    // One final update
    sendHeightToParent();
}, 10000);

// Additional event listeners for common DOM changes that might affect height
const observer = new MutationObserver(function(mutations) {
    sendHeightToParent();
});

// Start observing the document body for DOM changes
observer.observe(document.body, { 
    childList: true,
    subtree: true,
    attributes: true,
    characterData: true
}); 