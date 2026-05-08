<?php

namespace Tests\Unit\Services;

use App\Models\AnneeAcademique;
use App\Services\AnneeAcademiqueService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests unitaires — AnneeAcademiqueService
 * Sans base de données — logique pure uniquement
 *
 * Lancer : php artisan test tests/Unit/Services/AnneeAcademiqueServiceTest.php
 */
class AnneeAcademiqueServiceTest extends TestCase
{
    use RefreshDatabase;

    private function donneesValides(array $overrides = []): array
    {
        return array_merge([
            'code'                    => '2025-2026',
            'date_ouverture'          => '2025-10-01',
            'date_fermeture'          => '2026-07-01',
            'date_debut_inscription'  => '2025-09-01',
            'date_fin_inscription'    => '2025-11-01',
        ], $overrides);
    }

    public function test_creation_annee_normal()
    {
        $service = new AnneeAcademiqueService();

        $data = $this->donneesValides();

        $annee = $service->creer($data);

        // Vérifie que l'objet est bien créé
        $this->assertNotNull($annee);
        $this->assertEquals('2025-2026', $annee->code);

        // Vérifie la base de données
        $this->assertDatabaseHas('annees_academiques', [
            'code'   => '2025-2026',
            'statut' => AnneeAcademique::STATUT_BROUILLON,
        ]);
    }

    public function test_creation_annee_different_9mois()
    {

        $service = new AnneeAcademiqueService();

        $data =  $this->donneesValides([
            'date_ouverture' => '2025-10-01',
            'date_fermeture' => '2026-06-01',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/9 mois/');

        $annee = $service->creer($data);
    }

    public function test_sauter_un_statut()
    {
        $service = new AnneeAcademiqueService();

        $data = $this->donneesValides();

        $annee = $service->creer($data);

        $annee = $service->avancerStatus($annee);

        $this->assertNotEquals(
            AnneeAcademique::STATUT_INSCRIPTION_OUVERTE,
            $annee->statut
        );

        $this->assertEquals(
            AnneeAcademique::STATUT_PUBLIE,
            $annee->statut
        );
    }

    public function test_sauter_un_statut_publier(){
        $service = new AnneeAcademiqueService();

        $data = $this->donneesValides();

        $annee = $service->creer($data);

        $this->expectException(\Exception::class);

        $annee->update(['statut' => AnneeAcademique::STATUT_INSCRIPTION_OUVERTE]);
    }

}
