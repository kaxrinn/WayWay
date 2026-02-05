#!/bin/bash

echo "=== DEBUG: Edit Request Check ==="
echo ""

# Check if tinker is available
php artisan tinker << 'EOF'

echo "1. Checking EditRequest table...\n";
$requests = \App\Models\EditRequest::all();
echo "Total requests: " . $requests->count() . "\n\n";

echo "2. Approved requests:\n";
$approved = \App\Models\EditRequest::where('status', 'approved')->get();
foreach ($approved as $req) {
    echo "  ID: {$req->id} | User: {$req->user_id} | Destinasi: {$req->destinasi_id} | Status: {$req->status} | Created: {$req->created_at}\n";
}
echo "\n";

echo "3. Check specific user (ID 8):\n";
$userRequests = \App\Models\EditRequest::where('user_id', 8)
    ->where('status', 'approved')
    ->get();
echo "User 8 approved requests: " . $userRequests->count() . "\n";
foreach ($userRequests as $req) {
    echo "  Destinasi ID: {$req->destinasi_id} | Created: {$req->created_at}\n";
}
echo "\n";

echo "4. Check if within 7 days:\n";
$recentApproved = \App\Models\EditRequest::where('user_id', 8)
    ->where('status', 'approved')
    ->where('created_at', '>=', now()->subDays(7))
    ->get();
echo "Recent (7 days) approved: " . $recentApproved->count() . "\n";
foreach ($recentApproved as $req) {
    echo "  Destinasi ID: {$req->destinasi_id} | Days ago: " . $req->created_at->diffInDays(now()) . "\n";
}

EOF

echo ""
echo "=== Debug Complete ==="
