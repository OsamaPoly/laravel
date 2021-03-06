<?php

namespace Illuminate\Tests\Integration\Database;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (PHP_VERSION_ID >= 80100) {
    include_once 'Enums.php';
}

/**
 * @requires PHP >= 8.1
 */
class QueryingWithEnumsTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('enum_casts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('string_status', 100)->nullable();
            $table->integer('integer_status')->nullable();
        });
    }

    public function testCanQueryWithEnums()
    {
        DB::table('enum_casts')->insert([
            'string_status' => 'pending',
            'integer_status' => 1,
        ]);

        $record = DB::table('enum_casts')->where('string_status', StringStatus::pending)->first();
        $record2 = DB::table('enum_casts')->where('integer_status', IntegerStatus::pending)->first();
        $record3 = DB::table('enum_casts')->whereIn('integer_status', [IntegerStatus::pending])->first();

        $this->assertNotNull($record);
        $this->assertNotNull($record2);
        $this->assertNotNull($record3);
        $this->assertEquals('pending', $record->string_status);
        $this->assertEquals(1, $record2->integer_status);
    }

    public function testCanInsertWithEnums()
    {
        DB::table('enum_casts')->insert([
            'string_status' => StringStatus::pending,
            'integer_status' => IntegerStatus::pending,
        ]);

        $record = DB::table('enum_casts')->where('string_status', StringStatus::pending)->first();

        $this->assertNotNull($record);
        $this->assertEquals('pending', $record->string_status);
        $this->assertEquals(1, $record->integer_status);
    }
}
