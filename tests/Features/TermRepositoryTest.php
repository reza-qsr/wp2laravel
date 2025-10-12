<?php

namespace RezaQsr\Wp2Laravel\Tests\Features;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use RezaQsr\Wp2Laravel\Tests\TestCase;
use RezaQsr\Wp2Laravel\Repositories\DBTermRepository;
use RezaQsr\Wp2Laravel\Models\{Term, TermTaxonomy, TermRelationship};

class TermRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    protected DBTermRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new DBTermRepository();
    }

    /** @test */
    public function it_can_insert_a_new_term()
    {
        DB::beginTransaction();
        $res = $this->repo->insert('mockup', 'category', [
            'description' => 'test description',
        ]);

        $this->assertInstanceOf(Term::class, $res->term);
        $this->assertInstanceOf(TermTaxonomy::class, $res->taxonomy);

        $this->assertEquals('mockup', $res->term->name);
        $this->assertEquals('category', $res->taxonomy->taxonomy);
        DB::Rollback();
    }

    /** @test */
    public function it_prevents_duplicate_slug()
    {
        DB::beginTransaction();
        $first = $this->repo->insert('mockup', 'category');
        $second = $this->repo->insert('mockup', 'category');

        $this->assertNotEquals($first->term->slug, $second->term->slug);
        $this->assertStringContainsString('-2', $second->term->slug);
        DB::Rollback();
    }

    /** @test */
    public function it_can_update_an_existing_term()
    {
        DB::beginTransaction();
        $term = Term::create(['name' => 'old name', 'slug' => 'old-slug']);
        TermTaxonomy::create(['term_id' => $term->term_id, 'taxonomy' => 'category']);

        $updated = $this->repo->update($term->term_id, 'category', [
            'name' => 'new name',
            'description' => 'new description',
        ]);

        $this->assertEquals('new name', $updated->term->name);
        $this->assertEquals('new description', $updated->taxonomy->description);
        DB::Rollback();
    }

    /** @test */
    public function it_can_delete_a_term_and_its_relationships()
    {
        DB::beginTransaction();
        $res = $this->repo->insert('delete term', 'category');
        $deleted = $this->repo->delete($res->term->term_id, 'category');
        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('wp_terms', ['term_id' => $res->term->term_id]);
        DB::Rollback();
    }

    /** @test */
    public function it_returns_empty_when_term_not_found()
    {
        $res = $this->repo->getBy('id', 999999, 'category');
        $this->assertTrue($res->isEmpty());
    }

    /** @test */
    public function it_can_get_terms_by_taxonomy_or_slug()
    {
        DB::beginTransaction();
        $t1 = $this->repo->insert('test1', 'category');
        $t2 = $this->repo->insert('test2', 'tag');

        $byTax = $this->repo->get(['taxonomy' => 'category']);
        $this->assertTrue($byTax->contains('name', 'test1'));
        $this->assertFalse($byTax->contains('name', 'test2'));

        $bySlug = $this->repo->get(['slug' => $t1->term->slug]);
        $this->assertTrue($bySlug->contains('name', 'test1'));
        DB::Rollback();
    }

    /** @test */
    public function it_can_attach_and_get_post_terms()
    {
        DB::beginTransaction();
        $term = $this->repo->insert('mockup-post', 'category');
        $postId = 123;
        $this->repo->setPostTerms($postId, [$term->term->term_id], 'category');
        $terms = $this->repo->getPostTerms($postId, 'category');

        $this->assertTrue($terms->contains('term_id', $term->term->term_id));
        DB::Rollback();
    }
}
