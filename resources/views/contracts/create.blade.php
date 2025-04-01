<!-- Add authentication check script at the top -->
<script>
    // Check if we have an auth token
    document.addEventListener('DOMContentLoaded', function() {
        if (!localStorage.getItem('auth_token')) {
            console.log('No auth token found, attempting to obtain one');
            // If no auth token, try to get one
            fetch('/api/auth/get-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    console.error('Token fetch failed with status:', response.status);
                    // Try to get the error details from the response
                    return response.json().then(data => {
                        throw new Error(data.message || 'Authentication failed');
                    }).catch(() => {
                        throw new Error(`Authentication failed with status ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.data && data.data.token) {
                    localStorage.setItem('auth_token', data.data.token);
                    console.log('API token obtained successfully');
                } else {
                    console.error('Token response did not contain expected data:', data);
                    throw new Error('Invalid token response format');
                }
            })
            .catch(error => {
                console.error('Error obtaining API token:', error);
                
                // Check if we're in a cycle of redirects
                const redirectCount = parseInt(sessionStorage.getItem('auth_redirect_count') || '0', 10);
                if (redirectCount > 2) {
                    alert('Login system error. Please contact support.');
                    sessionStorage.removeItem('auth_redirect_count');
                    return;
                }
                
                sessionStorage.setItem('auth_redirect_count', (redirectCount + 1).toString());
                alert('Your session has expired. Please log in again.');
                window.location.href = '/login';
            });
        } else {
            console.log('Using existing auth token');
            // Reset redirect counter since we have a token
            sessionStorage.removeItem('auth_redirect_count');
        }
    });
</script>

@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-8">
        <contract-form></contract-form>
    </div>
@endsection