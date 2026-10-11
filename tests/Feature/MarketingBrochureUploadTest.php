<?php

namespace Tests\Feature;

use App\Models\MarketingBrochure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketingBrochureUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_a_png_brochure(): void
    {
        Storage::fake('local');

        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p5sAAAAASUVORK5CYII=', true);

        $response = $this->actingAs($user)->post(route('marketing-brochures.store'), [
            'title' => 'Dubai tour',
            'brochure' => UploadedFile::fake()->createWithContent('dubai.png', $png),
        ]);

        $response->assertRedirect(route('campaign-analytics.index', ['tab' => 'brochures']));
        $response->assertSessionHas('success', 'Promotional brochure uploaded.');
        $this->assertDatabaseHas('marketing_brochures', [
            'title' => 'Dubai tour',
            'file_name' => 'dubai.png',
            'uploaded_by' => $user->id,
        ]);

        $brochure = MarketingBrochure::query()->where('title', 'Dubai tour')->firstOrFail();
        Storage::disk('local')->assertExists($brochure->file_path);
    }

    public function test_user_cannot_upload_a_brochure_larger_than_10_mb(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->from(route('campaign-analytics.index', ['tab' => 'brochures']))
            ->post(route('marketing-brochures.store'), [
                'title' => 'Oversized brochure',
                'brochure' => UploadedFile::fake()->create('oversized.pdf', 10241, 'application/pdf'),
            ]);

        $response->assertRedirect(route('campaign-analytics.index', ['tab' => 'brochures']));
        $response->assertSessionHasErrors([
            'brochure' => 'The brochure or photo must not be larger than 10 MB.',
        ]);
        $this->assertDatabaseMissing('marketing_brochures', [
            'title' => 'Oversized brochure',
        ]);
    }
}
