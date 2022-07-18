<?php

/**
 * Option Helper
 */

use App\Entities\Option;
use App\Entities\OptionMeta;
use App\Models\Option as ModelsOption;
use App\Models\OptionMeta as ModelsOptionMeta;
use CodeIgniter\Database\Exceptions\DataException;

/**
 * Get option
 *
 * @param string  $key   Option key.
 * @param boolean $first First value.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return mixed
 */
function get_option(string $key, bool $first = true)
{
	if (empty($key))
	{
		throw new DataException('Key field is required');
	}

	$model = new ModelsOption();
	$model->where(compact('key'));

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
 * Get option by id
 *
 * @param Option|integer $id Option.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return Option|false
 */
function get_option_by_id($id)
{
	if (is_object_option($id))
	{
		return $id;
	}
	else
	{
		$model = new ModelsOption();
		return $model->find($id) ?: false;
	}
}

/**
 * Insert option
 *
 * @param string         $key   Option key.
 * @param string|integer $value Option value.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return BaseResult|false|integer|object|string
 */
function insert_option(string $key, $value = '')
{
	if (empty($key))
	{
		throw new DataException('Key field is required');
	}

	if (empty($value))
	{
		return delete_option($key);
	}
	else
	{
		$model = new ModelsOption();

		$option = new Option(compact('key', 'value'));
		return  $model->insert($option);
	}
}

/**
 * Update option
 *
 * @param string         $key      Option Key.
 * @param string|integer $value    Option Value.
 * @param string|integer $oldValue Old option value.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return boolean|integer
 */
function update_option(string $key, $value = '', $oldValue = '')
{
	if (empty($key))
	{
		throw new DataException('Key field is required');
	}

	$model = new ModelsOption();
	$model->where(compact('key'));

	if (! empty($oldValue))
	{
		$model->where('value', $oldValue);
	}

	$option = $model->first() ?? new Option(compact('key'));
	$option->fill(compact('value'));

	try
	{
		if (! empty($value))
		{
			$model->save($option);
		}
		else
		{
			return delete_option($key);
		}
	}
	catch (DataException $e)
	{
	}finally{
		if (isset($option->id))
		{
			return $option->id;
		}

		return $model->getInsertID();
	}
}

/**
 * Delete option
 *
 * @param string $key Option key.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return mixed|boolean
 */
function delete_option(string $key)
{
	if (empty($key))
	{
		throw new DataException('Key field is required');
	}

	$model = new ModelsOption();
	$model->where(compact('key'));

	return $model->delete();
}

/**
 * Insert option meta
 *
 * @param integer        $option Option.
 * @param string         $key    Option Key
 * @param string|integer $value  Option Value.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return BaseResult|false|integer|object|string
 */
function insert_option_meta(int $option, string $key, $value = '')
{
	if (empty($option) || empty($key))
	{
		throw new DataException('Key and option field is required');
	}

	if (empty($value))
	{
		return  delete_option_meta($option, $key);
	}
	else
	{
		$meta = new OptionMeta(compact('option', 'key', 'value'));

		$model = new ModelsOptionMeta();
		return  $model->insert($meta);
	}
}

/**
 * Update option meta
 *
 * @param integer        $option   Option.
 * @param string         $key      Meta key.
 * @param string|integer $value    Meta Value.
 * @param string|integer $oldValue Old meta value.
 * @param integer        $id       Meta Id.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return integer
 */
function update_option_meta(int $option, string $key, $value = '', $oldValue = '', int $id = DEFAULT_ID):int
{
	if (empty($option) || empty($key))
	{
		throw new DataException('Key and option field is required');
	}

	$model = new ModelsOptionMeta();
	$model->where(compact('option', 'key'));

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
		$meta = $model->first() ?? new OptionMeta(compact('option', 'key', 'id'));
		$meta->fill(compact('value'));

		try
		{
			$model->save($meta);
		}
		catch (DataException $e)
		{
		}finally{
			if (isset($meta->id) && $id != DEFAULT_ID)
			{
				return $meta->id;
			}

			return $model->getInsertID();
		}
	}
	else
	{
		$meta = $model->first();
		delete_option_meta($option, $key, $oldValue);

		return $meta->id ?? 0;
	}
}

/**
 * Delete option meta
 *
 * @param integer        $option Option.
 * @param string         $key    Meta key.
 * @param string|integer $value  Meta value.
 * @param integer        $metaId Meta Id.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return mixed|boolean
 */
function delete_option_meta(int $option, string $key, $value = '', int $metaId = DEFAULT_ID)
{
	if (empty($option) || empty($key))
	{
		throw new DataException('Key and option field is required');
	}

	$model = new ModelsOptionMeta();
	$model->where(compact('option', 'key'));

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
 * Get option meta
 *
 * @param string         $key    Meta key.
 * @param integer        $option Option.
 * @param boolean        $first  First Match.
 * @param string|integer $value  Meta Value.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return mixed
 */
function get_option_meta(string $key, int $option = DEFAULT_ID, bool $first = true, $value = '')
{
	if (empty($key))
	{
		throw new DataException('Key field is required');
	}

	$model = new ModelsOptionMeta();
	$model->where(compact('key'));

	if (! empty($value))
	{
		$model->where(compact('value'));
	}

	if ($option != DEFAULT_ID)
	{
		$model->where(compact('option'));
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
 * Check whether an item is a option object
 *
 * @param object|string $item Item.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return boolean
 */
function is_object_option($item):bool
{
	return is_a($item, '\App\Entities\Option');
}

/**
 * Check whether an item is a option meta object
 *
 * @param object|string $item Item.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return boolean
 */
function is_object_option_meta($item):bool
{
	return is_a($item, '\App\Entities\OptionMeta');
}
