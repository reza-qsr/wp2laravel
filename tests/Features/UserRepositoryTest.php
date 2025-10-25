<?php

namespace RezaQsr\Wp2Laravel\Tests\Features;

use RezaQsr\Wp2Laravel\Tests\TestCase;
use RezaQsr\Wp2Laravel\Repositories\DbUserRepository;
use RezaQsr\Wp2Laravel\Models\User;
use RezaQsr\Wp2Laravel\Models\UserMeta;
use Illuminate\Support\Facades\DB;

class UserRepositoryTest extends TestCase
{
    protected DbUserRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new DbUserRepository();
    }

    /** @test */
    public function it_can_insert_a_new_user()
    {
        DB::beginTransaction();
        $data = [
            'user_login' => 'reza_test',
            'user_pass' => 'secret123',
            'user_email' => 'reza_test@example.com',
            'display_name' => 'Reza Test',
        ];

        $user = $this->repo->insert($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('reza_test', $user->user_login);
        $this->assertNotNull($user->ID);
        DB::rollBack();
    }

    /** @test */
    public function it_can_update_an_existing_user()
    {
        DB::beginTransaction();
        $user = $this->repo->insert([
            'user_login' => 'update_user',
            'user_pass' => 'password',
            'user_email' => 'update_user@example.com',
        ]);

        $updated = $this->repo->update($user->ID, [
            'display_name' => 'Updated Name',
        ]);

        $this->assertTrue($updated);
        $this->assertEquals('Updated Name', User::find($user->ID)->display_name);
        DB::rollBack();
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        DB::beginTransaction();
        $user = $this->repo->insert([
            'user_login' => 'delete_me',
            'user_pass' => 'secret',
            'user_email' => 'delete_me@example.com',
        ]);

        $deleted = $this->repo->delete($user->ID);
        $this->assertTrue($deleted);
        $this->assertNull(User::find($user->ID));
        DB::rollBack();
    }

    /** @test */
    public function it_can_manage_user_meta()
    {
        DB::beginTransaction();
        $user = $this->repo->insert([
            'user_login' => 'meta_user',
            'user_pass' => 'secret',
            'user_email' => 'meta_user@example.com',
        ]);


        $this->repo->updateMeta($user->ID, 'nickname', 'Reza');
        $this->assertTrue($this->repo->hasMeta($user->ID, 'nickname'));
        $value = $this->repo->getMeta($user->ID, 'nickname');
        $this->assertEquals('Reza', $value);

        $this->repo->updateMeta($user->ID, 'nickname', 'RezaQsr');
        $this->assertEquals('RezaQsr', $this->repo->getMeta($user->ID, 'nickname'));

        $this->repo->deleteMeta($user->ID, 'nickname');
        $this->assertFalse($this->repo->hasMeta($user->ID, 'nickname'));
        $this->assertNull($this->repo->getMeta($user->ID, 'nickname'));
        DB::rollBack();
    }

    /** @test */
    public function it_can_get_user_roles()
    {
        DB::beginTransaction();
        $user = $this->repo->insert([
            'user_login' => 'role_user',
            'user_pass' => 'secret',
            'user_email' => 'role_user@example.com',
        ]);

        $update = $this->repo->updateMeta($user->ID , 'wp_capabilities' , ['administrator' => true]);
        $roles = $this->repo->getRoles($user->ID);
        $this->assertIsArray($roles);
        $this->assertContains('administrator', $roles);
        DB::rollBack();
    }
}
