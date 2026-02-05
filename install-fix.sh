#!/bin/bash

echo "Ì¥ß WayWay Quick Fix Script"
echo "=========================="
echo ""

# Clear all caches
echo "Ì∑π Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "‚úÖ Caches cleared!"
echo ""

# Assign Basic paket to users without paket
echo "Ì±• Fixing users without paket..."
php artisan tinker --execute="
\$basic = \App\Models\PaketPromosi::where('nama_paket', 'Basic')->first();
if (\$basic) {
    \$updated = \App\Models\User::where('role', 'pemilik_wisata')
        ->whereNull('current_paket_id')
        ->update(['current_paket_id' => \$basic->id]);
    echo \"‚úÖ Fixed \$updated users\n\";
} else {
    echo \"‚ùå Basic paket not found! Run seeder first.\n\";
}
"

echo ""
echo "‚úÖ Quick fix completed!"
echo ""
echo "Now test:"
echo "1. php artisan serve"
echo "2. Login as pemilik wisata"
echo "3. All pages should work now!"

