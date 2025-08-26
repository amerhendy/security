<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{
	public function up()
    {
        if(!Schema::hasTable('users')){
            Schema::create('users', function (Blueprint $table) {
                $table->uid();
                $table->string('title')                     ->nullable();
                $table->string('first_name')                ->nullable();
                $table->string('last_name')                 ->nullable();
                $table->string('name')                      ->nullable();
                $table->string('email')                     ->nullable()->unique();
                $table->timestamp('email_verified_at')      ->nullable();
                $table->string('mobile')                    ->nullable();
                $table->string('password')                  ->nullable();
                $table->string('remember_token',100)        ->nullable();
                $table->longText('note')                        ->nullable();
                $table->foreignUuid('current_team_id')             ->nullable()->default(null);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->string('username')->nullable()->unique();
                $table->enum('gender', ['male', 'female'])->nullable();
                $table->date('birthdate')->nullable();
                $table->dates();
            });
        }
        if (!Schema::hasTable('teams')) {
            Schema::create('teams', function (Blueprint $table) {
                $table->uid();
                $table->uuid('user_id')
                      ->nullable()
                      ->default(null)
                      ->index();
                $table->string('name')->nullable();
                $table->boolean('personal_team')->nullable();
                $table->text('description')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->dates();
            });
        }
        if (!Schema::hasTable('team_user')) {
            Schema::create('team_user', function (Blueprint $table) {
                $table->uid();
                $table->foreignUuid('team_id')
                      ->nullable()
                      ->default(null)
                      ->cascadeOnDelete();
                $table->foreignUuid('user_id')
                      ->nullable()
                      ->default(null)
                      ->cascadeOnDelete();
                $table->string('role')->nullable();
                $table->dates();
                $table->unique(['team_id', 'user_id']);
            });
        }
        if (!Schema::hasTable('team_invitations')) {
            Schema::create('team_invitations', function (Blueprint $table) {
                $table->uid();
                $table->foreignUuid('team_id')
                        ->nullable()
                        ->default(null)
                        ->cascadeOnDelete();
                $table->string('email')->nullable();
                $table->string('role')->nullable()->default(null);
                $table->dates();
                $table->unique(['team_id', 'email']);
            });
        }
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->uid();
                $table->foreignUuid('team_id')->nullable()->cascadeOnDelete();
                $table->string('name')->nullable();
                $table->string('ar_name')->nullable()->default(null);
                $table->integer('sort')->nullable()->default(0);
                $table->string('guard_name')->nullable()->default(null);
                $table->dates();

                // منع التكرار لنفس الاسم داخل نفس الفريق
                $table->unique(['team_id', 'name']);
            });
        }

        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->uid();
                $table->string('name')->nullable();
                $table->string('ar_name')->nullable()->default(null);
                $table->string('guard_name')->nullable()->default(null);
                $table->dates();

                $table->unique(['name']);
                // لو ar_name مهم يكون فريد (اختياري)
                // $table->unique(['ar_name']);
            });
        }
        if(!Schema::hasTable('role_has_permissions')){
            Schema::create('role_has_permissions', function (Blueprint $table) {
                $table->uuid('permission_id')               ->nullable()->default(null)->constrained();
                $table->uuid('role_id')                     ->nullable()->default(null)->constrained();
            });
        }
        if(!Schema::hasTable('model_has_roles')){
            Schema::create('model_has_roles', function (Blueprint $table) {
                $table->uuid('role_id')                     ->nullable()->default(null)->constrained()->index();
                $table->string('model_type')                ->nullable()->default(null)->index();
                $table->uuid('model_id')                    ->nullable()->default(null)->index();
                $table->uuid('team_id')                     ->nullable()->default(null);
            });
        }
        if(!Schema::hasTable('model_has_permissions')){
            Schema::create('model_has_permissions', function (Blueprint $table) {
                $table->foreignUuid('permission_id')               ->nullable()->default(null)->constrained()->cascadeOnDelete();
                $table->string('model_type')                ->nullable()->default(null)->index();
                $table->uuid('model_id')                    ->nullable()->default(null)->index();
                $table->uuid('team_id')                     ->nullable()->default(null);
            });
        }
        if(!Schema::hasTable('oauth_refresh_tokens')){
            Schema::create('oauth_refresh_tokens', function (Blueprint $table) {
                $table->string('id');
                $table->string('access_token_id')           ->nullable()->index();
                $table->boolean('revoked')                  ->nullable();
                $table->timestamp('expires_at')             ->nullable()->default(null);
            });
        }
        if(!Schema::hasTable('oauth_personal_access_clients')){
            Schema::create('oauth_personal_access_clients', function (Blueprint $table) {
                $table->uid();
                $table->foreignUuid('client_id')             ->nullable();
                $table->dates();
            });
        }
        if(!Schema::hasTable('oauth_clients')){
            Schema::create('oauth_clients', function (Blueprint $table) {
                $table->uid();
                $table->foreignUuid('user_id')               ->nullable()->default(null)->index();
                $table->string('name')                      ->nullable();
                $table->string('secret')                    ->nullable();
                $table->string('provider')                  ->nullable();
                $table->text('redirect')                    ->nullable();
                $table->boolean('personal_access_client')   ->nullable();
                $table->boolean('password_client')          ->nullable();
                $table->boolean('revoked')                  ->nullable();
                $table->dates();
            });
        }
        if(!Schema::hasTable('oauth_auth_codes')){
            Schema::create('oauth_auth_codes', function (Blueprint $table) {
                $table->uid();
                $table  ->foreignUuid('user_id')               ->nullable()->default(null)->index();
                $table->foreignUuid('client_id')             ->nullable()->default(null);
                $table->text('scopes')                      ->nullable();
                $table->boolean('revoked')                  ->nullable();
                $table->timestamp('expires_at')             ->nullable();
            });
        }
        if(!Schema::hasTable('oauth_access_tokens')){
            Schema::create('oauth_access_tokens', function (Blueprint $table) {
                $table->uid();
                $table->foreignUuid('user_id')               ->nullable()->default(null)->index();
                $table->foreignUuid('client_id')             ->nullable()->default(null);
                $table->string('name')                      ->nullable();
                $table->text('scopes')                      ->nullable();
                $table->boolean('revoked')                  ->nullable();
                $table->timestamp('expires_at')             ->nullable()->default(null);
                $table->dates();
            });
        }
        if (!Schema::hasTable('bans')) {
            Schema::create('bans', function (Blueprint $table) {
                $table->uid();
                $table->string('bannable_type')->nullable()->index();
                $table->foreignUuid('bannable_id')->nullable()->index();
                $table->string('created_by_type')->nullable()->index();
                $table->foreignUuid('created_by_id')->nullable()->index();
                $table->text('comment')->nullable();
                $table->timestamp('expired_at')->nullable()->index();
                $table->dates();
            });
        }

        if (!Schema::hasTable('authentication_log')) {
            Schema::create('authentication_log', function (Blueprint $table) {
                $table->uid();
                $table->string('authenticatable_type')->nullable()->index();
                $table->foreignUuid('authenticatable_id')->nullable()->index();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('login_at')->nullable()->default(null);
                $table->timestamp('logout_at')->nullable()->default(null);
                $table->dates();
            });
        }
        $this->PrimaryIndex();
    }
    public function PrimaryIndex(){
        Schema::table('teams', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
        Schema::table('team_user', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams');
            $table->foreign('user_id')->references('id')->on('users');
        });
        Schema::table('team_invitations', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams');
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams');
        });
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
        });
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
        });
    }
};
