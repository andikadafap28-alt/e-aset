<?php

namespace Tests\Feature;

use App\Models\StockOpnamePersediaan;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StockOpnamePersediaanTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('users')) {
            Artisan::call('migrate', [
                '--path' => 'database/migrations/0001_01_01_000000_create_users_table.php',
            ]);
            Artisan::call('migrate', [
                '--path' => 'database/migrations/2026_05_19_235615_add_role_to_users_table.php',
            ]);
            Artisan::call('migrate', [
                '--path' => 'database/migrations/2026_10_29_173258_create_stock_opname_persediaans_table.php',
            ]);
        }
    }

    private function createAuthenticatedUser(): User
    {
        return User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    public function test_rekening_summary_list_splits_obat_and_bmhp_per_category(): void
    {
        $record = new StockOpnamePersediaan([
            'tanggal_opname' => '2026-10-27',
            'atk' => 1000000,
            'bahan_cetak' => 500000,
            'benda_pos' => 0,
            'bahan_komputer' => 250000,
            'obat_blud' => 1100000,
            'bmhp_blud' => 2200000,
            'obat_apbd' => 3300000,
            'bmhp_apbd' => 4400000,
            'obat_apbn' => 5500000,
            'bmhp_apbn' => 6600000,
        ]);

        $summary = $record->getRekeningSummaryList();
        $byKey = collect($summary)->keyBy('key');

        $this->assertTrue($byKey->has('obat_blud'));
        $this->assertTrue($byKey->has('bmhp_blud'));
        $this->assertTrue($byKey->has('obat_apbd'));
        $this->assertTrue($byKey->has('bmhp_apbd'));
        $this->assertTrue($byKey->has('obat_apbn'));
        $this->assertTrue($byKey->has('bmhp_apbn'));

        $this->assertSame('Obat BLUD', $byKey['obat_blud']['name']);
        $this->assertSame(1100000.0, $byKey['obat_blud']['val']);
        $this->assertSame('BMHP BLUD', $byKey['bmhp_blud']['name']);
        $this->assertSame(2200000.0, $byKey['bmhp_blud']['val']);

        $this->assertSame('Obat APBD', $byKey['obat_apbd']['name']);
        $this->assertSame(3300000.0, $byKey['obat_apbd']['val']);
        $this->assertSame('BMHP APBD', $byKey['bmhp_apbd']['name']);
        $this->assertSame(4400000.0, $byKey['bmhp_apbd']['val']);

        $this->assertSame('Obat APBN', $byKey['obat_apbn']['name']);
        $this->assertSame(5500000.0, $byKey['obat_apbn']['val']);
        $this->assertSame('BMHP APBN', $byKey['bmhp_apbn']['name']);
        $this->assertSame(6600000.0, $byKey['bmhp_apbn']['val']);

        $this->assertSame(24850000.0, $record->total_nilai);
    }

    public function test_stock_opname_persediaan_views_and_print_show_split_obat_and_bmhp(): void
    {
        $user = $this->createAuthenticatedUser();
        $this->actingAs($user);

        $record = StockOpnamePersediaan::create([
            'nomor_surat' => '000.2.1.2/    /413.102.5.18/2026',
            'tanggal_opname' => '2026-10-27',
            'atk' => 1000000,
            'bahan_cetak' => 500000,
            'benda_pos' => 0,
            'bahan_komputer' => 250000,
            'obat_blud' => 1111111,
            'bmhp_blud' => 2222222,
            'obat_apbd' => 3333333,
            'bmhp_apbd' => 4444444,
            'obat_apbn' => 5555555,
            'bmhp_apbn' => 6666666,
        ]);

        // Index page shows all 6 split rows
        $indexRes = $this->get(route('ba-stock-opname.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Obat BLUD');
        $indexRes->assertSee('BMHP BLUD');
        $indexRes->assertSee('1.111.111,00');
        $indexRes->assertSee('2.222.222,00');
        $indexRes->assertSee('Obat APBD');
        $indexRes->assertSee('BMHP APBD');
        $indexRes->assertSee('3.333.333,00');
        $indexRes->assertSee('4.444.444,00');
        $indexRes->assertSee('Obat APBN');
        $indexRes->assertSee('BMHP APBN');
        $indexRes->assertSee('5.555.555,00');
        $indexRes->assertSee('6.666.666,00');

        // Print page renders Page 1 (formatHal1Rekap) & Page 2 (lampiranTabelRingkas & lampiranTabelRinci)
        $printRes = $this->get(route('ba-stock-opname.show', $record->id));
        $printRes->assertStatus(200);
        $printHtml = $printRes->getContent();

        // Check lampiranTabelRingkas contains 10 split rows with individual values
        $this->assertStringContainsString('id="lampiranTabelRingkas"', $printHtml);
        $this->assertStringContainsString('<td><strong>Obat BLUD</strong></td>', $printHtml);
        $this->assertStringContainsString('<td><strong>BMHP BLUD</strong></td>', $printHtml);
        $this->assertStringContainsString('<td><strong>Obat APBD</strong></td>', $printHtml);
        $this->assertStringContainsString('<td><strong>BMHP APBD</strong></td>', $printHtml);
        $this->assertStringContainsString('<td><strong>Obat APBN</strong></td>', $printHtml);
        $this->assertStringContainsString('<td><strong>BMHP APBN</strong></td>', $printHtml);

        $printRes->assertSee('1.111.111,00');
        $printRes->assertSee('2.222.222,00');
        $printRes->assertSee('3.333.333,00');
        $printRes->assertSee('4.444.444,00');
        $printRes->assertSee('5.555.555,00');
        $printRes->assertSee('6.666.666,00');

        // Create & edit pages render cleanly
        $this->get(route('ba-stock-opname.create'))->assertStatus(200);
        $this->get(route('ba-stock-opname.edit', $record->id))->assertStatus(200);

        // Store & update endpoints persist separate Obat and BMHP fields
        $storeRes = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('ba-stock-opname.store'), [
                'nomor_surat' => '000.2.1.2/    /413.102.5.18/2026',
                'tanggal_opname' => '2026-11-11',
                'atk' => 100000,
                'bahan_cetak' => 200000,
                'benda_pos' => 50000,
                'bahan_komputer' => 150000,
                'obat_blud' => 1200000,
                'bmhp_blud' => 800000,
                'obat_apbd' => 900000,
                'bmhp_apbd' => 450000,
                'obat_apbn' => 700000,
                'bmhp_apbn' => 350000,
            ]);
        $storeRes->assertRedirect();

        $created = StockOpnamePersediaan::latest('id')->first();
        $this->assertSame(1200000.0, $created->obat_blud);
        $this->assertSame(800000.0, $created->bmhp_blud);
        $this->assertSame(900000.0, $created->obat_apbd);
        $this->assertSame(450000.0, $created->bmhp_apbd);
        $this->assertSame(700000.0, $created->obat_apbn);
        $this->assertSame(350000.0, $created->bmhp_apbn);
        $this->assertSame(4900000.0, $created->total_nilai);
    }
}
