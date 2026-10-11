<?php

namespace Tests\Feature;

use App\Models\MarketingBrochure;
use App\Models\MarketingCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketingCampaignBrochureTest extends TestCase
{
    use RefreshDatabase;

    public function test_social_ad_can_be_created_with_an_uploaded_brochure(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
        $brochure = MarketingBrochure::create([
            'title' => 'Island escape flyer',
            'file_name' => 'island-escape.pdf',
            'file_path' => 'marketing-brochures/island-escape.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 2048,
            'uploaded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('campaigns.store'), [
            'name' => 'Island Instagram Ad',
            'channel' => 'instagram',
            'marketing_brochure_id' => $brochure->id,
            'start_date' => '2026-10-10',
            'end_date' => '2026-10-31',
            'status' => 'planned',
        ]);

        $response->assertRedirect(route('campaign-analytics.index', ['tab' => 'social-ads']));

        $campaign = MarketingCampaign::query()->where('name', 'Island Instagram Ad')->firstOrFail();
        $this->assertSame($brochure->id, $campaign->marketing_brochure_id);
        $this->assertSame($brochure->id, $campaign->brochure->id);

        $this->get(route('campaign-analytics.index', ['tab' => 'social-ads']))
            ->assertOk()
            ->assertSee('Island escape flyer');
    }

    public function test_social_ad_creation_form_lists_uploaded_brochures(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
        MarketingBrochure::create([
            'title' => 'Summer campaign art',
            'file_name' => 'summer.png',
            'file_path' => 'marketing-brochures/summer.png',
            'mime_type' => 'image/png',
            'file_size' => 1024,
            'uploaded_by' => $user->id,
        ]);

        $this->actingAs($user)->get(route('campaigns.create'))
            ->assertOk()
            ->assertSee('Summer campaign art')
            ->assertSee('summer.png')
            ->assertDontSee('budget')
            ->assertDontSee('spend');
    }

    public function test_marketing_hub_does_not_show_campaign_spend_or_budget(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($user)->get(route('campaign-analytics.index'))
            ->assertOk()
            ->assertDontSee('Budget')
            ->assertDontSee('Spend');
    }

    public function test_social_ad_shows_and_serves_an_inline_image_brochure(): void
    {
        Storage::fake('local');
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
        $imageContents = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p5sAAAAASUVORK5CYII=', true);
        Storage::disk('local')->put('marketing-brochures/ad-image.png', $imageContents);
        $brochure = MarketingBrochure::create([
            'title' => 'Instagram creative',
            'file_name' => 'ad-image.png',
            'file_path' => 'marketing-brochures/ad-image.png',
            'mime_type' => 'image/png',
            'file_size' => strlen($imageContents),
            'uploaded_by' => $user->id,
        ]);
        $campaign = MarketingCampaign::create([
            'name' => 'Beach Instagram Ad',
            'channel' => 'instagram',
            'marketing_brochure_id' => $brochure->id,
            'start_date' => '2026-10-10',
            'end_date' => '2026-10-31',
            'status' => 'planned',
        ]);

        $this->actingAs($user)->get(route('campaign-analytics.index', ['tab' => 'social-ads']))
            ->assertOk()
            ->assertSee(route('marketing-brochures.preview', $brochure), false)
            ->assertSee('alt="Instagram creative"', false);

        $this->get(route('marketing-brochures.preview', $brochure))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }
}
