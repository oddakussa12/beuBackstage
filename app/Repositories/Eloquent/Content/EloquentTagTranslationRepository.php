<?php

/**
 * @Author: Dell
 * @Date:   2019-08-06 16:44:20
 * @Last Modified by:   Dell
 * @Last Modified time: 2019-08-06 16:45:44
 */
class EloquentTagTranslationRepository  extends EloquentBaseRepository implements TagTranslationRepository
{
	public function test(){
		return 'TagTranslation';
	}
}