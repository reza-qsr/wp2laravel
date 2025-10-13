<?php

namespace RezaQsr\Wp2Laravel\Tests\Features;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use RezaQsr\Wp2Laravel\Models\Post;
use RezaQsr\Wp2Laravel\Models\TermTaxonomy;
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
            'post_title' => 'Test Product',
            'post_content' => 'This is the content',
            'post_status' => 'publish',
            'post_type' => 'product',
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

    /** @test */
    public function it_can_get_and_update_post_meta()
    {
        DB::beginTransaction();
        $data = [
            'post_title' => 'Meta Post',
            'post_type' => 'post',
            'post_status' => 'publish'
        ];
        $post = $this->repo->insert($data);

        $this->repo->updateMeta($post->ID, 'color', 'gold');
        $metaValue = $this->repo->getMeta($post->ID, 'color');

        $this->assertEquals('gold', $metaValue);
        $this->assertTrue($this->repo->hasMeta($post->ID, 'color'));

        $this->repo->updateMeta($post->ID, 'color', 'silver');
        $metaValue = $this->repo->getMeta($post->ID, 'color');

        $this->assertEquals('silver', $metaValue);
        DB::Rollback();
    }

    /** @test */
    public function it_can_delete_post_meta()
    {
        DB::beginTransaction();
        $data = [
            'post_title' => 'Meta Delete',
            'post_type' => 'post',
            'post_status' => 'publish'
        ];
        $post = $this->repo->insert($data);
        $this->repo->updateMeta($post->ID, 'color', 'red');
        $this->assertTrue($this->repo->deleteMeta($post->ID, 'color'));
        $this->assertFalse($this->repo->hasMeta($post->ID, 'color'));
        DB::Rollback();
    }


    /** @test */
    public function it_can_query_posts_with_conditions()
    {
        $cat =
        $args = array(
            'post_type' => 'product',
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => 'shirt',
                ),
            ),

        );
        $results = $this->repo->query($args);
        $this->assertContainsOnlyInstancesOf(Post::class, $results);
        foreach ($results as $post) {
            $this->assertEquals('product', $post->post_type);
            $terms = $post->taxonomies->pluck('taxonomy')->toArray();
            $this->assertContains('product_cat', $terms);
        }
    }

    /** @test */
    public function create_posts_and_can_query_posts_with_conditions()
    {
        DB::beginTransaction();
        $post1 = $this->repo->insert([
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_author' => 1,
        ]);

        $post2 = $this->repo->insert([
            'post_type' => 'post',
            'post_status' => 'draft',
            'post_author' => 2,
        ]);

        $post3 = $this->repo->insert([
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_author' => 2,
        ]);
        $this->repo->updateMeta( $post3->ID, 'featured', 'yes',);

        $taxonomy = TermTaxonomy::query()->firstOrCreate([
            'taxonomy' => 'product_cat', 'term_id' => 1581,
        ]);
        $post3->taxonomies()->attach($taxonomy->term_taxonomy_id);

        $args = [
            'post_type' => 'post',
            'post_status' => 'publish',
        ];

        $result = $this->repo->query($args);
        $this->assertTrue($result->first()->is($post3));


        $args = [
            'author' => 1,
        ];
        $result = $this->repo->query($args);
        $this->assertTrue($result->contains($post1));


        $args = [
            'include' => [$post1->ID, $post2->ID, $post3->ID],
            'exclude' => [$post2->ID],
        ];
        $result = $this->repo->query($args);
        $this->assertFalse($result->contains('ID', $post2->ID));


        $args = [
            'meta_query' => [
                [
                    'key' => 'featured',
                    'value' => 'yes',
                    'compare' => '=',
                ],
            ],
        ];
        $result = $this->repo->query($args);
        $this->assertTrue($result->first()->is($post3));


        $args = [
            'tax_query' => [
                [
                    'taxonomy' => 'product_cat',
                    'terms' => [1581],
                ],
            ],
        ];
        $result = $this->repo->query($args);
        $this->assertTrue($result->first()->is($post3));


        $args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'author' => 2,
            'meta_query' => [
                [
                    'key' => 'featured',
                    'value' => 'yes',
                ],
            ],
        ];
        $result = $this->repo->query($args);
        $this->assertTrue($result->first()->is($post3));
        DB::Rollback();
    }
}
