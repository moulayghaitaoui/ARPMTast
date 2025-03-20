<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\SpreadsheetService;
use App\Jobs\ProcessProductImage;
use App\Models\Product;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Google\Service\Sheets\Spreadsheet;
class SpreadsheetServiceTest extends TestCase
{
    use RefreshDatabase; // Ensures a clean database before each test

    public function test_processSpreadsheet_imports_valid_data()
    {
        Queue::fake(); // Prevent actual job execution, allowing us to verify dispatching

        // Mock product data (valid entries)
        $productData = [
            ['product_code' => 'ABC123', 'quantity' => 10],
            ['product_code' => 'XYZ456', 'quantity' => 5],
        ];

        // Mock the Importer class
        $importerMock = Mockery::mock(SomeImporterClass::class);
        $importerMock->shouldReceive('import')->andReturn($productData);

        // Register the mock in Laravel's service container
        app()->instance(SomeImporterClass::class, $importerMock);

        // Execute the spreadsheet processing service
        $service = new SpreadsheetService();
        $service->processSpreadsheet('dummy_path.xlsx');

        // Verify that products were inserted into the database
        $this->assertDatabaseHas('products', ['code' => 'ABC123', 'quantity' => 10]);
        $this->assertDatabaseHas('products', ['code' => 'XYZ456', 'quantity' => 5]);

        // Ensure the image processing job was dispatched twice (once per product)
        Queue::assertPushed(ProcessProductImage::class, 2);
    }

    public function test_processSpreadsheet_skips_invalid_data()
    {
        Queue::fake(); // Prevent actual job execution

        // Invalid product data cases
        $invalidData = [
            ['product_code' => '', 'quantity' => 10], // Missing product code
            ['product_code' => 'ABC123', 'quantity' => 0], // Invalid quantity
            ['product_code' => 'XYZ456', 'quantity' => 'not_a_number'], // Non-numeric quantity
        ];

        // Mock the Importer class
        $importerMock = Mockery::mock(SomeImporterClass::class);
        $importerMock->shouldReceive('import')->andReturn($invalidData);

        // Register the mock in Laravel's service container
        app()->instance(SomeImporterClass::class, $importerMock);

        // Execute the spreadsheet processing service
        $service = new SpreadsheetService();
        $service->processSpreadsheet('dummy_path.xlsx');

        // Ensure invalid data is NOT inserted into the database
        $this->assertDatabaseMissing('products', ['code' => 'ABC123']);
        $this->assertDatabaseMissing('products', ['code' => 'XYZ456']);

        // Ensure no image processing job was dispatched due to invalid data
        Queue::assertNotPushed(ProcessProductImage::class);
    }
}
