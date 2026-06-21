<?php

namespace Tests;

/**
 * Trait WithoutRefresh
 *
 * Alias untuk DatabaseTransactions bawaan Laravel.
 * Setiap test dibungkus dalam database transaction yang di-rollback setelah selesai.
 *
 * @see \Illuminate\Foundation\Testing\DatabaseTransactions
 */
trait WithoutRefresh
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;
}
