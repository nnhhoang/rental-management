<?php

namespace App\Console\Commands;

use App\Models\ApartmentRoom;
use App\Models\RoomFeeCollection;
use App\Models\Tenant;
use App\Repositories\Eloquent\RoomFeeCollectionRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestQueryPerformanceCommand extends Command
{
    protected $signature = 'test:query-performance {query? : Type of query to test (all|monthly-stats|tenants|rooms|n+1)}';
    protected $description = 'Test the performance of important queries';

    public function handle()
    {
        $queryType = $this->argument('query') ?: 'all';
        
        $this->info('Starting query performance tests...');
        
        if ($queryType == 'all' || $queryType == 'monthly-stats') {
            $this->testMonthlyFeeStatistics();
        }
        
        if ($queryType == 'all' || $queryType == 'tenants') {
            $this->testTenantQueries();
        }
        
        if ($queryType == 'all' || $queryType == 'rooms') {
            $this->testRoomQueries();
        }
        
        if ($queryType == 'all' || $queryType == 'n+1') {
            $this->testN1Problem();
        }
        
        $this->info('Query performance testing completed!');
        
        return 0;
    }
    
    private function testMonthlyFeeStatistics()
    {
        $this->info('Testing getMonthlyFeeStatistics query...');
        
        $repository = new RoomFeeCollectionRepository(new RoomFeeCollection());
        $year = date('Y');
        
        if (Schema::hasTable('room_fee_collections')) {
            try {
                DB::statement('DROP INDEX room_fee_collections_charge_date_index ON room_fee_collections');
                $this->info('Removed charge_date index for testing');
            } catch (\Exception $e) {
                $this->info('Index does not exist or could not be removed: ' . $e->getMessage());
            }
        }
        
        $startTime = microtime(true);
        $repository->getMonthlyFeeStatistics($year);
        $originalTime = microtime(true) - $startTime;
        
        $this->info("Original query: {$originalTime} seconds");
        
        try {
            DB::statement('CREATE INDEX room_fee_collections_charge_date_index ON room_fee_collections(charge_date)');
            $this->info('Created charge_date index');
            
            $startTime = microtime(true);
            $repository->getMonthlyFeeStatistics($year);
            $indexedTime = microtime(true) - $startTime;
            
            $this->info("Query with index: {$indexedTime} seconds");
            $improvement = (($originalTime - $indexedTime) / $originalTime) * 100;
            $this->info("Improvement: " . round($improvement, 2) . "%");
        } catch (\Exception $e) {
            $this->error('Could not create index: ' . $e->getMessage());
        }
        
        try {
            $startTime = microtime(true);
            DB::table('room_fee_collections')
                ->select(
                    DB::raw('MONTH(charge_date) as month'),
                    DB::raw('SUM(total_price) as total_price'),
                    DB::raw('SUM(total_paid) as total_paid'),
                    DB::raw('SUM(total_price - total_paid) as total_debt')
                )
                ->whereYear('charge_date', $year)
                ->groupBy(DB::raw('MONTH(charge_date)'))
                ->orderBy(DB::raw('MONTH(charge_date)'))
                ->get();
            $optimizedTime = microtime(true) - $startTime;
            
            $this->info("Optimized query: {$optimizedTime} seconds");
            $improvement = (($originalTime - $optimizedTime) / $originalTime) * 100;
            $this->info("Improvement vs. original: " . round($improvement, 2) . "%");
        } catch (\Exception $e) {
            $this->error('Optimized query error: ' . $e->getMessage());
        }
    }
    
    private function testTenantQueries()
    {
        $this->info('Testing tenant listing by user query...');
        $userId = 1; // Sample user ID
        
        $startTime = microtime(true);
        $tenantsWithWhereHas = Tenant::whereHas('contracts.room.apartment', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
        $whereHasTime = microtime(true) - $startTime;
        
        $this->info("Query with whereHas: {$whereHasTime} seconds (count: " . count($tenantsWithWhereHas) . ")");
        
        // Using joins
        $startTime = microtime(true);
        $tenantsWithJoins = DB::table('tenants')
            ->join('tenant_contracts', 'tenants.id', '=', 'tenant_contracts.tenant_id')
            ->join('apartment_rooms', 'tenant_contracts.apartment_room_id', '=', 'apartment_rooms.id')
            ->join('apartments', 'apartment_rooms.apartment_id', '=', 'apartments.id')
            ->where('apartments.user_id', $userId)
            ->select('tenants.*')
            ->distinct()
            ->get();
        $joinsTime = microtime(true) - $startTime;
        
        $this->info("Query with joins: {$joinsTime} seconds (count: " . count($tenantsWithJoins) . ")");
        
        if ($whereHasTime > 0) {
            $improvement = (($whereHasTime - $joinsTime) / $whereHasTime) * 100;
            $this->info("Improvement: " . round($improvement, 2) . "%");
        } else {
            $this->info("Could not calculate improvement (division by zero)");
        }
    }
    
    private function testRoomQueries()
    {
        $this->info('Testing rooms with active contracts query...');
        
        // Remove index if exists
        try {
            DB::statement('DROP INDEX tenant_contracts_end_date_index ON tenant_contracts');
            $this->info('Removed end_date index for testing');
        } catch (\Exception $e) {
            $this->info('Index does not exist or could not be removed: ' . $e->getMessage());
        }
        
        // Test without index
        $startTime = microtime(true);
        $roomsWithContract = ApartmentRoom::whereHas('contracts', function ($query) {
            $query->whereNull('end_date')
                ->orWhere('end_date', '>', now());
        })->get();
        $noIndexTime = microtime(true) - $startTime;
        
        $this->info("Query without index: {$noIndexTime} seconds (count: " . count($roomsWithContract) . ")");
        
        // Add index and test again
        try {
            DB::statement('CREATE INDEX tenant_contracts_end_date_index ON tenant_contracts(end_date)');
            $this->info('Created end_date index');
            
            $startTime = microtime(true);
            $roomsWithContract = ApartmentRoom::whereHas('contracts', function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            })->get();
            $indexedTime = microtime(true) - $startTime;
            
            $this->info("Query with index: {$indexedTime} seconds (count: " . count($roomsWithContract) . ")");
            
            if ($noIndexTime > 0) {
                $improvement = (($noIndexTime - $indexedTime) / $noIndexTime) * 100;
                $this->info("Improvement: " . round($improvement, 2) . "%");
            } else {
                $this->info("Could not calculate improvement (division by zero)");
            }
        } catch (\Exception $e) {
            $this->error('Could not create index: ' . $e->getMessage());
        }
        
        // Test with subquery approach
        $startTime = microtime(true);
        $contractRoomIds = DB::table('tenant_contracts')
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            })
            ->select('apartment_room_id')
            ->distinct()
            ->pluck('apartment_room_id')
            ->toArray();
            
        $roomsWithContract = ApartmentRoom::whereIn('id', $contractRoomIds)->get();
        $subqueryTime = microtime(true) - $startTime;
        
        $this->info("Query with subquery: {$subqueryTime} seconds (count: " . count($roomsWithContract) . ")");
        
        if ($noIndexTime > 0) {
            $improvement = (($noIndexTime - $subqueryTime) / $noIndexTime) * 100;
            $this->info("Improvement vs. original: " . round($improvement, 2) . "%");
        } else {
            $this->info("Could not calculate improvement (division by zero)");
        }
    }
    
    private function testN1Problem()
    {
        $this->info('Testing N+1 problem...');
        
        // Without eager loading
        $startTime = microtime(true);
        $rooms = ApartmentRoom::take(50)->get();
        foreach ($rooms as $room) {
            $apartment = $room->apartment;
            $name = $apartment->name; 
        }
        $notEagerTime = microtime(true) - $startTime;
        
        $this->info("Without eager loading: {$notEagerTime} seconds");
        
        // With eager loading
        $startTime = microtime(true);
        $rooms = ApartmentRoom::with('apartment')->take(50)->get();
        foreach ($rooms as $room) {
            $apartment = $room->apartment;
            $name = $apartment->name;
        }
        $eagerTime = microtime(true) - $startTime;
        
        $this->info("With eager loading: {$eagerTime} seconds");
        
        if ($notEagerTime > 0) {
            $improvement = (($notEagerTime - $eagerTime) / $notEagerTime) * 100;
            $this->info("Improvement: " . round($improvement, 2) . "%");
        } else {
            $this->info("Could not calculate improvement (division by zero)");
        }
        
        // Count executed queries
        DB::enableQueryLog();
        
        // Reset query log
        DB::flushQueryLog();
        
        $rooms = ApartmentRoom::take(20)->get();
        foreach ($rooms as $room) {
            $apartment = $room->apartment;
            $name = $apartment->name;
        }
        
        $notEagerQueries = count(DB::getQueryLog());
        
        // Reset query log
        DB::flushQueryLog();
        
        $rooms = ApartmentRoom::with('apartment')->take(20)->get();
        foreach ($rooms as $room) {
            $apartment = $room->apartment;
            $name = $apartment->name;
        }
        
        $eagerQueries = count(DB::getQueryLog());
        
        DB::disableQueryLog();
        
        $this->info("Number of queries without eager loading: {$notEagerQueries}");
        $this->info("Number of queries with eager loading: {$eagerQueries}");
        $this->info("Query reduction: " . ($notEagerQueries - $eagerQueries));
    }
}