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
}
