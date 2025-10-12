<?php

namespace RezaQsr\Wp2Laravel\Tests\Features;

use Illuminate\Support\Facades\DB;
use RezaQsr\Wp2Laravel\Models\Term;
use RezaQsr\Wp2Laravel\Models\TermTaxonomy;
use RezaQsr\Wp2Laravel\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use RezaQsr\Wp2Laravel\Repositories\DBTermRepository;

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
        $termName = 'mockup';
        $taxonomy = 'category';

        $res = $this->repo->insert($termName, $taxonomy, [
            'description' => 'test description',
        ]);
        $this->assertInstanceOf(Term::class, $res->term);
        $this->assertInstanceOf(TermTaxonomy::class, $res->taxonomy);
        $this->assertEquals($termName, $res->term->name);
        DB::Rollback();

    }

    /** @test */
    public function it_can_update_an_existing_term()
    {
        DB::beginTransaction();
        $term = Term::create(['name' => 'old name', 'slug' => 'old-slug']);
        TermTaxonomy::create([
            'term_id' => $term->term_id,
            'taxonomy' => 'category',
        ]);

        $updated = $this->repo->update($term->term_id, 'category', [
            'name' => 'new name',
            'description' => 'new description',
        ]);

        $this->assertEquals('new name', $updated->term->name);
        DB::Rollback();
    }

    /** @test */
    public function it_can_delete_a_term_and_its_relationships()
    {
        $res = $this->repo->insert('delete term', 'category');
        $delete = $this->repo->delete($res->term->term_id, 'category');
       $this->assertTrue($delete);
    }

//    /** @test */
//    public function get_terms_should_return_valid_collection()
//    {
//        [$term, $tax] = $this->repo->insert('تست لیست', 'category');
//        $terms = $this->repo->getTerms(['taxonomy' => 'category']);
//
//        $this->assertTrue($terms->contains(fn($t) => $t->name === 'تست لیست'));
//    }
}
