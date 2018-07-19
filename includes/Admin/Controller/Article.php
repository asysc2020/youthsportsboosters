<?php
namespace Redaxscript\Admin\Controller;

use Redaxscript\Admin;
use Redaxscript\Filter;
use Redaxscript\Validator;

/**
 * children class to process the admin article request
 *
 * @since 4.0.0
 *
 * @package Redaxscript
 * @category Controller
 * @author Henry Ruhs
 */

class Article extends ControllerAbstract
{
	/**
	 * process the class
	 *
	 * @since 4.0.0
	 *
	 * @param string $action action to process
	 *
	 * @return string
	 */

	public function process(string $action = null) : string
	{
		$postArray = $this->_normalizePost($this->_sanitizePost());
		$validateArray = $this->_validatePost($postArray);
		$myUser = $this->_registry->get('myUser');
		$now = $this->_registry->get('now');

		/* validate post */

		if ($validateArray)
		{
			return $this->_error(
			[
				'route' => $postArray['id'] ? 'admin/edit/articles/' . $postArray['id'] : 'admin/new/articles',
				'message' => $validateArray
			]);
		}

		/* handle create */

		if ($action === 'create')
		{
			$createArray =
			[
				'title' => $postArray['title'],
				'alias' => $postArray['alias'],
				'author' => $myUser,
				'description' => $postArray['description'],
				'keywords' => $postArray['keywords'],
				'robots' => $postArray['robots'],
				'text' => $postArray['text'],
				'language' => $postArray['language'],
				'template' => $postArray['template'],
				'sibling' => $postArray['sibling'],
				'category' => $postArray['category'],
				'headline' => $postArray['headline'],
				'byline' => $postArray['byline'],
				'comments' => $postArray['comments'],
				'status' => $postArray['date'] > $now ? 2 : $postArray['status'],
				'rank' => $postArray['rank'],
				'access' => $postArray['access'],
				'date' => $postArray['date'] ? $postArray['date'] : $now
			];
			if ($this->_create($createArray))
			{
				return $this->_success(
				[
					'route' => 'admin/view/articles#' . $postArray['alias'],
					'timeout' => 2,
					'message' => $this->_language->get('operation_completed')
				]);
			}
		}

		/* handle update */

		if ($action === 'update')
		{
			$updateArray =
			[
				'title' => $postArray['title'],
				'alias' => $postArray['alias'],
				'author' => $myUser,
				'description' => $postArray['description'],
				'keywords' => $postArray['keywords'],
				'robots' => $postArray['robots'],
				'text' => $postArray['text'],
				'language' => $postArray['language'],
				'template' => $postArray['template'],
				'sibling' => $postArray['sibling'],
				'category' => $postArray['category'],
				'headline' => $postArray['headline'],
				'byline' => $postArray['byline'],
				'comments' => $postArray['comments'],
				'status' => $postArray['date'] > $now ? 2 : $postArray['status'],
				'rank' => $postArray['rank'],
				'access' => $postArray['access'],
				'date' => $postArray['date'] ? $postArray['date'] : $now
			];
			if ($this->_update($postArray['id'], $updateArray))
			{
				return $this->_success(
				[
					'route' => 'admin/view/articles#' . $postArray['alias'],
					'timeout' => 2,
					'message' => $this->_language->get('operation_completed')
				]);
			}
		}

		/* handle error */

		return $this->_error(
		[
			'route' => $postArray['id'] ? 'admin/edit/articles/' . $postArray['id'] : 'admin/new/articles',
			'message' => $this->_language->get('something_wrong')
		]);
	}

	/**
	 * sanitize the post
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */

	protected function _sanitizePost() : array
	{
		$aliasFilter = new Filter\Alias();
		$specialFilter = new Filter\Special();
		$htmlFilter = new Filter\Html();

		/* sanitize post */

		return
		[
			'id' => $specialFilter->sanitize($this->_request->getPost('id')),
			'title' => $this->_request->getPost('title'),
			'alias' => $aliasFilter->sanitize($this->_request->getPost('alias')),
			'description' => $this->_request->getPost('description'),
			'keywords' => $this->_request->getPost('keywords'),
			'robots' => $specialFilter->sanitize($this->_request->getPost('robots')),
			'text' => $htmlFilter->sanitize($this->_request->getPost('text'), $this->_registry->get('filter')),
			'language' => $specialFilter->sanitize($this->_request->getPost('language')),
			'template' => $specialFilter->sanitize($this->_request->getPost('template')),
			'sibling' => $specialFilter->sanitize($this->_request->getPost('sibling')),
			'category' => $specialFilter->sanitize($this->_request->getPost('category')),
			'headline' => $specialFilter->sanitize($this->_request->getPost('headline')),
			'byline' => $specialFilter->sanitize($this->_request->getPost('byline')),
			'comments' => $specialFilter->sanitize($this->_request->getPost('comments')),
			'status' => $specialFilter->sanitize($this->_request->getPost('status')),
			'rank' => $specialFilter->sanitize($this->_request->getPost('rank')),
			'access' => $specialFilter->sanitize($this->_request->getPost('access')),
			'date' => $this->_request->getPost('date')
		];
	}

	/**
	 * validate the post
	 *
	 * @since 4.0.0
	 *
	 * @param array $postArray array of the post
	 *
	 * @return array
	 */

	protected function _validatePost(array $postArray = []) : array
	{
		$aliasValidator = new Validator\Alias();
		$categoryModel = new Admin\Model\Category();
		$articleModel = new Admin\Model\Article();
		$validateArray = [];

		/* validate post */

		if (!$postArray['title'])
		{
			$validateArray[] = $this->_language->get('title_empty');
		}
		if (!$postArray['alias'])
		{
			$validateArray[] = $this->_language->get('alias_empty');
		}
		else if ($aliasValidator->validate($postArray['alias'], 'general') || $aliasValidator->validate($postArray['alias'], 'system'))
		{
			$validateArray[] = $this->_language->get('alias_incorrect');
		}
		else if ($categoryModel->getByAlias($postArray['alias'])->id || $articleModel->getByAlias($postArray['alias'])->id !== $articleModel->getById($postArray['id'])->id)
		{
			$validateArray[] = $this->_language->get('alias_exists');
		}
		if (!$postArray['text'])
		{
			$validateArray[] = $this->_language->get('text_empty');
		}
		return $validateArray;
	}

	/**
	 * create the article
	 *
	 * @since 4.0.0
	 *
	 * @param array $createArray array of the create
	 *
	 * @return bool
	 */

	protected function _create(array $createArray = []) : bool
	{
		$articleModel = new Admin\Model\Article();
		return $articleModel->createByArray($createArray);
	}

	/**
	 * update the article
	 *
	 * @since 4.0.0
	 *
	 * @param int $articleId identifier of the article
	 * @param array $updateArray
	 *
	 * @return bool
	 */

	public function _update(int $articleId = null, array $updateArray = []) : bool
	{
		$articleModel = new Admin\Model\Article();
		return $articleModel->updateByIdAndArray($articleId, $updateArray);
	}
}
