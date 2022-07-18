<?php

/**
 * Content helper
 */

use App\Entities\Content;
use App\Entities\ContentMeta;
use App\Models\Content as ModelsContent;
use App\Models\ContentMeta as ModelsContentMeta;
use CodeIgniter\Database\Exceptions\DataException;
use Config\Services;

/**
 * Insert user content
 *
 * @param string         $type    Type.
 * @param string|integer $content Content.
 * @param integer        $user    Content User.
 * @param integer        $parent  Content Parent.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return BaseResult|false|integer|object|string
 */
function insert_content(string $type, $content = '', int $user = DEFAULT_ID, int $parent = DEFAULT_PARENT_ID)
{
	if (empty($type))
	{
		throw new DataException('Type field is required');
	}

	if ($user == DEFAULT_ID)
	{
		helper('user');

		$user = current_user_id();
	}

	$content = new Content(compact('user', 'type', 'content', 'parent'));

	$model = new ModelsContent();
	return  $model->insert($content);
}

/**
 * Update user content
 *
 * @param string         $type    Type.
 * @param string|integer $content Content.
 * @param integer        $user    Content User Id.
 * @param integer        $id      Content Id.
 * @param integer        $parent  Content Parent.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return integer
 */
function update_content(string $type, $content = '', int $user = DEFAULT_ID, int $id = DEFAULT_ID, int $parent = DEFAULT_PARENT_ID):int
{
	if (empty($type))
	{
		throw new DataException('Type field is required');
	}

	$model = new ModelsContent();

	if ($user == DEFAULT_ID)
	{
		$user = current_user_id();
	}

	if ($id != DEFAULT_ID)
	{
		$model->where(compact('id'));
		$contentItem = $model->first();
	}
	else
	{
		$contentItem = new Content(compact('id'));
	}

	$contentItem->fill(compact('user', 'type', 'content', 'parent'));

	try
	{
		$model->save($contentItem);

		// Update user for children.
		if ($contentItem->id != DEFAULT_ID)
		{
			$model->where('parent', $contentItem->id);
			$model->set(compact('user'));
			$model->update();
		}
	}
	catch (DataException $e)
	{
	}finally{

		if (isset($contentItem->id) && $id != DEFAULT_ID)
		{
			return $contentItem->id;
		}

		return $model->getInsertID();
	}
}

/**
 * Delete user content
 *
 * @param string  $type   Type.
 * @param integer $id     Content Id.
 * @param integer $user   Content User.
 * @param integer $parent Content Parent.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return mixed|boolean
 */
function delete_content(string $type, int $id = DEFAULT_ID, int $user = DEFAULT_ID, int $parent = DEFAULT_PARENT_ID)
{
	if (empty($type))
	{
		throw new DataException('Type field is required');
	}

	$model = new ModelsContent();

	if ($user == DEFAULT_ID)
	{
		helper('user');
		$user = current_user_id();
	}

	if ($id != DEFAULT_ID)
	{
		$model->where(compact('id'));
	}

	if ($parent !== DEFAULT_PARENT_ID)
	{
		$model->where(compact('parent'));
	}

	return $model->delete();
}

/**
 * Insert user content meta
 *
 * @param integer        $content Content.
 * @param string         $key     Meta key.
 * @param string|integer $value   Meta Value.
 * @param boolean        $empty   Insert even when empty.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return BaseResult|false|integer|object|string
 */
function insert_content_meta(int $content, string $key, $value = '', bool $empty = false)
{
	if (empty($content) || empty($key))
	{
		throw new DataException('Key and content field is required');
	}

	if (! empty($value) || $empty)
	{
		$meta = new ContentMeta(compact('content', 'key', 'value'));

		$model = new ModelsContentMeta();
		return  $model->insert($meta);
	}
	else
	{
		return  delete_content_meta($content, $key);
	}
}

/**
 * Update user content meta
 *
 * @param integer        $content  Content.
 * @param string         $key      Meta Key.
 * @param string|integer $value    Meta Value.
 * @param string|integer $oldValue Meta Old Value.
 * @param integer        $id       Meta Id.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return integer
 */
function update_content_meta(int $content, string $key, $value = '', $oldValue = '', int $id = DEFAULT_ID):int
{
	if (empty($content) || empty($key))
	{
		throw new DataException('Key and content field is required');
	}

	$model = new ModelsContentMeta();
	$model->where(compact('content', 'key'));

	if (! empty($oldValue))
	{
		$model->where('value', $oldValue);
	}

	if ($id != DEFAULT_ID)
	{
		$model->where('id', $id);
	}

	if (! empty($value))
	{
		$meta = $model->first() ?? new ContentMeta(compact('content', 'key', 'id'));
		$meta->fill(compact('value'));

		try
		{
			$model->save($meta);
		}
		catch (DataException $e)
		{
		}finally{
			if (isset($meta->id)  && $id != DEFAULT_ID)
			{
				return $meta->id;
			}

			return $model->getInsertID();
		}
	}
	else
	{
		$meta = $model->first();
		delete_content_meta($content, $key, $oldValue);

		return $meta->id ?? 0;
	}
}

