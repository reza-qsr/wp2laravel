<?php
namespace RezaQsr\Wp2Laravel\Tests;

use RezaQsr\Wp2Laravel\Repositories\DbPostRepository;
use RezaQsr\Wp2Laravel\Models\Post;
use RezaQsr\Wp2Laravel\Models\PostMeta;
use RezaQsr\Wp2Laravel\Models\Term;
use RezaQsr\Wp2laravel\Models\TermTaxonomy;
use RezaQsr\Wp2Laravel\Models\TermRelationship;


class DbPostRepositoryTest extends TestCase
{
    protected DbPostRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = $this->app->make(DbPostRepository::class);
    }

    public function test_insert_and_find_post()
    {
        $post = $this->repo->insert([
            'post_author' => 1,
            'post_date'   => now(),
            'post_content'=> 'Hello world',
            'post_excerpt'=> 'Hello',
            'post_title'  => 'Hello',
            'post_status' => 'publish',
            'post_type'   => 'post',
            'post_name'   => 'hello-world',
        ]);

        $this->assertNotEmpty($post->ID);

        $found = $this->repo->find($post->ID);
        $this->assertEquals('Hello', $found->post_title);
    }

//    public function test_meta_crud_and_meta_query()
//    {
//        $p1 = $this->repo->insert(['post_title' => 'A', 'post_type'=>'post', 'post_status'=>'publish']);
//        $p2 = $this->repo->insert(['post_title' => 'B', 'post_type'=>'post', 'post_status'=>'publish']);
//
//        $this->repo->updateMeta($p1->ID, 'color', 'red');
//        $this->repo->updateMeta($p2->ID, 'color', 'blue');
//
//        $this->assertEquals('red', $this->repo->getMeta($p1->ID, 'color'));
//        $this->assertEquals('blue', $this->repo->getMeta($p2->ID, 'color'));
//
//
//        $results = $this->repo->query([
//            'post_type' => 'post',
//            'meta_query' => [
//                ['key' => 'color', 'value' => 'red', 'compare' => '=']
//            ]
//        ]);
//
//        $this->assertCount(1, $results);
//        $this->assertEquals('A', $results->first()->post_title);
//    }

    public function test_tax_query_by_slug()
    {
        $post = $this->repo->insert(['post_title' => 'Taxed', 'post_type'=>'post']);

        $term = \RezaQsr\Wp2Laravel\Models\Term::create(['name'=>'News','slug'=>'news']);
        $tt = \RezaQsr\Wp2Laravel\Models\TermTaxonomy::create(['term_id'=>$term->term_id,'taxonomy'=>'category']);
        \RezaQsr\Wp2Laravel\Models\TermRelationship::create(['object_id'=>$post->ID,'term_taxonomy_id'=>$tt->term_taxonomy_id]);

        $results = $this->repo->query([
            'post_type' => 'post',
            'tax_query' => [
                [
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => ['news']
                ]
            ]
        ]);

        $this->assertCount(1, $results);
        $this->assertEquals('Taxed', $results->first()->post_title);
    }
}
