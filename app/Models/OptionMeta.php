<?php

/**
 * Option Meta Model
 */

namespace App\Models;

/**
 * Option meta model class
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
class OptionMeta extends BaseModel
{
	protected $table         = 'option_meta';
	protected $allowedFields = [
		'option',
		'key',
		'value',
	];
	protected $returnType    = 'App\Entities\OptionMeta';

}