/**
 * Delete user content meta
 *
 * @param integer        $content Content.
 * @param string         $key     Meta key.
 * @param string|integer $value   Meta value.
 * @param integer        $metaId  Meta Id.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return mixed|boolean
 */
function delete_content_meta(int $content, string $key, $value = '', int $metaId = DEFAULT_ID)
{
	if (empty($content) || empty($key))
	{
		throw new DataException('Key and content field is required');
	}

	$model = new ModelsContentMeta();
	$model->where(compact('content', 'key'));

	if ($metaId != DEFAULT_ID)
	{
		$model->where('id', $metaId);
	}

	if (! empty($value))
	{
		$model->where(compact('value'));
	}

	return $model->delete();
}

/**
 * Get content meta
 *
 * @param string         $key     Meta Key.
 * @param integer        $content Content.
 * @param boolean        $first   All or first value.
 * @param string|integer $value   Meta value.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return mixed
 */
function get_content_meta(string $key, int $content = DEFAULT_ID, bool $first = true, $value = '')
{
	if (empty($key))
	{
		throw new DataException('Key field is required');
	}

	$model = new ModelsContentMeta();
	$model->where(compact('key'));

	if (! empty($value))
	{
		$model->where(compact('value'));
	}

	if ($content != DEFAULT_ID)
	{
		$model->where(compact('content'));
	}

	if ($first)
	{
		return $model->first() ?? false;
	}
	else
	{
		return $model->findAll() ?? [];
	}
}

/**
 * Get multiple content meta
 *
 * @param array           $keys    Meta keys.
 * @param Content|integer $content Content.
 * @param boolean         $tail    Add additional item filled with blanks.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return array
 */
function get_multiple_content_meta(array $keys, $content = DEFAULT_ID, bool $tail = false):array
{
	if (empty($keys))
	{
		throw new DataException('Key field is required');
	}

	//1 => one, 2 => two, 3 => three
	$rows = [];
	if ((is_numeric($content) && $content !== DEFAULT_ID) || (is_object_content($content) && $content->id))
	{
		/**
		 * Fetch all available content
		 */
		foreach ($keys as $key)
		{
			$rows[$key] = get_content_meta($key, get_content_by_id($content)->id, false);
		}

		/**
		 * Work with largest number of elements
		 */
		$count = 0;
		foreach ($keys as $key)
		{
			$data = $rows[$key] ?? [];

			$count = max(count($data), $count);
		}

		$data = [];
		foreach ($keys as $field)
		{
			$tempData = $rows[$field] ?? [];

			$values = [];
			foreach ($tempData as $meta)
			{
				$values[] = [
					$field . '-id' => strval($meta->id),
					$field         => $meta->value,
				];
			}

			/**
			 * Fill remaining gap with blanks
			 */
			$blank = [
				$field . '-id' => null,
				$field         => '',
			];

			$tempData = array_merge($values, array_fill(0, max($count - count($tempData), 0), $blank));

			/**
			 * Combine everything.
			 */
			for ($i = 0; $i < count($tempData); $i++)
			{
				$data[$i][$field . '-id'] = $tempData[$i][$field . '-id'];
				$data[$i][$field]         = $tempData[$i][$field];
			}
		}

		$rows = $data;
	}

	if ($tail)
	{
		$row = [];
		foreach ($keys as $key)
		{
			$row[$key . '-id'] = null;
			$row[$key]         = '';
		}

		$rows[] = $row;
	}

	return $rows;
}

/**
 * Save multiple content meta
 *
 * @param Content|integer $content Content.
 * @param array           $fields  Fields to save.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return void
 */
