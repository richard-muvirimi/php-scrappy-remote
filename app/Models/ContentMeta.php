<?php

/**
 * Content Meta Model
 */

namespace App\Models;

/**
 * Content meta model class
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
class ContentMeta extends BaseModel
{
	protected $table         = 'content_meta';
	protected $allowedFields = [
		'content',
		'key',
		'value',
	];
	protected $returnType    = 'App\Entities\ContentMeta';
}
