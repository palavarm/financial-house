## Financial House API Test Task

I use below endpoints in my task

- /api/v3/merchant/user/login
- /api/v3/transactions/report
- /api/v3/transaction/list
- /api/v3/transaction
- /api/v3/client

## Tech Stack
 
- Laravel 11
- Livewire 3.5
- PHP 8.2

## Pages

- There are four pages, Transaction Report, Transaction List, Transaction Details and Client Details
- In transaction report page I use 4 params, fromDate, toDate, merchantId and acquirer and when these values change, it sends ajax request to API without refreshing page
- In transaction list page there are 4 params as well, fromDate, toDate, status and paymentMethod, it works same way with report page
- In transaction list page you can go to transaction details page with clicking View links
- In transaction details page you can to the client details by clicking See Client link
- I also tried to use filters like customerEmail in transaction list page, but it didn't work and I removed them; I tried to send the filter params as in the API documentation

## Notes
- You can reach unit and feature tests under app\Packages\RpdPayment\Tests
- I used Pest for testing
