<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if jobs table exists and has the wrong structure (Laravel queue table)
        if (Schema::hasTable('jobs') && Schema::hasColumn('jobs', 'queue')) {
            // This is Laravel's queue table, not the application jobs table
            // Rename it to avoid conflicts
            Schema::rename('jobs', 'queue_jobs');
        }

        // Create the application jobs table if it doesn't exist
        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('buyer_id')->default(0);
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->unsignedBigInteger('category_id')->default(0);
                $table->unsignedBigInteger('subcategory_id')->default(0);
                $table->decimal('budget', 28, 8)->default(0);
                $table->tinyInteger('custom_budget')->default(0)->comment('1 =>Enable ,0 => Disabled');
                $table->text('description')->nullable();
                $table->tinyInteger('project_scope')->default(0)->comment('Large => 1, Medium=>2, Small->3');
                $table->tinyInteger('job_longevity')->default(0)->comment('job_longevity: 3 to 6 months=>4, 1 to 3 months=>3,Less than 1 month=>2 , Less than 1 Week=>1');
                $table->tinyInteger('skill_level')->default(0)->comment('Pro Level=>1, Expert=>2, Intermediate=>3, Entry=>4');
                $table->text('questions')->nullable();
                $table->text('skill_ids')->nullable();
                $table->date('deadline')->nullable();
                $table->tinyInteger('status')->default(0)->comment('0=>Draft, 1=>Published, 2=>Processing, 3=> Completed, 4=> Finished');
                $table->tinyInteger('is_approved')->default(0)->comment('0->Onward ,1->Approved, 3-> Rejected');
                $table->text('rejection_reason')->nullable();
                $table->integer('interviews')->default(0)->comment('total_interview count of this job , against bids');
                $table->timestamps();
            });
        } else {
            // Table exists, add missing columns
            // Handle column renames using raw SQL (Laravel's renameColumn has limitations)
            if (Schema::hasColumn('jobs', 'user_id') && !Schema::hasColumn('jobs', 'buyer_id')) {
                DB::statement('ALTER TABLE jobs CHANGE user_id buyer_id BIGINT UNSIGNED NOT NULL DEFAULT 0');
            }
            
            if (Schema::hasColumn('jobs', 'sub_category_id') && !Schema::hasColumn('jobs', 'subcategory_id')) {
                DB::statement('ALTER TABLE jobs CHANGE sub_category_id subcategory_id BIGINT UNSIGNED DEFAULT 0');
            }
            
            Schema::table('jobs', function (Blueprint $table) {
                
                // Add missing columns
                if (!Schema::hasColumn('jobs', 'slug')) {
                    $table->string('slug')->nullable()->after('title');
                }
                if (!Schema::hasColumn('jobs', 'custom_budget')) {
                    $table->tinyInteger('custom_budget')->default(0)->comment('1 =>Enable ,0 => Disabled')->after('budget');
                }
                if (!Schema::hasColumn('jobs', 'project_scope')) {
                    $table->tinyInteger('project_scope')->default(0)->comment('Large => 1, Medium=>2, Small->3')->after('description');
                }
                if (!Schema::hasColumn('jobs', 'job_longevity')) {
                    $table->tinyInteger('job_longevity')->default(0)->comment('job_longevity: 3 to 6 months=>4, 1 to 3 months=>3,Less than 1 month=>2 , Less than 1 Week=>1')->after('project_scope');
                }
                if (!Schema::hasColumn('jobs', 'skill_level')) {
                    $table->tinyInteger('skill_level')->default(0)->comment('Pro Level=>1, Expert=>2, Intermediate=>3, Entry=>4')->after('job_longevity');
                }
                if (!Schema::hasColumn('jobs', 'questions')) {
                    $table->text('questions')->nullable()->after('skill_level');
                }
                if (!Schema::hasColumn('jobs', 'skill_ids')) {
                    $table->text('skill_ids')->nullable()->after('questions');
                }
                if (!Schema::hasColumn('jobs', 'deadline')) {
                    $table->date('deadline')->nullable()->after('skill_ids');
                }
                if (!Schema::hasColumn('jobs', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('is_approved');
                }
                if (!Schema::hasColumn('jobs', 'interviews')) {
                    $table->integer('interviews')->default(0)->comment('total_interview count of this job , against bids')->after('rejection_reason');
                }
                
                // Ensure buyer_id exists (add if user_id was renamed or doesn't exist)
                if (!Schema::hasColumn('jobs', 'buyer_id')) {
                    $table->unsignedBigInteger('buyer_id')->default(0)->after('id');
                }
                
                // Ensure subcategory_id exists
                if (!Schema::hasColumn('jobs', 'subcategory_id')) {
                    $table->unsignedBigInteger('subcategory_id')->default(0)->after('category_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop the table, just remove added columns if needed
        Schema::table('jobs', function (Blueprint $table) {
            $columns = ['slug', 'custom_budget', 'project_scope', 'job_longevity', 'skill_level', 'questions', 'skill_ids', 'deadline', 'rejection_reason', 'interviews'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('jobs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