function save_multiple_content_meta($content, array $fields)
{
	/**
	 * Split content based on existing content so that we can take the appropriate action i.e update, insert or delete
	 */

	$request = Services::request();

	/**
	 * Work with largest number of elements
	 */
	$count = 0;
	foreach ($fields as $field)
	{
		$post = $request->getPostGet($field) ?? [];

		$count = max(count($post), $count);
	}

	/**
	 * Covert and combine passed content
	 */
	$data = [];
	foreach ($fields as $field)
	{
		$tempData = $request->getPostGet($field) ?? [];

		$values = [];
		foreach ($tempData as $key => $value)
		{
			$values[] = [
				$field . '-id' => strval($key),
				$field         => $value ?: '',
			];
		}

		/**
		 * Fill remaining gap with blanks
		 */
		$blank = [
			$field . '-id' => null,
			$field         => '',
		];

		$tempData = array_merge($values, array_fill(0, max($count - count($tempData), 0), $blank));

		/**
		 * Combine everything.
		 */
		for ($i = 0; $i < count($tempData); $i++)
		{
			$data[$i][$field . '-id'] = $tempData[$i][$field . '-id'];
			$data[$i][$field]         = $tempData[$i][$field];
		}
	}

	/**
	 * Filter empty
	 */
	$data = array_filter($data, function ($item) use ($fields) {
		$valid = false;

		/**
		 * Don't filter out off if it's the only field.
		 */
		$check = (count($fields) > 1) ? ['', 'off', null] : ['', null];

		foreach ($fields as $field)
		{
			$valid |= ! in_array($item[$field], $check);
		}
		return $valid;
	});

	/**
	 * Get saved items for below calculations
	 */
	$saved = get_multiple_content_meta($fields, $content);

	$compareId = function ($item1, $item2) use ($fields) {
		/**
		 * Filter fields to make comparing easier
		 */
		$compareItem1 = [];
		$compareItem2 = [];
		foreach ($fields as $field)
		{
			$compareItem1[$field] = $item1[$field . '-id'] ?: null;
			$compareItem2[$field] = $item2[$field . '-id'] ?: null;
		}

		return $compareItem1 <=> $compareItem2;
	};

	$compareValue = function ($item1, $item2) {
		return $item1 <=> $item2;
	};

	/**
	 * Calculate items to update
	 */
	$update       = array_uintersect($data, $saved, $compareId);
	$dataToUpdate = array_udiff($update, $saved, $compareValue);

	foreach ($dataToUpdate as $item)
	{
		foreach ($fields as $field)
		{
			if ($field . '-id')
			{
				update_content_meta($content, $field, $item[$field], '', $item[$field . '-id']);
			}
			else
			{
				insert_content_meta($content, $field, $item[$field], true);
			}
		}
	}

	/**
	 * Calculate items to insert
	 */
	$dataToSave = array_udiff($data, $update, $compareId);

	foreach ($dataToSave as $item)
	{
		foreach ($fields as $field)
		{
			insert_content_meta($content, $field, $item[$field], true);
		}
	}

	/**
	 * Calculate items to be deleted
	 */
	$dataToDelete = array_udiff($saved, $data, $compareId);

	foreach ($dataToDelete as $item)
	{
		foreach ($fields as $field)
		{
			if ($field . '-id')
			{
				delete_content_meta($content, $field, '', $item[$field . '-id']);
			}
		}
	}
}

/**
 * Get user content by id
 *
 * @param Content|integer $id Content Id.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return Content|false
 */
function get_content_by_id($id)
{
	if (is_object_content($id))
	{
		return $id;
	}

	$model = new ModelsContent();
	return $model->find($id) ?: false;
}

/**
 * Get user content
 *
 * @param string  $type   Content type.
 * @param integer $user   Content User.
 * @param integer $parent Content Parent.
 * @param integer $limit  Limit.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return array
 */
function get_user_content(string $type, int $user = DEFAULT_ID, int $parent = DEFAULT_PARENT_ID, int $limit = 0)
{
	if (empty($type))
	{
		throw new DataException('Type field is required');
	}

	if ($user == DEFAULT_ID)
	{
		$user = current_user_id();
	}

	$user = get_user($user);

	$model = new ModelsContent();
	$model->where('user', $user->id);

	if (! empty($type))
	{
		$model->where(compact('type'));
	}

	if ($parent !== DEFAULT_PARENT_ID)
	{
		$model->where(compact('parent'));
	}

	$model->orderBy('updated_at', 'DESC');

	return $model->findAll($limit) ?? [];
}

/**
 * Check whether an item is a content object
 *
 * @param object|string $item Item.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return boolean
 */
function is_object_content($item):bool
{
	return is_a($item, '\App\Entities\Content');
}

/**
 * Check whether an item is a content meta object
 *
 * @param object|string $item Item.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return boolean
 */
function is_object_content_meta($item):bool
{
	return is_a($item, '\App\Entities\ContentMeta');
}

/**
 * Get filter prepared content model
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return ModelsContent
 */
function get_prepared_content_model():ModelsContent
{
	$request = Services::request();

	$model = new ModelsContent();

	//Search
	$search = $request->getPostGet('search');
	if ($search != null)
	{
		$model->groupStart();

		$model->join('content_meta', 'content.id = content_meta.content', 'left');

		$model->like('content.content', $search);
		$model->orLike('content_meta.value', $search);

		$model->groupEnd();
	}

	$model->select('content.*');

	// Sort
	$sort = $request->getPostGet('sort');
	if ($sort != null)
	{
		$model->orderBy('content', strtoupper($sort));
	}

	// User
	$user = $request->getPostGet('user');
	if ($user)
	{
		$model->groupStart();

		$model->where(compact('user'));

		$model->groupEnd();
	}

	return $model;
}
