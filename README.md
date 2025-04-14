Live Auction Application Documentation

Project Setup Instructions
1. Install Laravel 11
    composer create-project laravel/laravel live-auction
2)PHP Version: 8.2.26
3)Composer: Installed
4)Node.js/NPM: Installed

Install Laravel Breeze (for Auth scaffolding)
composer require laravel/breeze --dev
php artisan breeze:install
npm install && npm run dev

Set Up the Project:
php artisan migrate
php artisan db:seed  # Seeds admin credentials
php artisan serve

Admin Panel Features : 
Admin login with seeded credentials.

Admin Panel Features
Admin login with seeded credentials.

Admin can:
Create, view, edit, and delete products.
Upload multiple images per product.
View all products in dashboard.
View bid history and user details.
See auction winners and payment status.
Engage in real-time chat with bidders using Pusher.
Real-time chat is integrated using Pusher.

Pusher Configuration
Install Pusher PHP SDK:
composer require pusher/pusher-php-server

.env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID="your-pusher-app-id"
PUSHER_APP_KEY="your-pusher-key"
PUSHER_APP_SECRET="your-pusher-secret"
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME="https"
PUSHER_APP_CLUSTER="ap2"

Add to config/broadcasting.php:
Make sure the pusher connection is correctly configured.

Queue Configuration (for broadcasting with ShouldBroadcast)
php artisan queue:work

.env
QUEUE_CONNECTION=database

Bidder Panel Features
Can view available live auction products.
Real-time live stream video support.
Real-time chat with admin via Pusher.
Real-time bid updates and notifications.
Notification icon shows live notifications when outbid.
notifications can be deleted.
"Show Bidders" button reveals all bidders and their bid history.
Bid button disabled when auction ends.

Bidding Logic & Validations
A bid must be greater than the current price.
Cannot place the same bid amount again.
A user must wait at least 5 seconds between bids.
When auction ends, bid button is disabled.
If a bid is placed when end time < 2 minutes, the time is automatically extended by 2 minutes.

Auction Result
When the auction ends, the user with the highest bid is marked as the Winner.
The Winner badge is shown to both the admin and all users.
Only the winner sees the "Pay Now" button to complete payment via Razorpay.

additional features
Payment Integration (Razorpay)
Setup Razorpay:
composer require razorpay/razorpay

Add to .env or config/services.php:
RAZORPAY_KEY=your_key
RAZORPAY_SECRET=your_secret

Flow:
Only the winner can click "Pay Now".
Clicking "Pay Now" generates a unique order ID using Razorpay.
User is redirected to Razorpay's test gateway.
On successful payment:
Redirect to confirmation screen.
"Pay Now" becomes disabled.
Badge "Paid" appears.
If not paid, badge shows "Unpaid" in both admin and user view.

Security:
Only the order ID is passed via URL (not product ID).
Payment option is only available after auction ends.

Final Notes
Ensure Pusher credentials and cluster are correct.
Run php artisan queue:work for real-time broadcasting if not using ShouldBroadcastNow.
Use Razorpay test credentials for development mode.
All real-time updates (bids, notifications, chat) work via Pusher.




