<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use App\Services\Snapshots\SnapshotException;
use App\Services\Snapshots\SnapshotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class DeveloperToolsSnapshotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        File::deleteDirectory(storage_path('app/private/snapshots'));
        File::deleteDirectory(storage_path('app/private/snapshot-work'));
        File::deleteDirectory(storage_path('app/private/snapshot-restore'));
    }

    public function test_admin_can_view_developer_tools(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.developer-tools.index'));

        $response->assertOk();
        $response->assertSee('Developer tools');
        $response->assertSee('Sistema');
        $response->assertSee('Migración');
    }

    public function test_non_admin_cannot_access_developer_tools(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);

        $response = $this->actingAs($editor)->get(route('admin.developer-tools.index'));

        $response->assertForbidden();
    }

    public function test_snapshot_create_command_generates_backup_file(): void
    {
        User::factory()->create(['role' => 'admin']);
        Storage::disk('public')->put('media/example.txt', 'example');

        $exitCode = Artisan::call('snapshot:create', ['--name' => 'feature-test']);

        $this->assertSame(0, $exitCode);
        $this->assertFileExists(storage_path('app/private/snapshots/feature-test.appbackup'));
    }

    public function test_restore_replaces_database_and_uploads(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'origin@example.com',
        ]);
        Media::query()->create([
            'filename' => 'origin.txt',
            'original_filename' => 'origin.txt',
            'path' => 'media/origin.txt',
            'mime_type' => 'text/plain',
            'extension' => 'txt',
            'size' => 6,
            'disk' => 'public',
            'uploaded_by' => $admin->id,
        ]);
        Storage::disk('public')->put('media/origin.txt', 'origin');

        $archive = app(SnapshotService::class)->create('restore-source');

        Media::query()->delete();
        User::query()->delete();
        User::factory()->create([
            'role' => 'admin',
            'email' => 'destination@example.com',
        ]);
        Storage::disk('public')->delete('media/origin.txt');
        Storage::disk('public')->put('media/old.txt', 'old');

        app(SnapshotService::class)->restore($archive, true);

        $this->assertDatabaseHas('users', ['email' => 'origin@example.com']);
        $this->assertDatabaseMissing('users', ['email' => 'destination@example.com']);
        $this->assertDatabaseHas('media', ['path' => 'media/origin.txt']);
        Storage::disk('public')->assertExists('media/origin.txt');
        Storage::disk('public')->assertMissing('media/old.txt');
    }

    public function test_manifest_does_not_contain_schema_qualified_table_names(): void
    {
        User::factory()->create(['role' => 'admin']);

        $archive = app(SnapshotService::class)->create('manifest-test');
        $manifest = $this->readJsonFromZip($archive, 'manifest.json');

        foreach ($manifest['database']['tables'] as $table) {
            $this->assertStringNotContainsString('.', $table['name']);
        }
    }

    public function test_restore_rejects_schema_qualified_table_names(): void
    {
        User::factory()->create(['role' => 'admin']);

        $archive = app(SnapshotService::class)->create('unsafe-table');
        $unsafeArchive = $this->copyArchiveWithMutatedManifest($archive, function (array $manifest): array {
            $manifest['database']['tables'][0]['name'] = 'other_schema.'.$manifest['database']['tables'][0]['name'];

            return $manifest;
        });

        $this->expectException(SnapshotException::class);
        $this->expectExceptionMessage('schema-qualified');

        app(SnapshotService::class)->restore($unsafeArchive, true);
    }

    protected function readJsonFromZip(string $archive, string $path): array
    {
        $zip = new ZipArchive;
        $zip->open($archive);

        try {
            return json_decode($zip->getFromName($path), true, 512, JSON_THROW_ON_ERROR);
        } finally {
            $zip->close();
        }
    }

    protected function copyArchiveWithMutatedManifest(string $archive, callable $mutator): string
    {
        $sourceDir = storage_path('app/private/test-mutate/'.uniqid('source-', true));
        $target = storage_path('app/private/test-mutate/mutated-'.uniqid().'.appbackup');
        File::ensureDirectoryExists($sourceDir);

        $zip = new ZipArchive;
        $zip->open($archive);
        $zip->extractTo($sourceDir);
        $zip->close();

        $manifestPath = $sourceDir.'/manifest.json';
        $manifest = json_decode((string) file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);
        file_put_contents($manifestPath, json_encode($mutator($manifest), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

        $checksums = [];
        foreach (File::allFiles($sourceDir) as $file) {
            $relative = str_replace('\\', '/', $file->getRelativePathname());
            if ($relative !== 'checksums.json') {
                $checksums[$relative] = hash_file('sha256', $file->getPathname());
            }
        }
        ksort($checksums);
        file_put_contents($sourceDir.'/checksums.json', json_encode($checksums, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

        $newZip = new ZipArchive;
        $newZip->open($target, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach (File::allFiles($sourceDir) as $file) {
            $newZip->addFile($file->getPathname(), str_replace('\\', '/', $file->getRelativePathname()));
        }
        $newZip->close();

        File::deleteDirectory($sourceDir);

        return $target;
    }
}
