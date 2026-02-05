#!/bin/bash

echo "íº€ WayWay Paket System - Auto Installation Script"
echo "=================================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Run Migrations
echo -e "${YELLOW}í³¦ Step 1: Running migrations...${NC}"
php artisan migrate --force

if [ $? -eq 0 ]; then
    echo -e "${GREEN}âœ… Migrations completed successfully!${NC}"
else
    echo -e "${RED}âŒ Migration failed!${NC}"
    exit 1
fi

echo ""

# Step 2: Seed Paket Promosi
echo -e "${YELLOW}í¼± Step 2: Seeding Paket Promosi...${NC}"
php artisan db:seed --class=PaketPromosiSeeder --force

if [ $? -eq 0 ]; then
    echo -e "${GREEN}âœ… Paket Promosi seeded successfully!${NC}"
else
    echo -e "${RED}âŒ Seeding failed!${NC}"
    exit 1
fi

echo ""

# Step 3: Assign Basic Paket to Existing Users
echo -e "${YELLOW}í±¥ Step 3: Assigning Basic Paket to existing pemilik wisata...${NC}"
php artisan tinker --execute="
    \$basic = \App\Models\PaketPromosi::where('nama_paket', 'Basic')->first();
    if (\$basic) {
        \$updated = \App\Models\User::where('role', 'pemilik_wisata')
            ->whereNull('current_paket_id')
            ->update(['current_paket_id' => \$basic->id]);
        echo \"âœ… Updated \$updated users with Basic paket\n\";
    } else {
        echo \"âŒ Basic paket not found!\n\";
    }
"

echo ""

# Step 4: Create Storage Link
echo -e "${YELLOW}í´— Step 4: Creating storage link...${NC}"
php artisan storage:link

if [ $? -eq 0 ]; then
    echo -e "${GREEN}âœ… Storage link created!${NC}"
else
    echo -e "${YELLOW}âš ï¸  Storage link might already exist${NC}"
fi

echo ""

# Step 5: Clear All Caches
echo -e "${YELLOW}í·¹ Step 5: Clearing caches...${NC}"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo -e "${GREEN}âœ… All caches cleared!${NC}"
echo ""

# Step 6: Generate New Caches
echo -e "${YELLOW}âš¡ Step 6: Generating caches...${NC}"
php artisan config:cache
php artisan route:cache

echo -e "${GREEN}âœ… Caches regenerated!${NC}"
echo ""

# Final Summary
echo ""
echo "=================================================="
echo -e "${GREEN}í¾‰ Installation Completed Successfully!${NC}"
echo "=================================================="
echo ""
echo "Next steps:"
echo "1. Start server: php artisan serve"
echo "2. Login as pemilik wisata"
echo "3. Access: http://localhost:8000/pemilik/dashboard"
echo ""
echo "Default test credentials:"
echo "Email: pemilik@test.com"
echo "Password: password"
echo ""
echo "To create test user, run:"
echo "php artisan tinker"
echo "Then paste:"
echo "\App\Models\User::create([
    'name' => 'Test Pemilik',
    'email' => 'pemilik@test.com',
    'password' => bcrypt('password'),
    'role' => 'pemilik_wisata',
    'current_paket_id' => 1,
    'no_telepon' => '08123456789',
]);"
echo ""
