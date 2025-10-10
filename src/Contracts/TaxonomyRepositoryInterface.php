<?php

namespace RezaQsr\Wp2Laravel\Contracts;

 interface TaxonomyRepositoryInterface
 {
     public function getTaxonomy(string $taxonomy);
     public function getTaxonomies(array $args = []);

 }