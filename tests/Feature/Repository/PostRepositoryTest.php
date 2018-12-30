<?php

namespace Tests\Feature\Repository;

use App\Model\Category;
use App\Model\Post;
use App\Model\Role;
use App\Model\User;
use App\Repository\PostRepository;
use App\Status;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostRepositoryTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * @test
	 */
	public function correct_status_for_add_member_post()
	{
		factory(Category::class)->create();
		Role::create(['name' => 'member', 'slug' => 'member', 'description' => 'member']);
		factory(User::class)->create(['role_id' => Role::first()]);
		factory(User::class)->create();
		$postRepository = new PostRepository(new Post());
		$data = [
			'name' => 'foo',
			'slug' => 'foo',
			'content' => 'foobar',
			'category_id' => Category::first(),
			'user_id' => User::first(),
			'status' => 'pending',
			'validated' => true
		];
		$post = $postRepository->saveUserPost($data);

		$this->assertEquals('pending', $post->status);
	}

	/** @test */
	public function status_accepted_if_is_admin_role()
	{
		factory(Category::class)->create();
		Role::create(['name' => 'admin', 'slug' => 'admin', 'description' => 'admin']);
		factory(User::class)->create(['role_id' => Role::first()]);
		factory(User::class)->create();
		$postRepository = new PostRepository(new Post());
		$data = [
			'name' => 'foo',
			'slug' => 'foo',
			'content' => 'foobar',
			'category_id' => Category::first(),
			'user_id' => User::first(),
			'status' => 'pending'
		];
		$post = $postRepository->saveUserPost($data, true);

		$this->assertEquals('accepted', $post->status);
	}

}
