<?php

namespace Tests\Feature;

use App\Models\AnneeAcademique;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Tests d'intégration — AnneeAcademique
 * Sans authentification — routes publiques ou sans middleware auth
 *
 * Lancer : php artisan test tests/Feature/AnneeAcademiqueTest.php
 */
class AnneeAcademiqueTest extends TestCase
{
    use RefreshDatabase;

    private function donneesValides(array $overrides = []): array
    {
        return array_merge([
            'code'                    => '2025-2026',
            'date_ouverture'          => '2025-10-01',
            'date_fermeture'          => '2026-07-01', // exactement 9 mois ✓
            'date_debut_inscription'  => '2025-09-01', // avant ouverture école ✓
            'date_fin_inscription'    => '2025-11-01', // après ouverture, avant fermeture ✓
        ], $overrides);
    }

    /**
     * ✅ Création d'une année académique avec des données valides
     */
    public function test_creation_annee_academique_valide(): void
    {
        $response = $this->post(route('annees.store'), $this->donneesValides());

        $response->assertRedirect(route('annees.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('annees_academiques', [
            'code'   => '2025-2026',
            'statut' => AnneeAcademique::STATUT_BROUILLON,
        ]);
    }

    /**
     * ✅ Le statut est automatiquement "brouillon" à la création
     */
    public function test_statut_brouillon_a_la_creation(): void
    {
        $this->post(route('annees.store'), $this->donneesValides());

        $annee = AnneeAcademique::where('code', '2025-2026')->first();

        $this->assertNotNull($annee);
        $this->assertEquals(AnneeAcademique::STATUT_BROUILLON, $annee->statut);
    }

    /**
     * ❌ Date de fermeture avant date d'ouverture → erreur de validation
     */
    public function test_creation_avec_fermeture_avant_ouverture_echoue(): void
    {
        $response = $this->post(route('annees.store'), $this->donneesValides([
            'date_ouverture' => '2025-10-01',
            'date_fermeture' => '2025-08-01', // ❌ avant l'ouverture
        ]));

        $response->assertSessionHasErrors('date_fermeture');

        $this->assertDatabaseMissing('annees_academiques', ['code' => '2025-2026']);
    }

    /**
     * ❌ Date de fin d'inscription avant date de début → erreur
     */
    public function test_creation_avec_fin_inscription_avant_debut_echoue(): void
    {
        $response = $this->post(route('annees.store'), $this->donneesValides([
            'date_debut_inscription' => '2025-09-15',
            'date_fin_inscription'   => '2025-09-01', // ❌ avant le début
        ]));

        $response->assertSessionHasErrors('date_fin_inscription');
    }

    /**
     * ❌ Ouverture inscriptions après ouverture de l'école → erreur
     */
    public function test_inscription_ouvre_apres_ecole_echoue(): void
    {
        $response = $this->post(route('annees.store'), $this->donneesValides([
            'date_ouverture'         => '2025-10-01',
            'date_debut_inscription' => '2025-10-15', // ❌ après ouverture école
        ]));

        $response->assertSessionHasErrors('date_debut_inscription');
    }


    /**
     * ❌ Créer une année avec un code déjà existant → erreur unique
     */
    public function test_creation_annee_avec_code_existant_echoue(): void
    {
        // Première création — doit réussir
        $this->post(route('annees.store'), $this->donneesValides());

        $this->assertDatabaseHas('annees_academiques', ['code' => '2025-2026']);

        // Deuxième création même code — doit échouer
        $response = $this->post(route('annees.store'), $this->donneesValides([
            'date_ouverture'         => '2025-10-05',
            'date_fermeture'         => '2026-07-05',
            'date_debut_inscription' => '2025-09-05',
            'date_fin_inscription'   => '2025-11-05',
        ]));

        $response->assertSessionHasErrors('code');

        // Une seule entrée 2025-2026 en base
        $this->assertEquals(1, AnneeAcademique::where('code', '2025-2026')->count());
    }

    /**
     * ❌ Une année de 8 mois doit être refusée
     */
    public function test_annee_de_8_mois_echoue(): void
    {
        $response = $this->post(route('annees.store'), $this->donneesValides([
            'date_ouverture' => '2025-10-01',
            'date_fermeture' => '2026-06-01', // 8 mois ❌
        ]));

        $response->assertSessionHasErrors();

        $this->assertDatabaseMissing('annees_academiques', ['code' => '2025-2026']);
    }

    /**
     * ❌ Une année de 12 mois doit être refusée
     */
    public function test_annee_de_12_mois_echoue(): void
    {
        $response = $this->post(route('annees.store'), $this->donneesValides([
            'date_ouverture' => '2025-10-01',
            'date_fermeture' => '2026-10-01', // 12 mois ❌
        ]));

        $response->assertSessionHasErrors();
    }

    /**
     * ❌ Modifier une année publiée doit être refusé
     */
    public function test_modification_annee_publiee_est_refusee(): void
    {
        // Créer directement en base avec statut publié (bypass le hook)
        DB::table('annees_academiques')->insert([
            ...$this->donneesValides(),
            'statut'     => AnneeAcademique::STATUT_PUBLIE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $annee = AnneeAcademique::where('code', '2025-2026')->first();

        $response = $this->put(route('annees.update', $annee), $this->donneesValides([
            'code' => '2025-2026',
        ]));

        $response->assertSessionHasErrors();
    }

    /**
     * ✅ Supprimer une année en brouillon doit fonctionner
     */
    public function test_suppression_brouillon_reussit(): void
    {
        $this->post(route('annees.store'), $this->donneesValides());

        $annee = AnneeAcademique::where('code', '2025-2026')->first();

        $response = $this->delete(route('annees.destroy', $annee));

        $response->assertRedirect();
        $this->assertDatabaseMissing('annees_academiques', ['code' => '2025-2026']);
    }

    /**
     * ❌ Supprimer une année publiée doit être refusé
     */
    public function test_suppression_annee_publiee_est_refusee(): void
    {
        DB::table('annees_academiques')->insert([
            ...$this->donneesValides(),
            'statut'     => AnneeAcademique::STATUT_PUBLIE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $annee = AnneeAcademique::where('code', '2025-2026')->first();

        $response = $this->delete(route('annees.destroy', $annee));

        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('annees_academiques', ['code' => '2025-2026']);
    }
}