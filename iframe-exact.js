/**
 * iframe-exact.js - Forces exact iframe height with no extra space
 */
(function() {
    // Function to get the exact height of visible content
    function getExactContentHeight() {
        const wrapper = document.querySelector('.content-wrapper');
        if (!wrapper) return 0;
        
        // Try to find the last table row for more precise measurement
        const lastTableRow = document.querySelector('#attendanceTable tbody tr:last-child');
        if (lastTableRow) {
            const tableBottom = lastTableRow.offsetTop + lastTableRow.offsetHeight;
            const tableContainer = document.querySelector('.table-responsive');
            const containerOffset = tableContainer.offsetTop;
            return containerOffset + tableBottom + 5; // Just 5px buffer
        }
        
        // Fallback: Get all direct children of the wrapper
        const children = wrapper.children;
        let totalHeight = 0;
        
        // If there are children, calculate based on last visible child position
        if (children.length > 0) {
            const lastChild = children[children.length - 1];
            totalHeight = lastChild.offsetTop + lastChild.offsetHeight;
        } else {
            // Fallback to wrapper height
            totalHeight = wrapper.offsetHeight;
        }
        
        return totalHeight;
    }
    
    // Send the exact height to parent
    function sendExactHeight() {
        const height = getExactContentHeight();
        if (window.parent && height > 0) {
            window.parent.postMessage({
                type: 'resize',
                height: height
            }, '*');
        }
    }
    
    // Run on load and after a short delay
    window.addEventListener('load', function() {
        sendExactHeight();
        // Try multiple times to catch any layout shifts
        setTimeout(sendExactHeight, 100);
        setTimeout(sendExactHeight, 300);
        setTimeout(sendExactHeight, 500);
    });
    
    // Run on DOM ready
    document.addEventListener('DOMContentLoaded', sendExactHeight);
    
    // Run on any content changes
    const observer = new MutationObserver(sendExactHeight);
    observer.observe(document.body, { 
        childList: true, 
        subtree: true,
        attributes: true
    });
})(); 