<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
             $table->bigIncrements('id');
            $table->string('user_id');
            $table->string('lead_no');
            $table->string('client_id');
            $table->string('company_name');
            $table->string('softwaretype');
            $table->string('plan');
            $table->string('projectvalue');
            $table->string('advance_amt')->nullable();
            $table->string('handoverdate')->nullable();
            $table->string('domainname')->nullable();
             $table->string('domainamt')->nullable();
            $table->string('first_installment_dt')->nullable();
            $table->string('first_installment')->nullable();
            $table->string('sec_installment_dt')->nullable();
             $table->string('sec_installment')->nullable();
            $table->string('onhand_amt_dtae')->nullable();
            $table->string('onhandover_amt')->nullable();
            $table->string('logo')->nullable();
            $table->string('requirements')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
