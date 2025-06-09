document.addEventListener('DOMContentLoaded', function() {
    const shortenForm = document.getElementById('shorten-form');
    const longUrlInput = document.getElementById('long-url');
    const shortenBtn = document.getElementById('shorten-btn');
    const resultDiv = document.getElementById('result');
    const shortUrlInput = document.getElementById('short-url');
    const copyBtn = document.getElementById('copy-btn');

    // Handle form submission
    shortenForm.addEventListener('submit', function(e) {
        e.preventDefault();
        shortenBtn.disabled = true;
        shortenBtn.textContent = 'Shortening...';
        
        const longUrl = longUrlInput.value.trim();
        
        fetch('shorten.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'long_url=' + encodeURIComponent(longUrl)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                shortUrlInput.value = data.short_url;
                resultDiv.classList.remove('hidden');
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        })
        .finally(() => {
            shortenBtn.disabled = false;
            shortenBtn.textContent = 'Shorten';
        });
    });

    // Copy to clipboard
    copyBtn.addEventListener('click', function() {
        shortUrlInput.select();
        document.execCommand('copy');
        
        // Show feedback
        const originalIcon = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        setTimeout(() => {
            copyBtn.innerHTML = originalIcon;
        }, 2000);
    });

    // Check for URL parameters (errors)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        const error = urlParams.get('error');
        let errorMsg = 'An error occurred.';
        
        if (error === 'invalid_link') {
            errorMsg = 'The shortened link is invalid or expired.';
        } else if (error === 'database_error') {
            errorMsg = 'A database error occurred. Please try again.';
        }
        
        alert(errorMsg);
    }
});
