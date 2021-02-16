<?php

/**
 * @Author: Dell
 * @Date:   2019-08-01 15:48:18
 * @Last Modified by:   Dell
 * @Last Modified time: 2019-08-01 15:48:51
 */
class EloquentCategoryTranslationRepository  extends EloquentBaseRepository implements CategoryTranslationRepository
{
	public function test(){
		return 'CategoryTranslation';
	}
}