<?php

/**
 * User Meta Model
 */

namespace App\Models;

/**
 * User meta model class
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
class UserMeta extends BaseModel
{
	protected $table         = 'user_meta';
	protected $allowedFields = [
		'user',
		'key',
		'value',
	];
	protected $returnType    = 'App\Entities\UserMeta';
}
