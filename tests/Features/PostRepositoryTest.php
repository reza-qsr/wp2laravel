<?php

namespace RezaQsr\Wp2Laravel\Tests\Features;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use RezaQsr\Wp2Laravel\Models\Post;
use RezaQsr\Wp2Laravel\Repositories\DbPostRepository;
use RezaQsr\Wp2Laravel\Tests\TestCase;

class PostRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    protected DbPostRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new DbPostRepository();
    }
    /** @test */
    public function it_can_insert_a_new_post()
    {
        DB::beginTransaction();
        $data = [
            'post_title'   => 'Test Product',
            'post_content' => 'This is the content',
            'post_status'  => 'publish',
            'post_type'    => 'product',
        ];

        $post = $this->repo->insert($data);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals('Test Product', $post->post_title);
        $this->assertEquals('publish', $post->post_status);
        $this->assertEquals('product', $post->post_type);
        DB::Rollback();
    }

    /** @test */
    public function it_can_find_a_post_by_id()
    {
        DB::beginTransaction();
        $data = [
            'post_title' => 'Find Me',
            'post_type' => 'post',
            'post_status' => 'publish'
        ];

        $post = $this->repo->insert($data);
        $found = $this->repo->find($post->ID);

        $this->assertEquals($post->ID, $found->ID);
        $this->assertEquals('Find Me', $found->post_title);
        DB::Rollback();
    }

    /** @test */
    public function it_can_update_a_post()
    {
        DB::beginTransaction();
        $data = [
            'post_title' => 'Old Title',
            'post_type' => 'post',
            'post_status' => 'draft'
        ];
        $post = $this->repo->insert($data);
        $updated = $this->repo->update($post->ID, [
            'post_title' => 'Updated Title',
            'post_status' => 'publish',
        ]);

        $this->assertTrue($updated);

        $post->refresh();
        $this->assertEquals('Updated Title', $post->post_title);
        $this->assertEquals('publish', $post->post_status);

        DB::Rollback();
    }
    /** @test */
    public function it_can_delete_a_post()
    {
        $data = [
            'post_title' => 'Delete Me',
            'post_type' => 'post',
            'post_status' => 'publish'
        ];
        $post = $this->repo->insert($data);
        $deleted = $this->repo->delete($post->ID);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('wp_posts', ['ID' => $post->ID]);
    }
}
