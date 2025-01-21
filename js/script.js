

function redirect(url) {
    window.location.href = url;
}

var offlinePageUrl = "/bookingapp/no_internet_connection.php";

// Function to handle internet connection status
function checkInternetConnection() {
    if (!navigator.onLine) {
        // Save the current page URL to localStorage
        localStorage.setItem("lastVisitedPage", window.location.href);
        // Redirect to the "No Internet Connection" page
        window.location.href = offlinePageUrl;
    }
}

// Add event listeners for network status changes
window.addEventListener("online", () => {
    // Redirect back to the last visited page if it exists
    const lastVisitedPage = localStorage.getItem("lastVisitedPage");
    if (lastVisitedPage && lastVisitedPage !== offlinePageUrl) {
        localStorage.removeItem("lastVisitedPage"); // Clear the stored page
        window.location.href = lastVisitedPage; // Redirect back
    }
});

// checkInternetConnection();