<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{
	public function up()
    {
		Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('personal_team');
            $table->timestamps();
			
        });
		if (!Schema::hasTable('authentication_log')) {
        Schema::create('authentication_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('authenticatable');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();
        });
		}
        Schema::create('team_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'email']);
        });
        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');
            $table->foreignId('user_id');
            $table->string('role')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'user_id']);
        });
		if (!Schema::hasTable('bans')) {
        Schema::create('bans', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('bannable');
            $table->nullableMorphs('created_by');
            $table->text('comment')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('expired_at');
        });
		}
		$this->upto();
		$this->setprimaryindex();
	}
	function upto(){
		if (!Schema::hasColumn('permissions', 'ArName')) {Schema::table('permissions', function (Blueprint $table) {$table->string('ArName')->nullable()->after('name');});}
        if (!Schema::hasColumn('permissions', 'deleted_at')) {Schema::table('permissions', function (Blueprint $table) {$table->softDeletes($column = 'deleted_at', $precision = 0);});}
        if (!Schema::hasColumn('roles', 'ArName')) {Schema::table('roles', function (Blueprint $table) {$table->string('ArName')->nullable()->after('name');});}
        if (!Schema::hasColumn('roles', 'sort')) {Schema::table('roles', function (Blueprint $table) {$table->integer('sort')->nullable()->after('name');});}
        if (!Schema::hasColumn('roles', 'deleted_at')) {Schema::table('roles', function (Blueprint $table) {$table->softDeletes($column = 'deleted_at', $precision = 0);});}
		if (!Schema::hasColumn('users', 'deleted_at')) {Schema::table('users', function (Blueprint $table) {$table->string('first_name')->nullable();});}
		if (!Schema::hasColumn('users', 'deleted_at')) {Schema::table('users', function (Blueprint $table) {$table->string('last_name')->nullable();});}
		if (!Schema::hasColumn('users', 'deleted_at')) {Schema::table('users', function (Blueprint $table) {$table->string('title')->nullable();});}
		if (!Schema::hasColumn('users', 'deleted_at')) {Schema::table('users', function (Blueprint $table) {$table->string('mobile')->nullable();});}
		if (!Schema::hasColumn('users', 'deleted_at')) {Schema::table('users', function (Blueprint $table) {$table->foreignId('current_team_id')->nullable();});}
		if (!Schema::hasColumn('users', 'deleted_at')) {Schema::table('users', function (Blueprint $table) {$table->longText('note')->nullable();});}
		if (!Schema::hasColumn('users', 'deleted_at')) {Schema::table('users', function (Blueprint $table) {$table->softDeletes($column = 'deleted_at', $precision = 0);});}
	}
    function setprimaryindex(){
        Schema::table('team_user', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
	public function down()
    {
		Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('ArName');
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('ArName');
            $table->dropColumn('sort');
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
		Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
			$table->dropColumn('last_name');
			$table->dropColumn('title');
			$table->dropColumn('mobile');
			$table->dropColumn('current_team_id');
			$table->dropColumn('note');
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
		Schema::dropIfExists('teams');
        Schema::dropIfExists('authentication_log');
        Schema::dropIfExists('team_invitations');
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('bans');
    }
};